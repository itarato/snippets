<?php
/**
 * Git commit log graph DB importer (for Drupal or similar git message conventions)
 *
 * Git commit message format should be:
 * HASH|Issue ISSUE_CODE by AUTHORS_SEPARATED_BY_COMMA: MESSAGE
 *
 *
 * Setup:
 * - add dependency through composer:
 * # echo '{"require":{"everyman/neo4jphp":"dev-master"}}' > composer.json && composer install
 * - generate git commit log into the same folder as this script is (set limit to what you need):
 * # git log -100 --pretty=format:"%H|%s" > FOLDER/commit_log.txt
 * - start Neo4J database
 * - run script:
 * # php parse.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$nodes = array();

$neoClient = new \Everyman\Neo4j\Client();

$query = new Everyman\Neo4j\Cypher\Query($neoClient, 'match (n)-[r]-() delete n, r;');
$query->getResultSet();
$query = new Everyman\Neo4j\Cypher\Query($neoClient, 'match (n) delete n;');
$query->getResultSet();

$file = fopen(__DIR__ . '/commit_log.txt', 'r');
while (!feof($file)) {
  $line = fgets($file);

  $matches = NULL;
  if (preg_match('/^(?P<hash>[0-9a-f]*)\|Issue #\d* by (?P<commiters>[^:]*):.*$/', $line, $matches)) {
    $commiters = explode(', ', $matches['commiters']);
    $hash = $matches['hash'];

    $commitNode = $neoClient->makeNode(array('hash' => $hash));
    $commitNode->save();

    $commitNodeLabel = new \Everyman\Neo4j\Label($neoClient, 'COMMIT');
    $commitNode->addLabels(array($commitNodeLabel));

    foreach ($commiters as $commiter) {
      if (!isset($nodes[$commiter])) {
        $nodes[$commiter] = $neoClient->makeNode(array('name' => $commiter));
        $nodes[$commiter]->save();
      }

      $commiterNodeLabel = new \Everyman\Neo4j\Label($neoClient, 'AUTHOR');
      $nodes[$commiter]->addLabels(array($commiterNodeLabel));

      $commitNode->relateTo($nodes[$commiter], 'COMMITTED')->save();
    }
  }
}
