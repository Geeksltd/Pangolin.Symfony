<?php


namespace Geeks\Pangolin\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Persistence\ManagerRegistry;

class OutboxController extends AbstractController
{

    private $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }
   public function index()
   {

       $em = $this->doctrine->getManager();
       $entities = $em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

       $implementsIModule = [];
       foreach($entities as $entity) {
           $reflect = new \ReflectionClass($entity);
           if($reflect->implementsInterface('Geeks\Pangolin\Bridge\IEmailMessage'))
               $implementsIModule[] = $entity;
       }

       if(!empty($implementsIModule)){
           $entity = $implementsIModule[0];
           $metadata = $em->getClassMetadata($entity);
           $fields = $metadata->getFieldNames();
           $records = $em->getRepository($entity)->findAll();
           
           return $this->render("@Pangolin/outbox/index.html.twig", [
               'records' => $records,
               'fields'  => $fields
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