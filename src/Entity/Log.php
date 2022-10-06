<?php

namespace Geeks\Pangolin\Entity;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use Geeks\Pangolin\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;
use Geeks\Pangolin\Controller\LogController;
use Geeks\Pangolin\Controller\ResetDatabaseController;
use Geeks\Pangolin\Dto\LogInput;
use Geeks\Pangolin\Controller\SqlExecutionController;
use Geeks\Pangolin\Controller\LocaltimeController;
/**
 * @ORM\Entity (repositoryClass=LogRepository::class)
 */
#[ApiResource(operations: [new GetCollection(uriTemplate: '/get-db-changes', controller: LogController::class), new GetCollection(uriTemplate: '/local-date', controller: LocaltimeController::class, openapiContext: ['parameters' => [['name' => 'date', 'in' => 'query', 'required' => true, 'description' => '', 'schema' => ['type' => 'string']], ['name' => 'time', 'in' => 'query', 'description' => '', 'schema' => ['type' => 'string']]]]), new GetCollection(uriTemplate: '/db-restart', controller: ResetDatabaseController::class), new Post(uriTemplate: '/db-run-changes', controller: SqlExecutionController::class, openapiContext: ['requestBody' => ['content' => ['application/json' => ['schema' => []]]]])], paginationEnabled: false, input: LogInput::class)]
class Log
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeName;
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $payload;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $columnId;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $actionName;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $dbalQuery;
    public function getId() : ?int
    {
        return $this->id;
    }
    public function getTypeName() : ?string
    {
        return $this->typeName;
    }
    public function setTypeName(string $typeName) : self
    {
        $this->typeName = $typeName;
        return $this;
    }
    public function getUpdatedAt() : ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt) : self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    public function getCreatedAt() : ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTimeImmutable $createdAt) : self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function getPayload() : ?string
    {
        return $this->payload;
    }
    public function setPayload(string $payload) : self
    {
        $this->payload = $payload;
        return $this;
    }
    public function getColumnId()
    {
        return $this->columnId;
    }
    public function setColumnId(string $columnId) : self
    {
        $this->columnId = $columnId;
        return $this;
    }
    public function getActionName() : ?string
    {
        return $this->actionName;
    }
    public function setActionName(string $actionName) : self
    {
        $this->actionName = $actionName;
        return $this;
    }
    public function getDbalQuery() : ?string
    {
        return $this->dbalQuery;
    }
    public function setDbalQuery(?string $dbalQuery) : self
    {
        $this->dbalQuery = $dbalQuery;
        return $this;
    }
}
