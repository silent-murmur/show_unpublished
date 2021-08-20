<?php

namespace Drupal\show_unpublished\Handler;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeAccessControlHandler;

/**
 * Defines the access control handler for the node entity type.
 *
 * @see \Drupal\node\Entity\Node
 * @ingroup node_access
 */
class ShowUnpublishedAccessControlHandler extends NodeAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $node, $operation, AccountInterface $account) {
    /** @var \Drupal\node\NodeInterface $node */

    // Fetch information from the node object if possible.
    $status = $node->isPublished();
    $uid = $node->getOwnerId();
    $permission = 'view any unpublished ' . $node->getType() . ' content';

    // Check if authors can view their own unpublished nodes.
    if ($operation === 'view' && !$status && $account->hasPermission('view own unpublished content') && $account->isAuthenticated() && $account->id() == $uid) {
      return AccessResult::allowed()->cachePerPermissions()->cachePerUser()->addCacheableDependency($node);
    }

    // Check if authors can view all unpublished nodes.
    if ($operation === 'view' && !$status && $account->hasPermission($permission)) {
      return AccessResult::allowed()->cachePerPermissions()->cachePerUser()->addCacheableDependency($node);
    }

    // Evaluate node grants.
    return $this->grantStorage->access($node, $operation, $account);
  }

}
