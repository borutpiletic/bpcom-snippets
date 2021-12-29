<?php

namespace Drupal\modulename\Form;

use Drupal\modulename\BatchServiceInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines batch form.
 */
class BatchForm extends FormBase
{

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
    protected $messenger;

    /**
     * The batch service.
     *
     * @var \Drupal\modulename\BatchServiceInterface
     */
    protected $batch;

    /**
     * Constructor.
     *
     * @param \Drupal\Core\Messenger\MessengerInterface $messenger
     *   The messenger.
     * @param \Drupal\modulename\BatchServiceInterface $batch
     *   THe batch service.
     */
    public function __construct(MessengerInterface $messenger, BatchServiceInterface $batch)
    {
        $this->messenger = $messenger;
        $this->batch = $batch;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('messenger'),
            $container->get('modulename.batch'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'test_batch_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['actions']['#type'] = 'actions';
        $form['descriptions'] = ['#markup' => '<p>This form will run batch processing.</p>'];
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Run batch process'),
            '#button_type' => 'primary',
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->batch->create();
    }
}
