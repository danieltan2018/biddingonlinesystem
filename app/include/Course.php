<?php

class Course
{
    public $cid;
    public $school;
    public $title;
    public $description;
    public $examdate;
    public $examstart;
    public $examend;

    public function __construct($cid = '', $school = '', $title = '', $description = '', $examdate = '', $examstart = '', $examend = '')
    {
        $this->cid = $cid;
        $this->school = $school;
        $this->title = $title;
        $this->description = $description;
        $this->examdate = $examdate;
        $this->examstart = $examstart;
        $this->examend = $examend;
    }
}
