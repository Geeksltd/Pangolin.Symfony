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
            Events::postRemove,
            Events::postUpdate,
        ];
    }
    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args): void
    {
        if (!$this->isDevelopment()) return;
        if ($this->checkBlacklistRoutes()) return;

        $entity = $args->getObject();
        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if ($entity instanceof PostLogBridge) {
            $queryEntity = count($this->logger->queries);
            $this->insertLog($args, $entity, 'post', $this->logger->queries[$queryEntity]);
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        if (!$this->isDevelopment()) return;
        if ($this->checkBlacklistRoutes()) return;
        $entity = $args->getObject();
        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if ($entity instanceof DeleteLogBridge) {
            $queryEntity = count($this->logger->queries);
            $this->insertLog($args, $entity, 'remove', $this->logger->queries[$queryEntity]);
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        if (!$this->isDevelopment()) return;
        if ($this->checkBlacklistRoutes()) return;
        $entity = $args->getObject();
        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if ($entity instanceof UpdateLogBridge) {
            $queryEntity = count($this->logger->queries) - 1;
            $queryAudit = count($this->logger->queries);
            $this->insertLog($args, $entity, 'update', $this->logger->queries[$queryEntity]);
            if(strpos($this->logger->queries[$queryAudit]['sql'], 'audit') > -1) {
                $this->insertLog($args, $entity, 'post', $this->logger->queries[$queryAudit]);
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
        $params = $query['params'] ?? [];
        $sql = $query['sql'];
        $types = $query['types'];
        $databaseType = $args->getObjectManager()->getConnection()->getDatabasePlatform();
        foreach ($params as $key => $param) {
            if (isset($types[$key])) {
                $typeData = $types[$key];
                if (strpos($typeData, 'uid') > -1) {
                    $typeData = 'string';
                    $param = (string)$param;
                }
                $type = Type::getType($typeData);
                $value = $type->convertToDatabaseValue($param, $databaseType);

                if($value == '') {
                    $exportVal = 'NULL';
                } else {
                    $exportVal = var_export($value, true);
                }
                $sql = join($exportVal, explode('?', $sql, 2));
            }
        }
        $log->setDbalQuery($sql);
        $args->getObjectManager()->persist($log);
        $args->getObjectManager()->flush();
    }
}
