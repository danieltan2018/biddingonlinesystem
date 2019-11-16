<?php

class CourseCompleted
{
    public $userid;
    public $cid;

    public function __construct($userid = ' ', $cid = '')
    {
        $this->userid = $userid;
        $this->cid = $cid;
    }
}
