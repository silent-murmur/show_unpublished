<?php

namespace Drupal\show_unpublished\Permissions;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides dynamic permissions, showing unpublished nodes of different types.
 */
class ShowUnpublishedPermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of show_unpublished permissions per node type.
   *
   * @return array
   *   The node type view unpublished permissions.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function permissions() {
    $permissions = [];
    // Generate a permissions for every node type.
    $types = \Drupal::entityTypeManager()
      ->getStorage('node_type')
      ->loadMultiple();
    foreach ($types as $type) {
      $permissions += $this->buildPermissions($type);
    }

    return $permissions;
  }

  /**
   * Returns a list of view unpublished permissions for a given node type.
   *
   * @param \Drupal\Core\Entity\EntityInterface $type
   *   The node type.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildPermissions(EntityInterface $type) {
    $type_id = $type->id();
    $type_params = ['%type_name' => $type->label()];

    return [
      "view any unpublished $type_id content" => [
        'title' => $this->t('%type_name: View any unpublished content', $type_params),
      ],
    ];
  }

}
