<?php

class BidResult
{
    public $round;
    public $cid;
    public $sid;
    public $ranking;
    public $userid;
    public $amount;
    public $state;

    public function __construct($round = '', $cid = '', $sid = '', $ranking = '', $userid = '', $amount = '', $state = '')
    {
        $this->round = $round;
        $this->cid = $cid;
        $this->sid = $sid;
        $this->ranking = $ranking;
        $this->userid = $userid;
        $this->amount = $amount;
        $this->state = $state;
    }
}
