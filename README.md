Miscellaneous snippets
----------------------

# Double cache

Double cache attempts to provide a minimal double caching solution to Drupal 7. The 2 levels of chaches are static cache and permanent cache. The reason is Drupal's permanent cache is still doing database queries to access cached data. Static cache should ease that problem.

Usage:

```php
<?php
$data = DoubleCache::get('my_cache_key');
if (!$data->hasData()) {
  $generated_data = array();

  // Do heavy processing.

  $data->set($generated_data);
}

$data->value();
?>
```

# Entity structure mindmap generator

Applying the script on Devel module's devel/php page will provide a dowloadable *.mm file with the entity strucutre (entities with their fields).

Simple copy and paste the code (except the first line: ```<?php```) and submit the form.
