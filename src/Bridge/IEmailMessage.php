<?php


namespace Geeks\Pangolin\Bridge;

interface IEmailMessage
{

    public function getBody();
    public function setBody();
    public function getSendableDate();
    public function setSendableDate();
    public function getHtml();
    public function setHtml();
    public function getFromAddress();
    public function setFromAddress();
    public function getFromName();
    public function setFromName();
    public function getReplyToAddress();
    public function setReplyToAddress();
    public function getReplyToName();
    public function setReplyToName();
    public function getSubject();
    public function setSubject();
    public function getTo();
    public function setTo();
    public function getAttachments();
    public function setAttachments();
    public function getBcc();
    public function setBcc();
    public function getCc();
    public function setCc();
    public function getRetries();
    public function setRetries();
    public function getVCalendarView();
    public function setVCalendarView();
}