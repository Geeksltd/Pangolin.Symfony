<?php

namespace Geeks\Pangolin\Lib;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class LocaltimeHelper 
{


    public static function getLocaltime()
    {
        $cache = new FilesystemAdapter();
        $localtime = $cache->getItem('default_local_time');

        if($localtime->get()){
            return $localtime->get();
        }
        else {
            return date("Y/m/d H:i:s");
        }

    }

    public static function updateLocaltime(string $date)
    {
        $cache = new FilesystemAdapter();
        $localtime = $cache->getItem('default_local_time');
        $localtime->set($date);
        $cache->save($localtime);
        return $localtime->get();
    }


    public static function resetLocaltime()
    {
        $cache = new FilesystemAdapter();
        return $cache->delete('default_local_time');
    }
}