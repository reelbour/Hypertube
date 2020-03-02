<?php

$file = "test.torrent";
$fileContent = file_get_contents($file);
$escapedFileContent = str_replace("'", "'\''", $fileContent);
$output = shell_exec("./bencode.out '" . $escapedFileContent . "'");
$torrentData = json_decode($output);

var_dump($torrentData);

?>
