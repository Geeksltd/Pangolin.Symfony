<?php


namespace Geeks\Pangolin\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;

class OutlookController extends AbstractController
{

    
   public function index()
   {


       $em = $this->getDoctrine()->getManager();
       $entities = $em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

       $implementsIModule = [];
       foreach($entities as $entity) {
           $reflect = new \ReflectionClass($entity);
           if($reflect->implementsInterface('Geeks\Pangolin\Bridge\IEmailMessage'))
               $implementsIModule[] = $entity;
       }

       if(!empty($implementsIModule)){

           $entity = $implementsIModule[0];
           $object = new $entity();
           $records = $em->getRepository($entity)->findAll();

           return $this->json([
              "data" => $records
           ]);

       }
       else {
           return $this->json([
               "status" => false,
               "message" => "There is no entity implementing such Interface"
           ]);
       }



   }

}