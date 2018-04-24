<?php

namespace Drupal\dmx_project\Entity;

use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Project entities.
 *
 * @ingroup dmx_project
 */
interface ProjectInterface extends EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Project entity name.
   *
   * @return string
   *   Name of the Project entity.
   */
  public function getName();

  /**
   * Sets the Project entity name.
   *
   * @param string $name
   *   The Project entity name.
   *
   * @return \Drupal\dmx_project\Entity\ProjectInterface
   *   The called Project entity entity.
   */
  public function setName($name);

  /**
   * Gets the Project entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Project entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Project entity creation timestamp.
   *
   * @param int $timestamp
   *   The Project entity creation timestamp.
   *
   * @return \Drupal\dmx_project\Entity\ProjectInterface
   *   The called Project entity entity.
   */
  public function setCreatedTime($timestamp);
}
