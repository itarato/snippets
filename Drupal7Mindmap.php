<?php
/**
 * @file
 *
 * Mindmap generator from Drupal 7 entity structure.
 * Usage:
 *  - copy and paste code part into Devel's devel/php page and execute.
 */

/**
 * Class Drupal7Mindmap
 */
class Drupal7Mindmap {

  public static function generate() {
    $info = '<map version="0.9.0">' . "\n" . '  <node TEXT="Drupal">' . "\n";

    $entity_types = entity_get_info();
    $position = 'right';
    foreach ($entity_types as $type => $entities_info) {
      $info .= '    <node TEXT="' . urlencode($type) . '" POSITION="' . $position . '">' . "\n";
      $bundle_info = array();
      foreach ($entities_info['bundles'] as $bundle => $entity_info) {
        $info .= self::getFields($type, $bundle, '      ') . "\n";
      }
      $info .= "    </node>\n";
      $position = $position == 'right' ? 'left' : 'right';
    }

    $info .= '  </node>' . "\n" . '</map>';

    drupal_add_http_header('Content-Type', 'application/x-freemind');
    drupal_add_http_header('Content-Disposition', 'attachment; filename=drupal_architecture.mm;');

    echo $info;

    drupal_exit();
  }

  private static function getFields($entity_type, $bundle, $indentation = '') {
    $info = $indentation . '<node TEXT="' . urlencode($bundle) . '">' . "\n";

    $field_instances = field_info_instances($entity_type, $bundle);
    foreach ($field_instances as $field_name => $field_instance) {
      $field_info = field_info_field($field_name);
      if ($field_info['type'] == 'field_collection') {
        $info .= self::getFields('field_collection_item', $field_name, $indentation . '  ') . "\n";
      }
      else {
        $info .= $indentation . '  <node TEXT="' . urlencode($field_name) . '"/>' . "\n";
      }
    }

    $info .=  $indentation . '</node>';

    return $info;
  }

}

Drupal7Mindmap::generate();
