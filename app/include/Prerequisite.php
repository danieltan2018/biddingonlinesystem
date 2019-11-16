<?php

class Prerequisite
{
    public $cid;
    public $prerequisite;

    public function __construct($cid = '', $prerequisite = '')
    {
        $this->cid = $cid;
        $this->prerequisite = $prerequisite;
    }
}
