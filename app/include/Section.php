<?php

class Section
{

    public $cid;
    public $sid;
    public $dayweek;
    public $starttime;
    public $endtime;
    public $instructor;
    public $venue;
    public $size;

    public function __construct($cid = '', $sid = '', $dayweek = '', $starttime = '', $endtime = '', $instructor = '', $venue = '', $size = '')
    {
        $this->cid = $cid;
        $this->sid = $sid;
        $this->dayweek = $dayweek;
        $this->starttime = $starttime;
        $this->endtime = $endtime;
        $this->instructor = $instructor;
        $this->venue = $venue;
        $this->size = $size;
    }
}
