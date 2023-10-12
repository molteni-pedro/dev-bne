<?php

namespace Drupal\field_context\Plugin\views\argument_default;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;
use Drupal\views\Plugin\views\argument_default\ArgumentDefaultPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default argument plugin to extract current page matching field.
 *
 * @ViewsArgumentDefault(
 *   id = "fcmatch",
 *   title = @Translation("Field from route context")
 * )
 */
class FieldContext extends ArgumentDefaultPluginBase implements CacheableDependencyInterface {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    // Appears to just be defaults for the fields in buildOptionsForm.
    $options['fcftype'] = ['default' => ''];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    // parent::buildOptionsForm($form, $form_state);.
    $field_map = \Drupal::service('entity_field.manager')->getFieldMap()['node'];
    // Stand the map on its head.
    $field_options = [];
    foreach ($field_map as $field_key => $data) {
      foreach ($data['bundles'] as $b) {
        $field_options[$b][$field_key] = $field_key;
      }
    }

    // Explicitly build [machine_name => machine_name] array.
    $options = array_combine(array_keys($field_options), array_keys($field_options));

    // Start with narrowing the list scope.
    $form['fcftype'] = [
      '#type' => 'select',
      '#title' => $this->t('Choose source content'),
      '#options' => array_merge(['' => '-Select-'], $options),
      '#default_value' => $this->options['fcftype'],
    ];

    // Ajax function inside this class is not callable, use #states to imitate.
    foreach ($field_options as $b => $f) {
      $form['fc' . $b] = [
        '#type' => 'select',
        '#title' => $this->t('Choose the field from @b', ['@b' => $b]),
        '#options' => $f,
        '#default_value' => $this->options['fc' . $b],
        '#states' => [
          // Show this field only if the Type is this bundle.
          'visible' => [
            // Use css selector.
            ':input[name="options[argument_default][fcmatch][fcftype]"]' => ['value' => $b],
            // jQuery(':input[name="options[argument_default][fctree][fcftype]"]');.
          ],
        ],
      ];
    }

    // No return line in user or taxonomy files, which I am following.
  }

  /**
   * {@inheritdoc}
   */
  public function getArgument() {
    // Where the work happens.
    $key = $this->options['fcftype'];
    if (!empty($this->options['fc' . $key])) {
      // There is something to look for.
      // Just check, if a node could be detected.
      if (($node = $this->routeMatch->getParameter('node')) && $node instanceof NodeInterface) {
        // We're in, now look for chosen field.
        $source = $node->getFieldDefinitions();
        if (isset($source[$this->options['fc' . $key]])) {
          // This node has this field!
          return $node->get($this->options['fc' . $key])->getString();
        }
      }
    }
    // No return statement for catch-all fail case?
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::PERMANENT;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['url'];
  }

}
