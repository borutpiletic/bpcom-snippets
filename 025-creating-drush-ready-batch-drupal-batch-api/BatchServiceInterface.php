<?php

namespace Drupal\modulename;

/**
 * Defines batch service interface.
 */
interface BatchServiceInterface
{

  /**
   * Create batch.
   *
   * @param int $batchSize
   *   Batch size.
   */
    public function create(int $batchSize): void;

    /**
     * Batch operation callback.
     *
     * @param array $batch
     *   Information about batch (items, size, total, ...).
     * @param array $context
     *   Batch context.
     */
    public static function process(array $batch, array &$context): void;

    /**
     * Bach operations 'finished' callback.
     *
     * @param bool $success
     *   TRUE if processing was successfully completed.
     * @param mixed $results
     *   Additional data passed from $context['results'].
     * @param array $operations
     *   List of operations that did not complete.
     */
    public static function finishProcess($success, $results, array $operations): void;
}
