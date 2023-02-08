<?php

namespace Geeks\Pangolin\EventListener;

use Doctrine\DBAL\Types\Type;
use Geeks\Pangolin\Bridge\DeleteLogBridge;
use Geeks\Pangolin\Bridge\PostLogBridge;
use Geeks\Pangolin\Bridge\UpdateLogBridge;
use Geeks\Pangolin\Entity\Log;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bridge\Doctrine\Logger\DbalLogger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;

class DatabaseActivitySubscriber implements EventSubscriber
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var DbalLogger
     */
    private $logger;

    private string $currentPath;

    private $env;
    private $runQueries = [];

    private $blackRoutes = [
        '/cmd/db-restart', '/cmd/db-restart?runner=Sanity'
    ];

    public function __construct(SerializerInterface $serializer, DebugStack $logger, $env)
    {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->currentPath = $_SERVER['REQUEST_URI'] ?? "/";
        $this->env = $env;

    }
    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        if (!$this->isDevelopment()) return;
        if ($this->checkBlacklistRoutes()) return;
        $entity = $args->getObject();

        if ($entity instanceof UpdateLogBridge) {
            $queryEntity = count($this->logger->queries);
            $startTransactionKey = array_search('"START TRANSACTION"', array_column($this->logger->queries, 'sql')) + 2;
            if($startTransactionKey && $startTransactionKey > 1) {
                $em = $args->getObjectManager();
                for ($i = $startTransactionKey; $i <= $queryEntity; $i++) {
                    $query = $this->logger->queries[$i];
                    $sql = $this->getDbalQuery($query, $args);
                    if(!str_contains($sql, 'INTO log') && !str_contains($sql, 'SELECT t0')) {
                        if (!$em->getRepository(Log::class)->findOneBy(['dbalQuery' => $sql])) {
                            if(str_contains($sql, 'INSERT INTO')) {
                                $this->insertLog($args, $entity, 'post', $query);
                            }
                            else if(str_contains($sql, 'UPDATE')) {
                                $this->insertLog($args, $entity, 'update', $query);
                            }
                            else if(str_contains($sql, 'DELETE')) {
                                $this->insertLog($args, $entity, 'remove', $query);
                            }
                        }
                    }
                }
            }
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        if (!$this->isDevelopment()) return;
        if ($this->checkBlacklistRoutes()) return;
        $entity = $args->getObject();

        if ($entity instanceof UpdateLogBridge) {
            $queryEntity = count($this->logger->queries);
            $startTransactionKey = array_search('"START TRANSACTION"', array_column($this->logger->queries, 'sql')) + 2;
            if($startTransactionKey && $startTransactionKey > 1) {
                $em = $args->getObjectManager();
                for ($i = $startTransactionKey; $i <= $queryEntity; $i++) {
                    $query = $this->logger->queries[$i];
                    $sql = $this->getDbalQuery($query, $args);
                    if(!str_contains($sql, 'INTO log') && !str_contains($sql, 'SELECT t0')) {
                        if (!$em->getRepository(Log::class)->findOneBy(['dbalQuery' => $sql])) {
                            if(str_contains($sql, 'INSERT INTO')) {
                                $this->insertLog($args, $entity, 'post', $query);
                            }
                            else if(str_contains($sql, 'UPDATE')) {
                                $this->insertLog($args, $entity, 'update', $query);
                            }
                            else if(str_contains($sql, 'DELETE')) {
                                $this->insertLog($args, $entity, 'remove', $query);
                            }
                        }
                    }
                }
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        if (!$this->isDevelopment()) return;
        if ($this->checkBlacklistRoutes()) return;
        $entity = $args->getObject();

        if ($entity instanceof UpdateLogBridge) {
            $queryEntity = count($this->logger->queries);
            $startTransactionKey = array_search('"START TRANSACTION"', array_column($this->logger->queries, 'sql')) + 2;
            if($startTransactionKey && $startTransactionKey > 1) {
                $em = $args->getObjectManager();
                for ($i = $startTransactionKey; $i <= $queryEntity; $i++) {
                    $query = $this->logger->queries[$i];
                    $sql = $this->getDbalQuery($query, $args);
                    if(!str_contains($sql, 'INTO log') && !str_contains($sql, 'SELECT t0')) {
                        if (!$em->getRepository(Log::class)->findOneBy(['dbalQuery' => $sql])) {
                            if(str_contains($sql, 'INSERT INTO')) {
                                $this->insertLog($args, $entity, 'post', $query);
                            }
                            else if(str_contains($sql, 'UPDATE')) {
                                $this->insertLog($args, $entity, 'update', $query);
                            }
                            else if(str_contains($sql, 'DELETE')) {
                                $this->insertLog($args, $entity, 'remove', $query);
                            }
                        }
                    }
                }
            }
        }
    }

    protected function checkBlacklistRoutes()
    {
        return (in_array($this->currentPath, $this->blackRoutes));
    }

    protected function isDevelopment()
    {
        return ($this->env == '"dev"');
    }

    private function insertLog($args, $entity, $type, $query)
    {
        $log = new Log();
        $log->setPayload($this->serializer->serialize($entity, 'json', ["groups" => "log_read"]));
        $log->setTypeName(get_class($entity));
        $log->setCreatedAt(new \DateTimeImmutable());
        $log->setActionName($type);
        $sql = $this->getDbalQuery($query, $args);
        $log->setDbalQuery($sql);
        $args->getObjectManager()->persist($log);
        $args->getObjectManager()->flush();
    }
    private function getDbalQuery($query, $args) {
        $sql = $query['sql'];
        $types = $query['types'];
        $params = $query['params'] ?? [];
        $databaseType = $args->getObjectManager()->getConnection()->getDatabasePlatform();
        foreach ($params as $key => $param) {
            if (isset($types[$key])) {
                $typeData = $types[$key];
                if (strpos($typeData, 'uid') > -1) {
                    $typeData = 'string';
                    $param = (string)$param;
                }
                $value = '';
                if($typeData != 2) {
                    $type = Type::getType($typeData);
                    $value = $type->convertToDatabaseValue($param, $databaseType);
                }
                if($value == '') {
                    $exportVal = 'NULL';
                } else {
                    $exportVal = var_export($value, true);
                }
                $sql = join($exportVal, explode('?', $sql, 2));
            }
        }
        return $sql;
    }
}
