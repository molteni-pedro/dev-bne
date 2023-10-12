<?php

namespace Drupal\Tests\field_context\Functional;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\block\Traits\BlockCreationTrait;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;
use Drupal\Tests\views\Functional\ViewTestBase;
use Drupal\views\Tests\ViewTestData;
use Drupal\views\Views;

/**
 * Tests Field Context view plugin.
 *
 * @group field_context
 */
class FieldContextTest extends ViewTestBase {

  use BlockCreationTrait;
  use EntityReferenceTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'field_context',
    'node',
    'views',
    'taxonomy',
    'field_context_test_views',
  ];

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = [
    'test_field_context',
  ];

  /**
   * Stores the nodes used for the different tests.
   *
   * @var \Drupal\node\NodeInterface[]
   */
  protected $nodes = [];

  /**
   * The vocabulary used for creating terms.
   *
   * @var \Drupal\taxonomy\VocabularyInterface
   */
  protected $vocabulary;

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function setUp($import_test_views = TRUE, $modules = []): void {
    parent::setUp($import_test_views, $modules);

    $this->mockStandardInstall();

    $modules[] = 'field_context_test_views';
    if ($import_test_views) {
      ViewTestData::createTestViews(static::class, $modules);
    }

    $settings = [
      'name' => $this->randomMachineName(),
      'description' => $this->randomMachineName(),
      // Use the first available text format.
      'format' => 'plain_text',
      'vid' => $this->vocabulary->id(),
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ];
    $term = Term::create($settings);
    $term->save();

    $node = [];
    $node['type'] = 'article';
    $node['field_views_testing_tags'][]['target_id'] = $term->id();
    $this->nodes[] = $this->drupalCreateNode($node);
    $this->nodes[] = $this->drupalCreateNode($node);
    $this->nodes[] = $this->drupalCreateNode($node);

    $this->drupalPlaceBlock("views_block:test_field_context-block_1");
  }

  /**
   * Provides a workaround for the inability to use the standard profile.
   *
   * COPIED FROM
   * core/modules/taxonomy/tests/src/Functional/Views/TaxonomyTestBase.
   *
   * @see https://www.drupal.org/node/1708692
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function mockStandardInstall() {
    $this->drupalCreateContentType([
      'type' => 'article',
    ]);
    // Create the vocabulary for the tag field.
    $this->vocabulary = Vocabulary::create([
      'name' => 'Views testing tags',
      'vid' => 'views_testing_tags',
    ]);
    $this->vocabulary->save();
    $field_name = 'field_' . $this->vocabulary->id();

    $handler_settings = [
      'target_bundles' => [
        $this->vocabulary->id() => $this->vocabulary->id(),
      ],
      'auto_create' => TRUE,
    ];
    $this->createEntityReferenceField('node', 'article', $field_name, 'Tags', 'taxonomy_term', 'default', $handler_settings, FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getFormDisplay('node', 'article')
      ->setComponent($field_name, [
        'type' => 'entity_reference_autocomplete_tags',
        'weight' => -4,
      ])
      ->save();

    $display_repository->getViewDisplay('node', 'article')
      ->setComponent($field_name, [
        'type' => 'entity_reference_label',
        'weight' => 10,
      ])
      ->save();
    $display_repository->getViewDisplay('node', 'article', 'teaser')
      ->setComponent($field_name, [
        'type' => 'entity_reference_label',
        'weight' => 10,
      ])
      ->save();
  }

  /**
   * Test the overall Field Context module.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   * @throws \Behat\Mink\Exception\ResponseTextException
   */
  public function testFieldContext() {
    $assert = $this->assertSession();

    // Make sure view exists.
    $view = Views::getView('test_field_context');
    $this->assertNotNull($view);

    // Make sure the setup did create nodes.
    $this->assertNotEmpty($this->nodes);
    $first = $this->nodes[0];
    $second = $this->nodes[1];
    $third = $this->nodes[2];

    // Go to the first page.
    $this->drupalGet('node/' . $first->id());
    $assert->statusCodeEquals(200);

    // Assuming the block was placed correctly we should see the title of the
    // other two nodes.
    $assert->pageTextContains($second->label());
    $assert->pageTextContains($third->label());
  }

}
