<?php
header("Content-type: text/css");
$files = scandir(__DIR__ ."/static");
foreach ($files as $key => $value) {
    if (strpos($value, '.css') !== false) {
        echo "/* ". $value ." */\n";
        echo file_get_contents(__DIR__ ."/static/" .$value) ."\n\n";
    }
}
