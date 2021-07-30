<?php

namespace Geeks\Pangolin\Controller;

use Geeks\Pangolin\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class LogController extends AbstractController
{

    public function __invoke(KernelInterface $kernel)
    {
        $logs =  $this->getDoctrine()->getRepository(Log::class)->findAll();

        $manager = $this->getDoctrine()->getManager();

        $resultLog = [];
        foreach ($logs as $log) {
            $resultLog[]['log'] = $log->getDbalQuery();
            $manager->remove($log);
        }

        $manager->flush();

        return ['data' => $resultLog];
    }
    
}
