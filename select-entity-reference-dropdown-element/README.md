#### Select entity reference drop-down element in Drupal 8
This is a code snippet from blog post [Select entity reference drop-down element](http://borutpiletic.com/article/select-entity-reference-drop-down-element).

Code snippet adds new form element type `select_entity_dropdown`. Element allows selection of 
different entity field values. 

**Usage example**
```php
$form['language'] = [
  '#title' => $this->t('Language'),
  '#type' => 'select_entity_reference'
  '#target_type' => 'locale', // Referenced entity type ID.
  '#option_settings' => [
    'label' => 'country_name',
    'value' => 'country_code',
  ],
];
```

**Tags**
- entity reference
- select element
- select entity drop-down