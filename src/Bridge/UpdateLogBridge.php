<?php


namespace Geeks\Pangolin\Bridge;

//use Doctrine\DBAL\Types\GuidType;

interface UpdateLogBridge
{
    public function getId(): string;
    public function setId($id): void;
}