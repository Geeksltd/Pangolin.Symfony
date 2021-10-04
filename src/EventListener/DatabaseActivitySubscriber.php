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

    private $blackRoutes = [
        '/cmd/db-restart'
    ];

    public function __construct(SerializerInterface $serializer, DebugStack $logger)
    {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->currentPath  = $_SERVER['REQUEST_URI'] ?? "/";

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
        if(!$this->checkBlacklistRoutes()) return;

        $entity = $args->getObject();
        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if ($entity instanceof PostLogBridge) {
            $log = new Log();
            $log->setPayload($this->serializer->serialize($entity, 'json', ["groups" => "log_read"]));
            $log->setTypeName(get_class($entity));
            $log->setCreatedAt(new \DateTimeImmutable());
            $log->setActionName('post');
            $params = end($this->logger->queries)['params'];
            $sql = end($this->logger->queries)['sql'];
            $types = end($this->logger->queries)['types'];
            $databaseType = $args->getObjectManager()->getConnection()->getDatabasePlatform();
            foreach ($params as $key => $param) {
                $typeData = $types[$key];
                if ($typeData === 'ulid') {
                    $typeData = 'string';
                    $param = (string)$param;
                }
                $type = Type::getType($typeData);
                $value = $type->convertToDatabaseValue($param, $databaseType);
                $sql = join(var_export($value, true), explode('?', $sql, 2));
            }
            $log->setDbalQuery($sql);
            $args->getObjectManager()->persist($log);
            $args->getObjectManager()->flush();
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        if(!$this->checkBlacklistRoutes()) return;
        $entity = $args->getObject();
        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if ($entity instanceof DeleteLogBridge) {
            $log = new Log();
            $log->setPayload($this->serializer->serialize($entity, 'json', ["groups" => "log_read"]));
            $log->setTypeName(get_class($entity));
            $log->setCreatedAt(new \DateTimeImmutable());
            $log->setActionName('remove');
            $params = end($this->logger->queries)['params'];
            $sql = end($this->logger->queries)['sql'];
            $types = end($this->logger->queries)['types'];
            $databaseType = $args->getObjectManager()->getConnection()->getDatabasePlatform();
            foreach ($params as $key => $param) {
                $typeData = $types[$key];
                if ($typeData === 'ulid') {
                    $typeData = 'string';
                    $param = (string)$param;
                }
                $type = Type::getType($typeData);
                $value = $type->convertToDatabaseValue($param, $databaseType);
                $sql = join(var_export($value, true), explode('?', $sql, 2));
            }
            $log->setDbalQuery($sql);
            $args->getObjectManager()->persist($log);
            $args->getObjectManager()->flush();
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        if(!$this->checkBlacklistRoutes()) return;
        $entity = $args->getObject();
        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if ($entity instanceof UpdateLogBridge) {
            $log = new Log();
            $log->setPayload($this->serializer->serialize($entity, 'json', ["groups" => "log_read"]));
            $log->setTypeName(get_class($entity));
            $log->setCreatedAt(new \DateTimeImmutable());
            $log->setActionName('update');
            $params = end($this->logger->queries)['params'];
            $sql = end($this->logger->queries)['sql'];
            $types = end($this->logger->queries)['types'];
            $databaseType = $args->getObjectManager()->getConnection()->getDatabasePlatform();
            foreach ($params as $key => $param) {
                $typeData = $types[$key];
                if ($typeData === 'ulid') {
                    $typeData = 'string';
                    $param = (string)$param;
                }
                $type = Type::getType($typeData);
                $value = $type->convertToDatabaseValue($param, $databaseType);
                $sql = join(var_export($value, true), explode('?', $sql, 2));
            }
            $log->setDbalQuery($sql);
            $args->getObjectManager()->persist($log);
            $args->getObjectManager()->flush();
        }
    }

    protected function checkBlacklistRoutes()
    {
        if(in_array($this->currentPath, $this->blackRoutes)) return false;
    }
}
