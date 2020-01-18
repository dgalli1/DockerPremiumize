<?php
require __DIR__.'/premiumize.php';
require __DIR__ .'/PremiumizePathCache.php';
require __DIR__.'/vendor/autoload.php';
// deactivate because this sucks with docker lets just request env variables directly...
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->load();

$required_key = [
    'API_KEY',
    'LIBRARY_FOLDER',
    'DOWNLOAD_FOLDER'
];
foreach ($required_key as $key => $value) {
    if(!array_key_exists($value,$_ENV) || strlen(getenv($value)) === 0) {
        die($value." is required");
    }
}