<?php

function qlog($s) {
  $f = fopen('/tmp/qlog.txt', 'a+');
  fwrite($f, getmypid() . ' -- ' . date('Y-m-d H:i:s -- ') . $s . "\n---\n");
  fclose($f);
}

