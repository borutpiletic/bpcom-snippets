# Replace content during migration

Simple migration process plugin for Drupal 8/9 which allows you to replace content during the migration.
Found it useful for hardcoded links inside the content and file paths where CKEditor
inserted the absolute paths.

You can read more about the plugin in this blog post: [Replace string process plugin](http://borutpiletic.com/article/replace-string-process-plugin-migrate-api)

**Usage example**

```yml
field_teaser_text:
   -
      plugin: mymodule_migrate_replace
      source: field_teaser
      search: 'https?\:\/\/some-hardcoded-domain\.net/'
      replace: /
```
By default, the plugin will do replacement using a case-insensitive expression.
You can change that including other regex modifiers by using the `modifiers` option.

Tags:
- Migration API
- Drupal 9
- Drupal 7
