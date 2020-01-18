<?php
// curl --data "path=/blackhole_watch/Black Clover/Black Clover - S01E112 - Humans Who Can Be Trusted HDTV-1080p.mkv" http://localhost/premiumapi/resolvePath.php
// header('Content-Type: application/json');
require_once __DIR__ . '/../init.php';

if(!array_key_exists('path',$_POST)) {
    http_response_code(503);
    die();
}
$path = htmlspecialchars_decode($_POST['path']);
$premiumize_api = new Premiumize(getenv('API_KEY'),true);
echo $premiumize_api->resolvePath($path);
