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
 * Defines the project task entity class.
 *
 * @ContentEntityType(
 *   id = "project_task",
 *   label = @Translation("Task"),
 *   bundle_label = @Translation("Task type"),
 *   handlers = {
 *     "access" = "Drupal\dmx_project\ProjectAccessControlHandler",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\dmx_project\Form\ProjectTaskForm",
 *       "add" = "Drupal\dmx_project\Form\ProjectTaskForm",
 *       "edit" = "Drupal\dmx_project\Form\ProjectTaskForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "dmx_project_task",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "bundle",
 *     "project" = "project",
 *     "uid" = "uid",
 *     "label" = "name",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 *   fieldable = TRUE,
 *   admin_permission = "administer project task types",
 *   links = {
 *     "canonical" = "/admin/content/task/{project_task}",
 *     "add-page" = "/admin/content/task/add",
 *     "add-form" = "/admin/content/task/add/{project_task_type}",
 *     "edit-form" = "/admin/content/task/{project_task}/edit",
 *     "delete-form" = "/admin/content/task/{project_task}/delete",
 *     "collection" = "/admin/content/task",
 *   },
 *   bundle_entity_type = "project_task_type",
 *   field_ui_base_route = "entity.project_task_type.edit_form",
 * )
 */
class ProjectTask extends ContentEntityBase implements ProjectTaskInterface {

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

    $task_message = [
      'created' => FALSE,
      'assigned' => FALSE,
      'completed' => FALSE,
    ];

    if ($update) {
      // If task is updated, set assigned message if the assigned user is not
      // equal to the original assigned user.
      if (!empty($this->get('field_dmx_project_task_assigned')->target_id) && $this->get('field_dmx_project_task_assigned')->target_id != $this->original->get('field_dmx_project_task_assigned')->target_id) {
        $task_message['assigned'] = TRUE;
      }

      // If task is updated, set completed message if status is true and the
      // original status is not true.
      if ($this->get('status')->value && !$this->original->get('status')->value) {
        $task_message['completed'] = TRUE;
      }
    }
    else {
      // If task is created, set assigned message if the assigned user is not
      // null.
      if (!empty($this->get('field_dmx_project_task_assigned')->target_id)) {
        $task_message['assigned'] = TRUE;
      }
      // If the task is created, set the created message if not assigned.
      else {
        $task_message['created'] = TRUE;
      }
    }

    // Message dmx_project_task__create
    if ($task_message['created']) {
      $message = Message::create(['template' => 'dmx_project_task__create']);
      $message->set('uid', \Drupal::currentUser()->id());
      $message->set('field_dmx_project', $this->get('project')->target_id);
      $message->set('field_dmx_project_task', $this->id());
      $message->save();
    }

    // Message dmx_project_task__assigned
    if ($task_message['assigned']) {
      $message = Message::create(['template' => 'dmx_project_task__assigned']);
      $message->set('uid', \Drupal::currentUser()->id());
      $message->set('field_dmx_project', $this->get('project')->target_id);
      $message->set('field_dmx_project_task', $this->id());
      $message->set('field_dmx_project_task_assigned', $this->get('field_dmx_project_task_assigned')->target_id);
      $message->save();
    }

    // Message dmx_project_task__completed
    if ($task_message['completed']) {
      $message = Message::create(['template' => 'dmx_project_task__completed']);
      $message->set('uid', \Drupal::currentUser()->id());
      $message->set('field_dmx_project', $this->get('project')->target_id);
      $message->set('field_dmx_project_task', $this->id());
      $message->save();
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    // Delete any messages associated with this task.
    $result = \Drupal::entityQuery('message')->condition('field_dmx_project_task', array_keys($entities))->execute();
    entity_delete_multiple('message', $result);
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['project'] = BaseFieldDefinition::create('entity_reference')
        ->setLabel(t('Project'))
        ->setDescription(t('ID of the related project.'))
        ->setRequired(TRUE)
        ->setSetting('target_type', 'project')
        ->setSetting('handler', 'default')
        ->setDisplayOptions('view', [
            'label' => 'above',
            'type' => 'id',
            'weight' => -1,
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

    $fields['name'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Task'))
        ->setDescription(t('Name of the task.'))
        ->setDefaultValue('')
        ->setRequired(TRUE)
        ->setSettings([
            'max_length' => 250,
            'text_processing' => 0,
        ])
        ->setDisplayOptions('form', [
            'weight' => 1,
            'type' => 'string_textfield',
        ])
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string_long')
        ->setLabel(t('Description'))
        ->setDescription(t('Description of the task.'))
        ->setDefaultValue('')
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

    $fields['due'] = BaseFieldDefinition::create('datetime')
        ->setLabel(t('Due'))
        ->setDescription(t('Date task is due.'))
        ->setDefaultValue('')
        ->setSettings(array(
            'datetime_type' => 'date',
        ))
        ->setDisplayOptions('view', [
            'label' => 'above',
            'weight' => 3,
            'type' => 'datetime_default',
            'settings' => [
                'date_format' => 'medium',
            ]
        ])
        ->setDisplayOptions('form', array(
            'weight' => 3,
            'type' => 'datetime_default',
        ))
        ->setDisplayConfigurable('view', TRUE)
        ->setDisplayConfigurable('form', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
        ->setLabel(t('Completed'))
        ->setDescription(t('Status of the task.'))
        ->setRequired(FALSE)
        ->setSettings(array(
            'default_value' => 0,
            'settings' => array(
                'on_label' => 'Complete',
                'off_label' => 'Incomplete',
            ),
        ))
        ->setDefaultValue(0)
        ->setDisplayOptions('view', array(
            'label' => 'hidden',
            'type' => 'task_checkbox_formatter',
            'weight' => 4,
        ))
        ->setDisplayOptions('form', array(
            'weight' => 4,
            'type' => 'boolean_checkbox',
        ))
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
        ->setLabel(t('Task creator'))
        ->setDescription(t('Creator of the task.'))
        ->setRequired(TRUE)
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

    $fields['langcode'] = BaseFieldDefinition::create('language')
        ->setLabel(t('Language code'))
        ->setDescription(t('The language code of Contact entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
        ->setLabel(t('Created'))
        ->setDescription(t('The time that the task was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
        ->setLabel(t('Changed'))
        ->setDescription(t('The time that the task was last edited.'));

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
