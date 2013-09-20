<?php

function foobar() {
  $info = '<map version="0.9.0">' . "\n" . '<node TEXT="Content types">';

  $entity_type = 'node';
  $entities_info = entity_get_info($entity_type);
  foreach ($entities_info['bundles'] as $bundle => $entity_info) {
    $info .= get_fields($entity_type, $bundle) . "\n";
  }

  $info .= '</node>' . "\n" . '</map>';

  return $info;
}

function get_fields($entity_type, $bundle) {
  $info = '<node TEXT="' . $bundle . '">' . "\n";

  $field_instances = field_info_instances($entity_type, $bundle);
  foreach ($field_instances as $field_name => $field_instance) {
    $field_info = field_info_field($field_name);
    if ($field_info['type'] == 'field_collection') {
      $info .= get_fields('field_collection_item', $field_name) . "\n";
    }
    else {
      $info .= '<node TEXT="' . $field_instance['label'] . '" />' . "\n";
    }
  }

  return $info . '</node>' . "\n";
}
