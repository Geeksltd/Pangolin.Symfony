<?php


namespace Geeks\Pangolin\Bridge;

use Symfony\Component\Uid\Ulid;

interface DeleteLogBridge
{
    public function getId(): Ulid;
    public function setId($id): void;
}