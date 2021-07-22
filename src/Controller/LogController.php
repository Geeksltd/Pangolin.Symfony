<?php

namespace Geeks\Pangolin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class LogController extends AbstractController
{

    /**
     * @Route("cmd/get-db-changes", name="pangolin_logs")
     */
    public function index(KernelInterface $kernel): Response
    {
        $cache = new FilesystemAdapter();
        $logs = $cache->get('all_cached_logs', function ($item){
            return $item->get();
        });
        $cache->delete("all_cached_logs");
        return $this->json([
            'message' => $logs,
            'status' => true
        ]);
    }

}
