<?php

namespace Geeks\Pangolin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SqlExecutionController extends AbstractController
{
    public function __invoke(Request $request)
    {
        return $this->index($request);
    }


    protected function index($request): Response
    {


        $postData = file_get_contents('php://input');

        $decodedString = json_decode($postData);
        
        $queries = $decodedString->data;

        if(!isset($queries) or empty($queries)){
            return $this->json([
                'message' => "You have provided an empty data array in your request",
                'status' => false
            ],401);
        }
        foreach ($queries as $sql){
            $this->executeSqlQuery($sql->log);
        }
        return $this->json([
            'message' => 'All queries have been executed successfully',
            'status' => true
        ]);

    }


    protected function executeSqlQuery($sql)
    {
        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
