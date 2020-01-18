<?php

require_once __DIR__ ."/init.php";

$file_path = "/drophere/";

$premiumize_api = new Premiumize(getenv('API_KEY'),false);


function scan_dir($dir) {
    $ignored = array('.', '..', '.gitkeep');

    $files = array();    
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : false;
}


echo "Started PremiumizePHP\n";
while(true) {
    $files = scan_dir($file_path);
    //i don't care how long it takes till the file is queried maybe we could also set it to one minute
    sleep(5);
    if(!$files) {
        continue;
    }

    if(count($files)) {
        foreach ($files as $key => $file) {
            echo "Add new file to premiumize.me cloud $file \n";
            $file = $file_path."/".$file;

            //todo handel if we got to many active downloads
            $premiumize_api->add($file);
            unlink($file);
        }
    }

}
