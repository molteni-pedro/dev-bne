<?php

namespace Drupal\language_switcher_menu\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides menu links to switch to another language.
 */
class LanguageSwitcherLink extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new LanguageSwitcherLink instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LanguageManagerInterface $language_manager) {
    $this->configFactory = $config_factory;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('config.factory'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-param mixed $base_plugin_definition
   * @phpstan-return array<string, mixed>
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $links = [];

    if (!$this->languageManager->isMultilingual()) {
      return $links;
    }

    $config = $this->configFactory->get('language_switcher_menu.settings');

    if (empty($config->get('type'))) {
      return $links;
    }
    if (empty($config->get('parent'))) {
      return $links;
    }

    $parent_config = explode(':', $config->get('parent'));
    $menu_name = $parent_config[0];
    $parent = NULL;
    if (!empty($parent_config[1])) {
      unset($parent_config[0]);
      $parent = implode(':', $parent_config);
    }

    $weight = (int) $config->get('weight');
    $type = $config->get('type');

    foreach ($this->languageManager->getLanguages() as $langcode => $language) {
      $links[$langcode] = [
        'title' => $language->getName(),
        'metadata' => [
          'language' => $language,
          'langcode' => $langcode,
          'langtype' => $type,
        ],
        'route_name' => '<current>',
        'route_parameters' => [],
        'menu_name' => $menu_name,
        'parent' => $parent,
        'weight' => $weight,
        'options' => [],
      ] + $base_plugin_definition;
      $weight + 1;
    }

    return $links;
  }

}
