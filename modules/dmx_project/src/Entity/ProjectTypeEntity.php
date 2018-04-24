<?php

namespace Drupal\dmx_project\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Project Type entity. A configuration entity used to manage
 * bundles for the Project entity.
 *
 * @ConfigEntityType(
 *   id = "project_type",
 *   label = @Translation("Project Type"),
 *   bundle_of = "project",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_prefix = "project_type",
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   },
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dmx_project\ProjectTypeListBuilder",
 *     "form" = {
 *       "default" = "Drupal\dmx_project\Form\ProjectTypeEntityForm",
 *       "add" = "Drupal\dmx_project\Form\ProjectTypeEntityForm",
 *       "edit" = "Drupal\dmx_project\Form\ProjectTypeEntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer project types",
 *   links = {
 *     "canonical" = "/admin/structure/project_type/{project_type}",
 *     "add-form" = "/admin/structure/project_type/add",
 *     "edit-form" = "/admin/structure/project_type/{project_type}/edit",
 *     "delete-form" = "/admin/structure/project_type/{project_type}/delete",
 *     "collection" = "/admin/structure/project_type",
 *   }
 * )
 */
class ProjectTypeEntity extends ConfigEntityBundleBase implements ProjectTypeEntityInterface {

  /**
   * The machine name of the project type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the project type.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of the project type.
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
