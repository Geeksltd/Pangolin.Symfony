<?php


namespace Geeks\Pangolin\Bridge;

interface IEmailTemplate
{

    public function getBody();
    public function setBody( $body);
    public function getKey();
    public function setKey( $key);
    public function getMandatoryPlaceholders();
    public function setMandatoryPlaceholders( $mandatoryPlaceholders);
    public function getSubject();
    public function setSubject( $subject);
    
}