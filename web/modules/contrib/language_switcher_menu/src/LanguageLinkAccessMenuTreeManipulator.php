<?php

namespace Drupal\language_switcher_menu;

use Drupal\Core\Access\AccessManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\DefaultMenuLinkTreeManipulators;
use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\language_switcher_menu\Plugin\Menu\LanguageSwitcherLink;

/**
 * Extends access check tree manipulator provided by Drupal Core.
 *
 * @todo Revisit once https://www.drupal.org/project/drupal/issues/3008889 has
 *   been fixed.
 */
class LanguageLinkAccessMenuTreeManipulator extends DefaultMenuLinkTreeManipulators {

  /**
   * The path validator.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $pathValidator;

  /**
   * Constructs a LanguageLinkAccessMenuTreeManipulator object.
   *
   * @param \Drupal\Core\Access\AccessManagerInterface $access_manager
   *   The access manager.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator.
   */
  public function __construct(AccessManagerInterface $access_manager, AccountInterface $account, EntityTypeManagerInterface $entity_type_manager, PathValidatorInterface $path_validator) {
    parent::__construct($access_manager, $account, $entity_type_manager);
    $this->pathValidator = $path_validator;
  }

  /**
   * {@inheritdoc}
   */
  protected function menuLinkCheckAccess(MenuLinkInterface $instance) {
    $access_result = parent::menuLinkCheckAccess($instance);
    if (!$instance instanceof LanguageSwitcherLink) {
      return $access_result;
    }

    if (!$instance->hasLink()) {
      return $access_result->isAllowed() ? AccessResult::neutral() : $access_result;
    }

    $url = $instance->getUrlObject()->setAbsolute(TRUE)->toString();
    $validated_url = $this->pathValidator->getUrlIfValid($url);

    return AccessResult::allowedIfHasPermission($this->account, 'view language_switcher_menu links')
      ->andIf(AccessResult::allowedIf($validated_url));
  }

}
