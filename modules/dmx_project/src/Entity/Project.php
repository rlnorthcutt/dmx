<?php

namespace Drupal\dmx_project\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\message\Entity\Message;
use Drupal\user\UserInterface;

/**
 * Defines the project entity class.
 *
 * @ContentEntityType(
 *   id = "project",
 *   label = @Translation("Project"),
 *   label_singular = @Translation("project"),
 *   label_plural = @Translation("projects"),
 *   label_count = @PluralTranslation(
 *     singular = "@count project",
 *     plural = "@count projects",
 *   ),
 *   bundle_label = @Translation("Project type"),
 *   handlers = {
 *     "access" = "Drupal\dmx_project\ProjectAccessControlHandler",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\dmx_project\Form\ProjectForm",
 *       "add" = "Drupal\dmx_project\Form\ProjectForm",
 *       "edit" = "Drupal\dmx_project\Form\ProjectForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "dmx_project",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "project_type",
 *     "label" = "name",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *     "uid" = "uid",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 *   fieldable = TRUE,
 *   admin_permission = "administer project types",
 *   links = {
 *     "canonical" = "/admin/content/project/{project}",
 *     "add-page" = "/admin/content/project/add",
 *     "add-form" = "/admin/content/project/add/{project_type}",
 *     "edit-form" = "/admin/content/project/{project}/edit",
 *     "delete-form" = "/admin/content/project/{project}/delete",
 *     "collection" = "/admin/content/project",
 *   },
 *   bundle_entity_type = "project_type",
 *   field_ui_base_route = "entity.project_type.edit_form",
 * )
 */
class Project extends ContentEntityBase implements ProjectInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
        'uid' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    // Message dmx_project__create
    if (!$update) {
      $message = Message::create(['template' => 'dmx_project__create']);
      $message->set('uid', \Drupal::currentUser()->id());
      $message->set('field_dmx_project', $this->id());
      $message->save();
    }
    else {
      // Message dmx_project__status
      if ($this->getStatus() != $this->original->getStatus()) {
        $message = Message::create(['template' => 'dmx_project__status']);
        $message->set('uid', \Drupal::currentUser()->id());
        $message->set('field_dmx_project', $this->id());
        $message->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
      parent::postDelete($storage, $entities);

    // Delete any messages associated with this project.
    $result = \Drupal::entityQuery('message')->condition('field_dmx_project', array_keys($entities))->execute();
    entity_delete_multiple('message', $result);

    // Delete any tasks associated with this project.
    $result = \Drupal::entityQuery('project_task')->condition('project', array_keys($entities))->execute();
    entity_delete_multiple('project_task', $result);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('Name of the project.'))
      ->setSettings([
          'max_length' => 64,
          'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
          'weight' => 1,
          'type' => 'string_textfield',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of Contact entity.'));

    $fields['status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Status'))
      ->setDescription(t('Status of the project.'))
      ->setSettings(array(
          'default_value' => 'planned',
          'max_length' => 16,
          'allowed_values' => array(
              'plan' => 'Planned',
              'progress' => 'In progress',
              'complete' => 'Completed',
          ),
      ))
      ->setRequired(TRUE)
      ->setDefaultValue('planned')
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'string',
          'weight' => 4,
      ))
      ->setDisplayOptions('form', array(
          'weight' => 4,
          'type' => 'options_select',
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Project owner'))
      ->setDescription(t('Owner or creator of the project.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the project was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the project was last edited.'));

    $fields['description'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Description'))
      ->setDescription(t('Description of the project.'))
      ->setSettings(array(
          'default_value' => '',
          'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'basic_string',
          'weight' => 2,
      ))
      ->setDisplayOptions('form', array(
          'weight' => 2,
          'type' => 'string_textarea',
          'settings' => array(
              'rows' => 4,
          ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

      $fields['date'] = BaseFieldDefinition::create('daterange')
        ->setLabel(t('Date'))
        ->setDescription(t('Date project starts and ends.'))
        ->setDefaultValue('')
        ->setSettings(array(
            'datetime_type' => 'datetime',
            'optional_end_date' => 1,

        ))
        ->setDisplayOptions('view', [
            'label' => 'above',
            'weight' => 3,
            'type' => 'daterange_default',
            'settings' => [
                'timezone_override' => '',
                'format_type' => 'short',
                'separator' => 'to',
            ]
        ])
        ->setDisplayOptions('form', array(
            'weight' => 3,
            'type' => 'daterange_default',
        ))
        ->setDisplayConfigurable('view', TRUE)
        ->setDisplayConfigurable('form', TRUE);

      return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }
  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->set('status', $status);
    return $this;
  }

}
