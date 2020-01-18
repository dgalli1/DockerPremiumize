<?php
header('Content-Type: application/javascript');
$files = scandir(__DIR__ ."/static");
foreach ($files as $key => $value) {
    if (strpos($value, '.js') !== false) {
        echo "/* ". $value ." */\n";
        echo file_get_contents(__DIR__ ."/static/" .$value) ."\n\n";
    }
}
