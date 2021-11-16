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


        if(empty(trim($date)) and empty(trim($time))){
            LocaltimeHelper::resetLocalDateTime();
        }

        if(!empty(trim($date))){
           LocaltimeHelper::updateLocalDate($date);
        }

        if(!empty(trim($time))){
            LocaltimeHelper::updateLocaltime($time);
        }

        return $this->json([
            'message' => 'Local datetime has been updated.',
            'status' => true,
            'data' => LocaltimeHelper::getLocalDateTime()
        ]);

    }


    
}
