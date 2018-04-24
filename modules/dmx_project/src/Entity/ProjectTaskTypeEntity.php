<?php

namespace Drupal\dmx_project\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Project Task Type entity. A configuration entity used to manage
 * bundles for the Project Task entity.
 *
 * @ConfigEntityType(
 *   id = "project_task_type",
 *   label = @Translation("Project Task Type"),
 *   bundle_of = "project_task",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_prefix = "project_task_type",
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   },
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dmx_project\ProjectTaskTypeListBuilder",
 *     "form" = {
 *       "default" = "Drupal\dmx_project\Form\ProjectTaskTypeEntityForm",
 *       "add" = "Drupal\dmx_project\Form\ProjectTaskTypeEntityForm",
 *       "edit" = "Drupal\dmx_project\Form\ProjectTaskTypeEntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer project task types",
 *   links = {
 *     "canonical" = "/admin/structure/project_task_type/{project_task_type}",
 *     "add-form" = "/admin/structure/project_task_type/add",
 *     "edit-form" = "/admin/structure/project_task_type/{project_task_type}/edit",
 *     "delete-form" = "/admin/structure/project_task_type/{project_task_type}/delete",
 *     "collection" = "/admin/structure/project_task_type",
 *   }
 * )
 */
class ProjectTaskTypeEntity extends ConfigEntityBundleBase implements ProjectTaskTypeEntityInterface {

  /**
   * The machine name of the project task type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the project task type.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of the project task type.
   *
   * @var string
   */
  protected $description;
  
  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

}
