<?php

namespace Geeks\Pangolin\Logger;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class SimpleLogger implements SQLLogger
{

    protected $types = [
        "UPDATE", "INSERT", "CREATE", "ALTER"
        // "SELECT", "DELETE"
    ];
    protected $em;
    protected $cache;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->cache = new FilesystemAdapter();
    }


    public function startQuery($sql, array $params = null, array $types = null)
    {
        $platform = $this->em->getConnection()->getDatabasePlatform();
        if ($this->isLoggable($sql, $this->types, true)) {
            if (!empty($params)) {
                foreach ($params as $key => $param) {
                    $type = Type::getType($types[$key]);
                    $value = $type->convertToDatabaseValue($param, $platform);
                    $sql = join(var_export($value, true), explode('?', $sql, 2));
                }
                $arrayLog = [];
                $logItem = $this->cache->getItem('all_cached_logs');
                $currentValue = $logItem->get();
                if($currentValue){
                    $arrayLog = json_decode($currentValue);
                }
                array_push($arrayLog, $sql);
                $newValue =  json_encode($arrayLog);
                $logItem->set($newValue);
                $this->cache->save($logItem);
            }
        }

    }

    protected function isLoggable($string, array $search, $caseInsensitive = false)
    {
        $exp = '/' . implode('|',
                array_map('preg_quote', $search))
            . ($caseInsensitive ? '/i' : '/');
        return preg_match($exp, $string);
    }


    public function stopQuery()
    {
    }


}
