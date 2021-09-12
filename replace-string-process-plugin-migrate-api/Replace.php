<?php

namespace Drupal\mymodule_migrate\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\Exception\BadPluginDefinitionException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Replace string matched by pattern.
 *
 * @MigrateProcessPlugin(
 *   id = "mymodule_migrate_replace"
 * )
 *
 * If multiple patterns needs to be replaces, define them as list:
 * search:
 *  - 'pattern1'
 *  - 'pattern2' ...
 *
 * @code
 * field_teaser_text:
 *  -
 *    plugin: mymodule_migrate_replace
 *    source: field_base_teaser
 *    search: 'https?\:\/\/some-hardcoded-domain\.net/'
 *    replace: /
 * @endcode
 */
class Replace extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The migration.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entityTypeManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrateExecutable, Row $row, $destinationProperty) {
    if (!isset($this->configuration['search'])) {
      throw new BadPluginDefinitionException($this->pluginId, 'search');
    }
    if (!isset($this->configuration['replace'])) {
      throw new BadPluginDefinitionException($this->pluginId, 'replace');
    }

    if (isset($value['value'])) {
      $value['value'] = $this->processValue($value['value']);
      return $value['value'];
    }
    if (is_string($value) && $value) {
      $value = $this->processValue($value);
    }

    return $value;
  }

  /**
   * Search and replace string.
   *
   * @param string $value
   *   Value to process.
   *
   * @return string
   *   Replaced string.
   */
  protected function processValue(string $value) : string {
    $modifiers = $this->configuration['modifiers'] ?? 'i';
    $pattern = $this->configuration['search'];
    $pattern = is_array($pattern) ? $pattern : [$pattern];
    foreach ($pattern as $regexPattern) {
      $value = preg_replace('/' . $regexPattern . '/' . $modifiers, $this->configuration['replace'], $value);
    }

    return $value;
  }

}
