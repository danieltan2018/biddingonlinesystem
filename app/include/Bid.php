<?php

class Bid
{
    public $userid;
    public $amount;
    public $cid;
    public $sid;

    public function __construct($userid = '', $amount = '', $cid = '', $sid = '')
    {
        $this->userid = $userid;
        $this->amount = $amount;
        $this->cid = $cid;
        $this->sid = $sid;
    }
}
