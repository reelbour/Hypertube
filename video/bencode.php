<?php

function bdecode($file) {
  $output = shell_exec("./bencode.out " . $file);
  //echo $output . "\n\n";
  $torrentData = json_decode($output);
  //echo "\n\n";
  return $torrentData;
}

$file = "test.torrent";
$out = bdecode($file);
var_dump($out);

?>
