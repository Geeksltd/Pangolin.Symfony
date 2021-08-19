<?php

namespace Geeks\Pangolin\Controller;

use Geeks\Pangolin\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Geeks\Pangolin\Lib\LocaltimeHelper;

class LocaltimeController extends AbstractController
{

    public function __invoke(Request $request)
    {

        $date = $request->query->get('date');
        $time = $request->query->get('time');

        if(!isset($date)){
            return $this->json([
                'message' => 'Date parameter needs to be defined.',
                'status' => false,
            ],400);
        }

        $dateTime = isset($time) ? $date. " ". $time : $date;
        $data = LocaltimeHelper::updateLocaltime($dateTime);
        return $this->json([
            'message' => 'Local time has been updated.',
            'status' => true,
            'data' => $data
        ]);


    }


    
}
