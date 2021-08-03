<?php


namespace Geeks\Pangolin\Bridge;

interface IEmailTemplate
{

    public function getBody();
    public function setBody();
    public function getKey();
    public function setKey();
    public function getMandatoryPlaceholders();
    public function setMandatoryPlaceholders();
    public function getSubject();
    public function setSubject();
    
}