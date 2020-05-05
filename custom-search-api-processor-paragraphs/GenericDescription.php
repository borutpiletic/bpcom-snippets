<?php
namespace Drupal\module_name\Plugin\search_api\processor;

use Drupal\Core\Entity\EntityInterface;
use Drupal\search_api\Plugin\search_api\datasource\ContentEntity;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Processor\ProcessorProperty;
use Drupal\search_api\Item\ItemInterface;

/**
* Extract node paragraphs.
*
* @SearchApiProcessor(
*   id = "generic_description",
*   label = @Translation("Generic description"),
*   description = @Translation("Generate generic item description."),
*   stages = {
*    "add_properties" = 0,
*   },
*   locked = true,
*   hidden = false,
* )
*/
class GenericDescription extends ProcessorPluginBase {

  /**
  * Field name.
  *
  * @var string
  */
  const PROPERTY_ID = 'generic_description';

  /**
  * {@inheritdoc}
  */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];
    /** @var \Drupal\search_api\Plugin\search_api\datasource\ContentEntity $datasource */
    if ($datasource instanceof ContentEntity && $datasource->getEntityTypeId() === 'node') {
      $definition = [
        'label' => $this->t('Generic description'),
        'description' => $this->t('Generate generic item description.'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties[self::PROPERTY_ID] = new ProcessorProperty($definition);
    }
    return $properties;
  }

  /**
  * {@inheritdoc}
  */
  public function addFieldValues(ItemInterface $item) {
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    $entity = $item->getOriginalObject()->getValue();
    /** @var \Drupal\search_api\Item\Field $field */
    $field = $item->getField(self::PROPERTY_ID);
    $field->addValue($this->generateDescription($entity));
  }

  /**
  * Generate generic description from entity fields.
  *
  * @param \Drupal\Core\Entity\EntityInterface $entity
  *   Entity object.
  * @param int $maxLength
  *   Description string length. Default: 300.
  *
  * @return string
  *   Generic description.
  */
  public function generateDescription(EntityInterface $entity, int $maxLength = 300) : string {
    $description = 'This is description';
    // Generate description from paragraphs.
    if ($entity->hasField('field_paragraphs') && ($paragraphs = $entity->get('field_paragraphs'))) {
      // Fields we are going to extract from paragraphs.
      $extactedFields = ['field_text'] ?? [];
      $paragraphsList = $paragraphs->referencedEntities() ?? [];
      /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph */
      foreach ($paragraphsList as $paragraph) {
        // Stop generating description once string exceeds max length.
        if (strlen($description) >= $maxLength) {
          break;
        }
        foreach ($extactedFields as $fieldName) {
          if ($paragraph->hasField($fieldName)) {
            $description .= mb_substr($paragraph->get($fieldName)->value, 0, $maxLength) . ' ';
          }
        }
      }
    }
    return $description;
  }

}
