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
    public static function getLocalDateTime()
    {
        $cache = self::getCacheObject();

        $localTime = $cache->getItem('default_local_time')->get();
        $localDate = $cache->getItem('default_local_date')->get();

        if ($localTime || $localDate) {
            return  \DateTime::createFromFormat("d/m/Y H:i:s", self::generateDateTimeString($localDate, $localTime));
        } else {
            return new \DateTime();
        }

    }

    private static function generateDateTimeString($date=null, $time=null)
    {
        if($date && $time) return $date . ' ' . $time;
        if($date && !$time) return  $date . date(" H:i:s");

        else return  date("d/m/Y ")  . $time;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public static function updateLocalTime(string $date)
    {
        $cache = self::getCacheObject();

        $localtime = $cache->getItem('default_local_time');
        $localtime->set($date);
        $cache->save($localtime);
        return $localtime->get();
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public static function updateLocalDate(string $date)
    {
        $cache = self::getCacheObject();

        $localtime = $cache->getItem('default_local_date');
        $localtime->set($date);
        $cache->save($localtime);
        return $localtime->get();
    }

    /**
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public static function resetLocalDateTime()
    {
        $cache = self::getCacheObject();
        return $cache->delete('default_local_time') && $cache->delete('default_local_date');

    }
}