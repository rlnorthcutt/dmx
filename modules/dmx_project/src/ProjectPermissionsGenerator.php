<?php

namespace Drupal\dmx_project;

use Drupal\dmx_project\Entity\ProjectTypeEntity;
use Drupal\dmx_project\Entity\ProjectTaskTypeEntity;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class ProjectPermissionsGenerator
 */
class ProjectPermissionsGenerator {

  use StringTranslationTrait;

  /**
   * Loop through all ProjectTypeEntity and build an array of permissions.
   *
   * @return array
   */
  public function projectTypePermissions() {
    $perms = [];
    foreach (ProjectTypeEntity::loadMultiple() as $entity_type) {
      $perms += $this->buildProjectPermissions($entity_type);
    }
    return $perms;
  }

  /**
   * Loop through all ProjectTaskTypeEntity and build an array of permissions.
   *
   * @return array
   */
  public function projectTaskTypePermissions() {
    $perms = [];
    foreach (ProjectTaskTypeEntity::loadMultiple() as $entity_type) {
      $perms += $this->buildProjectTaskPermissions($entity_type);
    }
    return $perms;
  }

  /**
   * Create the permissions desired for an individual entity type.
   *
   * @param ProjectTypeEntity $entity_type
   *
   * @return array
   */
  protected function buildProjectPermissions(ProjectTypeEntity $entity_type) {
    $type_id = $entity_type->id();
    $bundle_of = $entity_type->getEntityType()->getBundleOf();
    $type_params = [
      '%type_name' => $entity_type->label(),
      '%bundle_of' => $bundle_of,
    ];

    return [
      "create $bundle_of $type_id" => [
        'title' => $this->t('%type_name: Create new %bundle_of', $type_params),
      ],
      "view any $bundle_of $type_id" => [
        'title' => $this->t('%type_name: View any %bundle_of', $type_params),
      ],
      "view own $bundle_of $type_id" => [
        'title' => $this->t('%type_name: View own %bundle_of', $type_params),
      ],
      "edit any $bundle_of $type_id" => [
        'title' => $this->t('%type_name: Edit any %bundle_of', $type_params),
      ],
      "edit own $bundle_of $type_id" => [
        'title' => $this->t('%type_name: Edit own %bundle_of', $type_params),
      ],
      "delete any $bundle_of $type_id" => [
        'title' => $this->t('%type_name: Delete any %bundle_of', $type_params),
      ],
      "delete own $bundle_of $type_id" => [
        'title' => $this->t('%type_name: Delete own %bundle_of', $type_params),
      ],
    ];
  }

  /**
   * Create the permissions desired for an individual entity type.
   *
   * @param ProjectTaskTypeEntity $entity_type
   *
   * @return array
   */
  protected function buildProjectTaskPermissions(ProjectTaskTypeEntity $entity_type) {
    $type_id = $entity_type->id();
    $bundle_of = $entity_type->getEntityType()->getBundleOf();
    $type_params = [
        '%type_name' => $entity_type->label(),
        '%bundle_of' => $bundle_of,
    ];

    return [
        "create $bundle_of $type_id" => [
            'title' => $this->t('%type_name: Create new project task', $type_params),
        ],
        "view any $bundle_of $type_id" => [
            'title' => $this->t('%type_name: View any project task', $type_params),
        ],
        "view own $bundle_of $type_id" => [
            'title' => $this->t('%type_name: View own project task', $type_params),
        ],
        "edit any $bundle_of $type_id" => [
            'title' => $this->t('%type_name: Edit any project task', $type_params),
        ],
        "edit own $bundle_of $type_id" => [
            'title' => $this->t('%type_name: Edit own project task', $type_params),
        ],
        "delete any $bundle_of $type_id" => [
            'title' => $this->t('%type_name: Delete any project task', $type_params),
        ],
        "delete own $bundle_of $type_id" => [
            'title' => $this->t('%type_name: Delete own project task', $type_params),
        ],
    ];
  }
}
