<?php

namespace Geeks\Pangolin\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Geeks\Pangolin\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;
use Geeks\Pangolin\Controller\LogController;
use Geeks\Pangolin\Controller\ResetDatabaseController;
use Geeks\Pangolin\Dto\LogInput;
use Geeks\Pangolin\Controller\SqlExecutionController;

/**
 * @ApiResource(
 *     attributes={"pagination_enabled"=false},
 *     input=LogInput::class,
 *     collectionOperations={
 *          "get_logs"= {
 *            "method"="GET",
 *            "path"="/get-db-changes",
 *            "controller"=LogController::class,
 *          },
 *          "get_db_reset"= {
 *            "method"="GET",
 *            "path"="/db-restart",
 *            "controller"=ResetDatabaseController::class,
 *           },
 *          "get_db_run_changes"= {
 *            "method"="POST",
 *            "path"="/db-run-changes",
 *            "controller"=SqlExecutionController::class,
 *             "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "application/json"={
 *                             "schema"={
 *
 *                             }
 *                         }
 *                     }
 *                 }
 *             }

 *           },
 *     },
 *     itemOperations={
 *     }
 * )
 * @ORM\Entity(repositoryClass=LogRepository::class)
 */
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    public function setTypeName(string $typeName): self
    {
        $this->typeName = $typeName;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function getColumnId()
    {
        return $this->columnId;
    }

    public function setColumnId(string $columnId): self
    {
        $this->columnId = $columnId;

        return $this;
    }

    public function getActionName(): ?string
    {
        return $this->actionName;
    }

    public function setActionName(string $actionName): self
    {
        $this->actionName = $actionName;

        return $this;
    }

    public function getDbalQuery(): ?string
    {
        return $this->dbalQuery;
    }

    public function setDbalQuery(?string $dbalQuery): self
    {
        $this->dbalQuery = $dbalQuery;

        return $this;
    }
}
