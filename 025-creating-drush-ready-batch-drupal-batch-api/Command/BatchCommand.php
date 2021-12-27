<?php

namespace Drupal\modulename\Commands;

use Drupal\modulename\BatchServiceInterface;
use Drush\Commands\DrushCommands;

/**
 * Defines drush command.
 */
class BatchCommand extends DrushCommands
{

  /**
   * The batch service.
   *
   * @var \Drupal\modulename\BatchServiceInterface
   */
    protected $batch;

    /**
     * Constructor.
     *
     * @param \Drupal\modulename\BatchServiceInterface $batch
     *   The batch service.
     */
    public function __construct(BatchServiceInterface $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Run batch command.
     *
     * @param array $options
     *   Command options.
     *
     * @command modulename-batch:run
     * @aliases modulename-br
     * @option batch Batch size. Default: 10
     * @usage modulename-batch:run
     */
    public function run(array $options = ['batch' => 10])
    {
        $this->batch->create($options['batch']);
    }
}
