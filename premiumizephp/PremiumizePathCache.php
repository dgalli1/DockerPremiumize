<?php

class PremiumizePathCache {
    /**
     * Cache represents files on the disk. Its a direct representation of a filesystem (childrens etc) 
     *
     * @var array
     */
    private $cache = [];
    private $cache_filename = __DIR__ ."/cache/filecache.json";
    private $premiumize;
    private $retries = 0;
    private $root_folder_id;
    function __construct($premiumize) {
        /** @var Premiumize $premiumize */
        //get complete all folders from subfolder via API
        $this->premiumize = $premiumize;
        $this->root_folder_id = getenv('LIBRARY_FOLDER');
        if(file_exists($this->cache_filename)) {
            /**
             * @todo Replace File cache with a db -> (will be necessary once we get to a libary bigger then 1000+ files)
             */
            $this->cache = json_decode(file_get_contents($this->cache_filename),true);
        } else {
            $this->queryRootFolder($this->root_folder_id);
        }
    }

    private function queryRootFolder($id) {
        $children = $this->premiumize->getFolder($id);

        foreach ($children as  $key => $file) {
            $this->addToCache($this->root_folder_id,$file);
        }
    }
    private function addToCache($parent_id,$folder) {
        $this->cache[$parent_id]['children'][] = $folder['id'];
        $this->cache[$folder['id']] = $folder;
        $this->cache[$folder['id']]['parent_id'] = $parent_id;
        file_put_contents($this->cache_filename,json_encode($this->cache));
    }


    /**
     * Only queries Premiuimze API when needed, if a chache for the requsted file exists it will query from the cache
     *
     * @return void
     */
    public function resolvePath($path)  {
        $path_fragements = explode('/',$path);
        unset($path_fragements[0]);
        unset($path_fragements[1]);
        unset($path_fragements[2]);
        $parent_id = $this->root_folder_id;
        
        foreach ($path_fragements as $key => $fragment) {
            //search in cache for parent_id
            $cache = $this->cache[$parent_id];
            if(!array_key_exists('children',$cache))  {
                $api_children = $this->premiumize->getFolder($cache['id']);
                foreach ($api_children as $key => $file) {
                    $this->addToCache($parent_id,$file);
                }
                $cache = $this->cache[$parent_id];
            }
            $children = $cache['children'];
            $found = false;
            foreach ($children as $key => $value) {
       
                $child = $this->cache[$value];
                if($child['name'] != $fragment) {
                    continue;
                }
                $found = true;
                
                if($child['type'] == 'folder') {
                    $parent_id = $child['id'];
                }
                if($child['type'] == 'file') {
                    return $this->premiumize->getFile($child['id']);
                }

            }
            if(!$found) {
                $this->retries++;
                if($this->retries > 10) {
                    die("Too many API Requests, something is wrong");
                }
                $api_children = $this->premiumize->getFolder($cache['id']);
                foreach ($api_children as $key => $file) {
                    $this->addToCache($parent_id,$file);
                }
                $cache = $this->cache[$parent_id];
                return $this->resolvePath($path);
               
            }
        }
    }
}

