<?php


namespace Geeks\Pangolin\Dto;


use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LogInput
 * @package Geeks\Pangolin\Dto
 */
class LogInput
{

    /**
     * @SerializedName(serializedName="LogInput.name")
     * @Groups({"xml"})
     * @ApiProperty(attributes={"LogInput.name"})
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Groups({"xml"})
     */
    private $dataBaseCommand;

    private $url;

    private $files;

    private $secureFiles;

    private $cookies;



    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDataBaseCommand()
    {
        return $this->dataBaseCommand;
    }

    /**
     * @param mixed $dataBaseCommand
     */
    public function setDataBaseCommand($dataBaseCommand): void
    {
        $this->dataBaseCommand = $dataBaseCommand;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files): void
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getSecureFiles()
    {
        return $this->secureFiles;
    }

    /**
     * @param mixed $secureFiles
     */
    public function setSecureFiles($secureFiles): void
    {
        $this->secureFiles = $secureFiles;
    }

    /**
     * @return mixed
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param mixed $cookies
     */
    public function setCookies($cookies): void
    {
        $this->cookies = $cookies;
    }


}