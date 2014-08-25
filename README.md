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


# Drupal (or similar) commit log to Neo4J graph DB importer

The script (parse.php) reads the commit messages and build up a graph with issues and authors, so it's possible to create queries agains the relationships.

- add dependency through composer:
```echo '{"require":{"everyman/neo4jphp":"dev-master"}}' > composer.json && composer install```
- generate git commit log into the same folder as this script is (set limit to what you need):
```git log -100 --pretty=format:"%H|%s" > FOLDER/commit_log.txt```
- start Neo4J database
- run script:
```php parse.php```
