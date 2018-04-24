<?php

/**
 * @file
 * Contains \Drupal\dmx_project\Form\ProjectTaskInlineForm.
 */

namespace Drupal\dmx_project\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\dmx_project\Entity\ProjectTask;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form builder for the project_task form.
 */
class ProjectTaskInlineForm implements FormInterface, ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager service.
   *
   * We need this for the submit handler.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Container injection factory.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service discovery container.
   *
   * @return self
   *   The form object.
   */
  public static function create(ContainerInterface $container) {
    $form = new static(
        $container->get('entity_type.manager')
    );
    $form->setStringTranslation($container->get('string_translation'));
    return $form;
  }

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'project_task_inline_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['task'] = [
        '#type' => 'textarea',
        '#placeholder' => $this->t('Add a task...'),
        '#required' => TRUE,
        '#attributes' => [
          'data-length' => 250,
        ],
    ];

    $form['assigned'] = [
        '#type' => 'entity_autocomplete',
        '#target_type' => 'user',
        '#selection_settings' => [
          'include_anonymous' => FALSE,
        ],
        '#placeholder' => $this->t('Assign task to...'),
    ];

    $form['due'] = [
        '#type' => 'date',
        '#field_prefix' => '<i class="material-icons">date_range</i>',
    ];

    $form['actions'] = [
        '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Add'),
        '#attributes' => [
            'class' => ['btn-primary', 'btn-sm']
        ]
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $project_task = ProjectTask::create(['bundle' => 'task']);
    $project_task->set('status', 'plan');

    $user = \Drupal::currentUser();
    $project_task->set('uid', $user->id());

    $project = \Drupal::routeMatch()->getParameter('project');
    $project_task->set('project', $project->id());

    $task = $form_state->getValue('task');
    $project_task->set('name', $task);

    $assigned = !empty($form_state->getValue('assigned')) ? entity_load('user', $form_state->getValue('assigned')) : NULL;
    if (!empty($assigned)) {
      $project_task->set('field_dmx_project_task_assigned', $assigned);
    }

    if (!empty($form_state->getValue('due'))) {
      $project_task->set('due', $form_state->getValue('due'));
    }

    $project_task->save();

    drupal_set_message(t('New task created.'));
  }
}