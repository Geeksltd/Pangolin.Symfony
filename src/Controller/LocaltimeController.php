<?php

namespace Geeks\Pangolin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Geeks\Pangolin\Lib\LocaltimeHelper;

class LocaltimeController extends AbstractController
{

    public function __invoke(Request $request)
    {

        $date = $request->query->get('date');
        $time = $request->query->get('time');

        if($date){
           LocaltimeHelper::updateLocalDate($date);
        }

        if($time){
            LocaltimeHelper::updateLocaltime($time);
        }

        return $this->json([
            'message' => 'Local datetime has been updated.',
            'status' => true,
            'data' => LocaltimeHelper::getLocalDateTime()
        ]);

    }


    
}
