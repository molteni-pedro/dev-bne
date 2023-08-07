<?php

namespace Drupal\language_switcher_menu\Plugin\Menu;

use Drupal\Core\Url;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\Core\Menu\StaticMenuLinkOverridesInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Represents a menu link to switch to a specific language.
 */
class LanguageSwitcherLink extends MenuLinkDefault {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $currentRouteMatch;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Language switch links for active route.
   *
   * NULL, if never initialized. FALSE, if unsuccessfully initialized. An array
   * of language switch links, if successfully initialized.
   *
   * @var array|null|false
   * @phpstan-var array<string,mixed>|null|false
   */
  protected $links = NULL;

  /**
   * Constructs a new LanguageSwitcherLink.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $current_route_match
   *   The current route match.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Menu\StaticMenuLinkOverridesInterface $static_override
   *   The static override storage.
   *
   * @phpstan-param array<mixed> $configuration
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $current_route_match, LanguageManagerInterface $language_manager, StaticMenuLinkOverridesInterface $static_override) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $static_override);
    $this->currentRouteMatch = $current_route_match;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-param array<mixed> $configuration
   * @phpstan-param string $plugin_id
   * @phpstan-param mixed $plugin_definition
   * @phpstan-return self
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('language_manager'),
      $container->get('menu_link.static.overrides'),
    );
  }

  /**
   * Initializes the "links" property.
   *
   * If the "links" property has not been initialized yet, gets links from
   * language switcher and assigns them to the "links" property.
   */
  protected function initLinks(): void {
    if ($this->links !== NULL) {
      return;
    }
    // If there is no route match, for example when creating blocks on 404 pages
    // for logged-in users with big_pipe enabled use the front page instead.
    $url = $this->currentRouteMatch->getRouteObject() ? Url::fromRouteMatch($this->currentRouteMatch) : Url::fromRoute('<front>');
    $link_info = $this->languageManager->getLanguageSwitchLinks($this->getLanguageType(), $url);
    $this->links = $link_info->links ?? FALSE;
  }

  /**
   * Whether a language switch link for this menu link's language code exists.
   *
   * @return bool
   *   Whether a language switch link for this menu link's language code exists.
   */
  public function hasLink(): bool {
    $this->initLinks();
    return isset($this->links[$this->getLangCode()]);
  }

  /**
   * Gets link for language code of this language switcher menu link.
   *
   * @return array
   *   Link for language code of this language switcher menu link.
   *
   * @phpstan-return array<mixed>
   */
  protected function getLink() {
    $this->initLinks();
    return $this->hasLink() ? $this->links[$this->getLangCode()] : [];
  }

  /**
   * Gets the language code.
   *
   * @return string
   *   Language code.
   */
  protected function getLangCode(): string {
    return $this->pluginDefinition['metadata']['langcode'];
  }

  /**
   * Gets the language type.
   *
   * @return string
   *   Language type.
   */
  protected function getLanguageType(): string {
    return $this->pluginDefinition['metadata']['langtype'];
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    $link = $this->getLink();
    return (string) ($link['title'] ?? parent::getTitle());
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-return array<string, mixed>
   */
  public function getOptions() {
    $link = $this->getLink();
    return [
      'language' => $link['language'] ?? NULL,
      'set_active_class' => TRUE,
    ] + (isset($link['query']) ? [
      'query' => $link['query'],
    ] : []) + (isset($link['attributes']) ? [
      'attributes' => $link['attributes'],
    ] : []) + parent::getOptions();
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteName() {
    $link = $this->getLink();
    return isset($link['url']) ? $link['url']->getRouteName() : '';
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-return array<string, mixed>
   */
  public function getRouteParameters() {
    $link = $this->getLink();
    return isset($link['url']) ? $link['url']->getRouteParameters() : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    // @todo Make cacheable once https://www.drupal.org/node/2232375 is fixed.
    return 0;
  }

}
