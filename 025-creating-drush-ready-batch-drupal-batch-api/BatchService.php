<?php

namespace Drupal\modulename;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Service for creating batches.
 */
class BatchService implements BatchServiceInterface
{
    use StringTranslationTrait;

    /**
     * The logger channel.
     *
     * @var \Drupal\Core\Logger\LoggerChannelInterface
     */
    protected $loggerChannel;

    /**
     * Constructor.
     *
     * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
     *   The logger factory.
     */
    public function __construct(LoggerChannelFactoryInterface $loggerFactory)
    {
        $this->loggerChannel = $loggerFactory->get('<modulename>');
    }

    /**
     * {@inheritdoc}
     */
    public function create(int $batchSize = 10): void
    {
        /** @var \Drupal\Core\Batch\BatchBuilder $batchBuilder */
        $batchBuilder = (new BatchBuilder())
            ->setTitle($this->t('Running node updates...'))
            ->setFinishCallback([self::class, 'finishProcess'])
            ->setInitMessage('The initialization message (optional)')
            ->setProgressMessage('Completed @current of @total. See other placeholders.');

        $nodes = array_fill(0, 13, 'test');
        $total = count($nodes);
        $itemsToProcess = [];
        $i = 0;
        // Create multiple batch operations based on the $batchSize.
        foreach ($nodes as $node) {
            $i++;
            $itemsToProcess[] = $node;
            if ($i == $total || !($i % $batchSize)) {
                $batchBuilder->addOperation([BatchService::class, 'process'], [
                    'batch' => [
                        'items' => $itemsToProcess,
                        'size' => $batchSize,
                        'total' => $total,
                    ],
                ]);
                $itemsToProcess = [];
            }
        }

        batch_set($batchBuilder->toArray());
        $this->loggerChannel->notice('Batch created.');
        if (PHP_SAPI === 'cli' && function_exists('drush_backend_batch_process')) {
            drush_backend_batch_process();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function process($batch, &$context): void
    {
        // Process elements stored in the each batch (operation).
        foreach ($batch['items'] as $item) {
            $context['results'][] = $item;
            sleep(1);
        }
        // Message displayed above the progress bar or in the CLI.
        $processedItems = !empty($context['results']) ? count($context['results']) : $batch['size'];
        $context['message'] = 'Processed ' . $processedItems . '/' . $batch['total'];

        \Drupal::logger('<modulename>')->info(
            'Batch processing completed: ' . $processedItems . '/' . $batch['total']
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function finishProcess($success, $results, array $operations): void
    {
        // Do something when processing is finished.
        if ($success) {
            \Drupal::logger('<modulename>')->info('Batch processing completed.');
        }
        if (!empty($operations)) {
            \Drupal::logger('<modulename>')->error('Batch processing failed: ' . implode(', ', $operations));
        }
    }
}
