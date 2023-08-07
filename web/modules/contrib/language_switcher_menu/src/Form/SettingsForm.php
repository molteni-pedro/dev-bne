<?php

namespace Drupal\language_switcher_menu\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Menu\MenuLinkManagerInterface;
use Drupal\Core\Menu\MenuParentFormSelectorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to administer settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The menu link plugin manager.
   *
   * @var \Drupal\Core\Menu\MenuLinkManagerInterface
   */
  protected $menuLinkManager;

  /**
   * The menu parent form selector.
   *
   * @var \Drupal\Core\Menu\MenuParentFormSelectorInterface
   */
  protected $menuParentFormSelector;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Menu\MenuLinkManagerInterface $menu_link_manager
   *   The menu link plugin manager.
   * @param \Drupal\Core\Menu\MenuParentFormSelectorInterface $menu_parent_form_selector
   *   The menu parent form selector.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, LanguageManagerInterface $language_manager, MenuLinkManagerInterface $menu_link_manager, MenuParentFormSelectorInterface $menu_parent_form_selector) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
    $this->languageManager = $language_manager;
    $this->menuLinkManager = $menu_link_manager;
    $this->menuParentFormSelector = $menu_parent_form_selector;
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-return self
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('plugin.manager.menu.link'),
      $container->get('menu.parent_form_selector')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-return array<string>
   */
  protected function getEditableConfigNames() {
    return [
      'language_switcher_menu.settings',
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-return string
   */
  public function getFormId() {
    return 'language_switcher_menu_settings_form';
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-param array<mixed> $form
   * @phpstan-return array<mixed>
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('language_switcher_menu.settings');

    $info = $this->languageManager->getDefinedLanguageTypesInfo();
    $configurable_types = $this->languageManager->getLanguageTypes();
    $options = [];
    foreach ($configurable_types as $type) {
      $options[$type] = $info[$type]['name'];
    }
    $default = $config->get('type') ? $config->get('type') : NULL;
    if (empty($default) && count($configurable_types) === 1) {
      $default = $type ?? NULL;
    }
    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Language type'),
      '#description' => $this->t('Language type to build language switcher links for.'),
      '#options' => $options,
      '#required' => TRUE,
      '#default_value' => $default,
    ];

    $default = $config->get('parent');
    if ($default === NULL) {
      // The module has not been configured, yet. Suggest a sane default.
      $default = 'main:';
      $menus = $this->entityTypeManager->getStorage('menu')->loadMultiple();
      if (!isset($menus['main']) && count($menus) > 0) {
        $default = array_key_first($menus) . ':';
      }
    }
    // If the value is empty, the module has been disabled. We need to set a
    // dummy default for menu parent form selector and reset the value later.
    $disabled = FALSE;
    if (empty($default)) {
      $disabled = TRUE;
      $default = 'main:';
    }
    $form['parent'] = $this->menuParentFormSelector->parentSelectElement($default);
    $form['parent']['#title'] = $this->t('Parent link');
    $form['parent']['#description'] = $this->t('Link to use as a parent link for language switcher links. Select %disabled to disable adding of language switcher links.', [
      '%disabled' => $this->t('Disabled'),
    ]);
    $form['parent']['#required'] = FALSE;
    $form['parent']['#empty_option'] = $this->t('Disabled');
    $form['parent']['#empty_value'] = '';
    if ($disabled) {
      $form['parent']['#default_value'] = '';
    }

    // Our own menu links should not be available for selection.
    $form['parent']['#options'] = array_filter($form['parent']['#options'], function ($key) {
      return strpos($key, ':language_switcher_menu.language_switcher_link:') === FALSE;
    }, ARRAY_FILTER_USE_KEY);

    $form['weight'] = [
      '#type' => 'weight',
      '#title' => $this->t('Weight'),
      '#description' => $this->t('Menu item weight of <em>first</em> language switcher link. Weight of any additional links will be increased by 1.'),
      '#delta' => 100,
      '#default_value' => (int) $config->get('weight'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-param array<mixed> $form
   * @phpstan-return void
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $has_changes = FALSE;
    $config = $this->config('language_switcher_menu.settings');
    foreach (['parent', 'type', 'weight'] as $setting) {
      $setting_old = $config->get($setting);
      $setting_new = $form_state->getValue($setting);
      if ($setting === 'weight') {
        $setting_new = (int) $setting_new;
      }
      if ($setting_old === $setting_new) {
        continue;
      }
      $has_changes = TRUE;
      break;
    }

    $config
      ->set('type', $form_state->getValue('type'))
      ->set('parent', $form_state->getValue('parent'))
      ->set('weight', (int) $form_state->getValue('weight'))
      ->save();

    if ($has_changes) {
      // Rebuild the menu link plugin cache. This will also invalidate cache
      // tags for old and new menu config, which will invalidate menu block
      // caches.
      $this->menuLinkManager->rebuild();
    }

  }

}
