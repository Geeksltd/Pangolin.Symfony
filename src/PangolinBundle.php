<?php

namespace Geeks\Pangolin;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class PangolinBundle extends Bundle
{

    public function load(array $configs, ContainerBuilder $container)
    {

        $this->addAnnotatedClassesToCompiles([
            "Geeks\Pangolin\Controller\ResetDatabaseController",
        ]);
    }



}