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

    public function index(KernelInterface $kernel): Response
    {
        $cache = new FilesystemAdapter();
        $logItem = $cache->getItem('all_cached_logs');
        $currentValue = $logItem->get();
        $newArray = null;
        if($currentValue){
            $arrayLog = json_decode($currentValue, true);
            if ($arrayLog && is_array($arrayLog) && count($arrayLog)){
                foreach ($arrayLog as $log){
                    $newArray[]['log'] = $log;
                }
            }
        }
        $cache->delete("all_cached_logs");
        return $this->createJson($newArray);
    }


    protected function createJson($data)
    {
        return $this->json([
            'data' => $data,
        ]);
    }

}
