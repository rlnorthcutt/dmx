<?php

namespace Drupal\dmx_project\Entity;

use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Project Task entities.
 *
 * @ingroup dmx_project
 */
interface ProjectTaskInterface extends EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Project Task entity name.
   *
   * @return string
   *   Name of the Project Task entity.
   */
  public function getName();

  /**
   * Sets the Project Task entity name.
   *
   * @param string $name
   *   The Project Task entity name.
   *
   * @return \Drupal\dmx_project\Entity\ProjectTaskInterface
   *   The called Project Task entity entity.
   */
  public function setName($name);

  /**
   * Gets the Project Task entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Project Task entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Project Task entity creation timestamp.
   *
   * @param int $timestamp
   *   The Project Task entity creation timestamp.
   *
   * @return \Drupal\dmx_project\Entity\ProjectTaskInterface
   *   The called Project Task entity entity.
   */
  public function setCreatedTime($timestamp);
}
