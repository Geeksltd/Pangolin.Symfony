<?php

namespace Geeks\Pangolin\EventListener;


use ApiPlatform\Core\EventListener\EventPriorities;
use Geeks\Pangolin\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ClearLogs implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['clearLogs', EventPriorities::POST_SERIALIZE],
        ];
    }

    public function clearLogs(ViewEvent $event)
    {
        /**
         * @var Paginator $entity
         */
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $entity = json_decode($entity, true);

        if ($entity && $entity['@context'] == "/api/contexts/Log" && Request::METHOD_GET === $method) {

                    $logs = $this->manager->getRepository(Log::class)->findAll();

                    foreach ($logs as $log) {
                        $this->manager->remove($log);
                    }

                    $this->manager->flush();
        }
    }
}
