<?php

namespace Geeks\Pangolin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class ResetDatabaseController extends AbstractController
{

    protected $dropCommand = [
        'command' => 'doctrine:database:drop',
        '--force' => true,
        '--if-exists' => true
    ];
    protected $createCommand = [
        'command' => 'doctrine:database:create'
    ];
    protected $migrationCommand = [
        'command' => 'doctrine:migrations:migrate',
        '--no-interaction' => true
    ];

    protected $fixtureCommand = [
        'command' => 'doctrine:fixtures:load',
        '--no-interaction' => true
    ];

    protected $lastResponse = [];


    /**
     * @Route("/geeks/pangolin/reset", name="pangolin_reset_database")
     */
    public function index(KernelInterface $kernel): Response
    {

        $databaseName = $this->getDoctrine()->getConnection()->getDatabase();

        if(!$this->hasTempSuffix($databaseName)){
            return $this->response([
                'message' => 'Database name does not contain .temp suffix',
                'status' => false
            ], 500);
        }

        if (!$this->dropDatabase($kernel, true)) return $this->lastResponse();

        if (!$this->createDatabase($kernel, true)) return $this->lastResponse();

        if (!$this->runMigration($kernel, true)) return $this->lastResponse();

        if (!$this->runFixture($kernel, true)) return $this->lastResponse();

        return $this->response([
            'message' => 'All operations successfully completed',
            'status' => true
        ]);


    }

    protected function lastResponse()
    {
        return $this->response($this->getLastResponse());
    }

    protected function getLastResponse()
    {
        return $this->lastResponse;
    }

    protected function response(array $data, $code=200)
    {
        return $this->json($data, $code);
    }


    protected function dropDatabase($kernel, $onlyStatus=false)
    {

        return $this->runCommand($kernel, $this->dropCommand, $onlyStatus);
    }

    protected function createDatabase($kernel, $onlyStatus=false)
    {
        return $this->runCommand($kernel, $this->createCommand, $onlyStatus);
    }

    protected function runMigration($kernel, $onlyStatus=false)
    {
        return $this->runCommand($kernel, $this->migrationCommand, $onlyStatus);
    }

    protected function runFixture($kernel, $onlyStatus=false)
    {
        return $this->runCommand($kernel, $this->fixtureCommand, $onlyStatus);
    }

    protected function runCommand($kernel, $command,bool $onlyStatus=false)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput($command);

        $output = new BufferedOutput();
        $status = $application->run($input, $output);
        $response = [
            "status" => (!$status),
            "error"  =>  $output->fetch()
        ];
        $this->lastResponse = $response;
        if($onlyStatus) return (!$status);
        return $response;
    }

    protected function hasTempSuffix($dbName)
    {
        $suffix = ".temp";
        return substr($dbName, -strlen($suffix)) === $suffix;
    }




}
