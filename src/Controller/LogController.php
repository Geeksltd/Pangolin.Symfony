<?php

namespace Geeks\Pangolin\Controller;

use Geeks\Pangolin\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Doctrine\Persistence\ManagerRegistry;

class LogController extends AbstractController
{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }
    public function __invoke(KernelInterface $kernel)
    {
        $logs =  $this->doctrine->getRepository(Log::class)->findAll();

        $manager = $this->doctrine->getManager();

        $resultLog = [];
        foreach ($logs as $log) {
            $resultLog[]['log'] = $log->getDbalQuery();
            $manager->remove($log);
        }

        $manager->flush();

        return ['data' => $resultLog];
    }

}
