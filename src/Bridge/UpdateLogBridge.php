<?php


namespace Geeks\Pangolin\Bridge;

//use Doctrine\DBAL\Types\GuidType;

interface UpdateLogBridge
{
    public function getId();
    public function setId($id): void;
}