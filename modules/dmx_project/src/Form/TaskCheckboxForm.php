<?php

/**
 * @file
 * Contains \Drupal\dmx_project\Form\TaskInlineForm.
 */

namespace Drupal\dmx_project\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Build a form to switch complete state of targeted FieldItem.
 */
class TaskCheckboxForm extends FormBase {

  /**
   * The FieldItem being targeted by this form.
   *
   * @var \Drupal\Core\Field\FieldItemInterface
   */
  protected $fieldItem;

  /**
   * The entity being used by this form.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * The current FieldItem name.
   *
   * @var string
   */
  protected $fieldName;

  /**
   * The current FieldItem delta.
   *
   * @var int
   */
  protected $delta;

  /**
   * Default value of current FieldItem.
   *
   * @var bool
   */
  protected $defaultValue;

  /**
   * The field item plugin settings.
   *
   * @var array
   */
  protected $fieldSettings;

  /**
   * Initialize this Form Builder with FieldItem definition.
   *
   * Drupal only supports one form with a given ID per page,
   * so we generate a fieldItem specific ID at getFormId().
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   FieldItem to be displayed.
   * @param array $settings
   *   The formatter settings.
   */
  public function setFieldItem(FieldItemInterface $item, array $settings = []) {
    $this->fieldItem = $item;
    $this->entity = $this->fieldItem->getEntity();
    $this->fieldName = $this->fieldItem->getFieldDefinition()->getName();
    $this->delta = $this->fieldItem->getName();
    $this->defaultValue = $this->fieldItem->value;
    $this->fieldSettings = $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    $parts = [
      'task_checkbox',
      $this->entity->getEntityTypeId(),
      $this->entity->id(),
      $this->fieldName,
      $this->delta,
      'form',
    ];
    return implode('_', $parts);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;
    $form['checkbox'] = [
      '#type' => 'checkbox',
      '#default_value' => $this->defaultValue,
      '#attributes' => [
        'data-toggle' => 'toggle',
        'class' => ['checkbox-toggle'],
      ],
      '#ajax' => [
        'callback' => [$this, 'formListAjax'],
        'event' => 'change',
        'progress' => [
          'type' => 'none',
        ],
      ],
      '#disabled' => !$this->accessCheckboxField(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Update the clicked field with given value.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The current AjaxResponse.
   *
   * @throws \Exception
   *   Thrown when the entity can't found the clicked field name.
   */
  public function formListAjax(array &$form, FormStateInterface $form_state) {
    $element = $form_state->getTriggeringElement();
    if (!empty($element)) {
      $this->updateFieldValue($form_state->getValue($element['#parents']));
    }

    $response = new AjaxResponse();
    return $response;
  }

  /**
   * Update the clicked field with given value.
   *
   * @param bool $value
   *   Value given by user.
   *
   * @throws \Exception
   *   Thrown when field name not found in entity.
   */
  public function updateFieldValue($value) {
    if (!$this->entity->hasField($this->fieldName)) {
      throw new \Exception("Field $this->fieldName not found in entity {$this->entity->id()}.");
    }

    if ($this->accessCheckboxField()) {
      $this->entity->get($this->fieldName)->set($this->delta, $value);
      $this->entity->save();
    }
  }

  /**
   * Custom access based on task update permissions or assigned reference.
   *
   * @return bool
   */
  public function accessCheckboxField() {
    $access = $this->entity->access('update');

    if (!$access) {
      if (!empty($this->entity->get('field_dmx_project_task_assigned'))) {
        $current_user = \Drupal::currentUser();

        foreach ($this->entity->get('field_dmx_project_task_assigned')->referencedEntities() as $account) {
          if ($current_user->id() == $account->id()) {
            $access = TRUE;
            break;
          }
        }
      }
    }

    return $access;
  }

}
