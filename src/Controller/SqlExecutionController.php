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

        $xml = simplexml_load_string($postData);

        $value = $xml->attributes()->DataBaseCommand[0];

        $string = $value->__toString();
        $decodedString = json_decode($string);
        
        $queries = $decodedString->data;

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
