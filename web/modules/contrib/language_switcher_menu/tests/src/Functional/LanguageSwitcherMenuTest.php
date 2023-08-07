<?php

namespace Drupal\Tests\language_switcher_menu\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Provides functional tests for Language Switcher Menu module.
 *
 * @group language_switcher_menu
 */
class LanguageSwitcherMenuTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'locale',
    'locale_test',
    'language',
    'block',
    'language_test',
    'menu_ui',
    'language_switcher_menu',
    'menu_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Users created during set-up.
   *
   * @var \Drupal\user\Entity\User[]
   */
  protected $users;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create users.
    $this->users['admin_user'] = $this->drupalCreateUser([
      'administer blocks',
      'administer languages',
      'access administration pages',
      'configure language_switcher_menu',
      'view language_switcher_menu links',
    ]);
    $this->users['admin_menu_any'] = $this->drupalCreateUser([
      'administer menu',
      'link to any page',
    ]);
    $this->users['admin_menu_regular'] = $this->drupalCreateUser([
      'administer menu',
    ]);
    $this->users['access_content'] = $this->drupalCreateUser([
      'access content',
    ]);
    $this->users['view_links'] = $this->drupalCreateUser([
      'view language_switcher_menu links',
    ]);
  }

  /**
   * Tests language switch links provided by Language Switcher Menu module.
   */
  public function testLanguageSwitchLinks(): void {
    $this->drupalLogin($this->users['admin_user']);

    // Add a language.
    $edit = [
      'predefined_langcode' => 'fr',
    ];
    $this->drupalGet('admin/config/regional/language/add');
    $this->submitForm($edit, 'Add language');

    // Enable URL language detection and selection.
    $edit = ['language_interface[enabled][language-url]' => '1'];
    $this->drupalGet('admin/config/regional/language/detection');
    $this->submitForm($edit, 'Save settings');

    // Place menu block.
    $this->drupalPlaceBlock('system_menu_block:main');

    // Configure module.
    $edit = [
      'type' => 'language_interface',
      'parent' => 'main:',
      'weight' => '1',
    ];
    $this->drupalGet('admin/config/regional/language_switcher_menu');
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm($edit, 'Save configuration');
    $this->assertSession()->statusCodeEquals(200);

    $this->assertMenuLinks([
      '/' => 'Home',
      '/admin/config/regional/language_switcher_menu' => 'English',
      '/fr/admin/config/regional/language_switcher_menu' => 'French',
    ], 'The language links have been added to the menu after "Home".');

    $this->drupalGet('');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertMenuLinks([
      '/' => 'Home',
      '/user/' . $this->users['admin_user']->id() => 'English',
      '/fr/user/' . $this->users['admin_user']->id() => 'French',
    ], 'The language links have been added to the menu after "Home".');

    // Reconfigure weight.
    $edit = [
      'type' => 'language_interface',
      'parent' => 'main:',
      'weight' => '-10',
    ];
    $this->drupalGet('admin/config/regional/language_switcher_menu');
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm($edit, 'Save configuration');
    $this->assertSession()->statusCodeEquals(200);

    $this->assertMenuLinks([
      '/admin/config/regional/language_switcher_menu' => 'English',
      '/fr/admin/config/regional/language_switcher_menu' => 'French',
      '/' => 'Home',
    ], 'The language links have been added to the menu before "Home".');

    $this->drupalGet('');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertMenuLinks([
      '/user/' . $this->users['admin_user']->id() => 'English',
      '/fr/user/' . $this->users['admin_user']->id() => 'French',
      '/' => 'Home',
    ], 'The language links have been added to the menu before "Home".');

    // Check access for anonymous users.
    $this->drupalLogout();
    $this->assertMenuLinks([
      '/' => 'Home',
    ], 'The language links are not visible to anonymous users.');
    $this->drupalGet('admin/config/regional/language_switcher_menu');
    $this->assertSession()->statusCodeEquals(403);

    // Check access for authenticated users.
    $this->drupalLogin($this->users['access_content']);
    $this->assertMenuLinks([
      '/' => 'Home',
    ], 'The language links are not visible for authenticated users without permission to view them.');
    $this->drupalGet('admin/config/regional/language_switcher_menu');
    $this->assertSession()->statusCodeEquals(403);

    $this->drupalLogin($this->users['view_links']);
    $this->drupalGet('/user/' . $this->users['view_links']->id());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertMenuLinks([
      '/user/' . $this->users['view_links']->id() => 'English',
      '/fr/user/' . $this->users['view_links']->id() => 'French',
      '/' => 'Home',
    ], 'The language links are visible for authenticated users with permission to view them.');
    $this->drupalGet('admin/config/regional/language_switcher_menu');
    $this->assertSession()->statusCodeEquals(403);

    // Check that links are visible on pages that deny access.
    $this->drupalGet('user/' . $this->users['admin_user']->id() . '/edit');
    $this->assertSession()->statusCodeEquals(403);
    $this->assertMenuLinks([
      '/system/403' => 'English',
      '/fr/system/403' => 'French',
      '/' => 'Home',
    ], 'The language links are visible on pages that deny access.');

    // Check that links are shown in a non-standard language.
    $this->clickLink('French');
    $this->assertMenuLinks([
      '/system/403' => 'English',
      '/fr/system/403' => 'French',
      '/fr' => 'Home',
    ], 'The menu links are correct in a non-standard language.');

    // Reconfigure to disable.
    $this->drupalLogin($this->users['admin_user']);
    $edit = [
      'type' => 'language_interface',
      'parent' => '',
      'weight' => '1',
    ];
    $this->drupalGet('admin/config/regional/language_switcher_menu');
    $this->assertSession()->statusCodeEquals(200);
    $this->submitForm($edit, 'Save configuration');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertMenuLinks([
      '/' => 'Home',
    ], 'The language links have been disabled.');

    // Check that a custom link targeting "<current>" (i.e. a link not added by
    // language_switcher_menu module) is not visible in menu.
    // @todo Remove once https://www.drupal.org/project/drupal/issues/3008889
    // has been fixed.
    $edit = [
      'link[0][uri]' => 'route:<current>',
      'title[0][value]' => 'link_current',
      'description[0][value]' => '',
      'enabled[value]' => 1,
      'expanded[value]' => 0,
      'menu_parent' => 'main:',
      'weight[0][value]' => 0,
    ];

    $this->drupalLogin($this->users['admin_menu_regular']);
    $this->drupalGet('admin/structure/menu/manage/main/add');
    $this->submitForm($edit, 'Save');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Error message');
    $this->assertSession()->pageTextContains("The path '<current>' is inaccessible.");
    $this->assertSession()->pageTextNotContains('The menu link has been saved.');
    $this->assertMenuLinks([
      '/' => 'Home',
    ], 'Custom link targeting <current> is not visible in menu.');

    $this->drupalLogin($this->users['admin_menu_any']);
    $this->drupalGet('admin/structure/menu/manage/main/add');
    $this->submitForm($edit, 'Save');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextNotContains('Error message');
    $this->assertSession()->pageTextNotContains("The path '<current>' is inaccessible.");
    $this->assertSession()->pageTextContains('The menu link has been saved.');
    $this->assertMenuLinks([
      '/' => 'Home',
      '/admin/structure/menu/item/1/edit' => 'link_current',
    ], 'Custom link targeting <current> is visible in menu with permission to link to any page.');

    $this->drupalGet('');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertMenuLinks([
      '/' => 'Home',
      '/user/' . $this->users['admin_menu_any']->id() => 'link_current',
    ], 'Custom link targeting <current> is visible in menu with permission to link to any page.');

    $this->drupalLogin($this->users['view_links']);
    $this->assertMenuLinks([
      '/' => 'Home',
    ], 'Custom link targeting <current> is not visible in menu.');
  }

  /**
   * Asserts that menu link urls and labels are shown as expected.
   *
   * @param string[] $expected
   *   Expected menu link labels keyed by expected URL in expected order.
   * @param string $text
   *   Assert message.
   */
  protected function assertMenuLinks(array $expected, string $text): void {
    $language_switchers = $this->xpath('//nav/ul/li');
    $labels = [];
    $urls = [];
    foreach ($language_switchers as $list_item) {
      $link = $list_item->find('xpath', 'a');
      $urls[] = $link->getAttribute('href');
      $labels[] = $link->getText();
    }
    // Tests may be executed in a subdirectory (e.g. by Drupal CI). Run URLs
    // through \Drupal\Core\Url to get the correct URL in use.
    $expected_compare = [];
    foreach ($expected as $url => $label) {
      $url = Url::fromUserInput($url);
      $options = $url->getOptions();
      if (isset($options['query']['destination'])) {
        $options['query']['destination'] = Url::fromUserInput($options['query']['destination'])->toString();
      }
      $url->setOptions($options);
      $url = $url->toString();
      $expected_compare[$url] = $label;
    }
    $this->assertSame(array_keys($expected_compare), $urls, $text);
    $this->assertSame(array_values($expected_compare), $labels, $text);
  }

}
