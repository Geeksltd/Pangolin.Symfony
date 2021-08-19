<?php

namespace Geeks\Pangolin\Lib;

//use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\ApcuAdapter;


class LocaltimeHelper
{


    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    private static function getCacheObject()
    {
       return new ApcuAdapter();
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public static function getLocaltime()
    {
        $cache = self::getCacheObject();

        $localtime = $cache->getItem('default_local_time');

        if ($localtime->get()) {
            return $localtime->get();
        } else {
            return date("Y/m/d H:i:s");
        }

    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public static function updateLocaltime(string $date)
    {
        $cache = self::getCacheObject();

        $localtime = $cache->getItem('default_local_time');
        $localtime->set($date);
        $cache->save($localtime);
        return $localtime->get();
    }


    /**
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public static function resetLocaltime()
    {
        $cache = self::getCacheObject();
        return $cache->delete('default_local_time');
    }
}