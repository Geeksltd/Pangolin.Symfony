<?php


namespace Geeks\Pangolin\Bridge;

use Doctrine\DBAL\Types\GuidType;

interface UpdateLogBridge
{
    public function getId(): GuidType;
    public function setId($id): void;
}