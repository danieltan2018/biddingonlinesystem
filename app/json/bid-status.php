<?php
require_once '../include/common.php';
require_once 'admin_protect.php';
require_once '../include/round-process.php';

$result = isAdmin();
if (isAdmin() === true) {
    $result = bidStatus();
}

function bidStatus()
{
    $message = array();
    $ConfigDAO = new ConfigDAO();
    $round = $ConfigDAO->getRound();
    if (isEmpty($round)) {
        // Database empty or system reset
        $message[] = "do bootstrap first";
    }
    if (isset($_REQUEST["r"])) {
        $r_json = $_REQUEST["r"];
        $r = json_decode($r_json, true);
        if (!isset($r['course'])) {
            $message[] = "missing course";
        } elseif (empty($r['course'])) {
            $message[] = "blank course";
        }
        if (!isset($r['section'])) {
            $message[] = "missing section";
        } elseif (empty($r['section'])) {
            $message[] = "blank section";
        }
    } else {
        $message[] = "missing request";
    }

    if (isEmpty($message)) {
        // set variables
        $cid = $r['course'];
        $sid = $r['section'];

        // invalid course/section error
        $CourseDAO = new CourseDAO();
        $SectionDAO = new SectionDAO();
        if ($CourseDAO->retrieve($cid) == null) {
            $message[] = "invalid course";
        } elseif ($SectionDAO->retrieve($cid, $sid) == null) {
            $message[] = "invalid section";
        }
    }

    if (isEmpty($message)) {
        // Find number of vacancies
        $section = $SectionDAO->retrieve($cid, $sid);
        $numVacancies = $section->size;
        $BidResultDAO = new BidResultDAO;
        $numSuccess = $BidResultDAO->countSuccess($cid, $sid);
        if ($numSuccess > 0) {
            $numVacancies -= $numSuccess;
        }

        // Find minimum bid
        $BidDAO = new BidDAO();
        $BidResultDAO = new BidResultDAO();
        $MinBidDAO = new MinBidDAO();
        if ($round == 1) {
            $numBids = $BidDAO->countBids($cid, $sid);
            if ($numBids < $numVacancies) {
                $minBid = $BidDAO->lowestBid($cid, $sid);
            } elseif ($numBids == 0) {
                $minBid = 10.0;
            } else {
                $minBid = getClearingPrice($cid, $sid);
            }
        } elseif ($round % 2 == 0) {
            if ($numSuccess == 0) {
                $minBid = 10.0;
            } else {
                $minBid = $BidResultDAO->lowestSuccess($cid, $sid);
            }
        } else {
            $minBid = $MinBidDAO->getMinBid($cid, $sid);
        }

        // Get bids
        $StudentDAO = new StudentDAO();
        if ($round % 2 != 0) {
            // During bidding rounds
            $BidDAO = new BidDAO();
            $bid_arr = [];
            $bids = $BidDAO->bidStatus($cid, $sid);
            foreach ($bids as $bid) {
                $bid['amount'] = floatval($bid['amount']);
                $student = $StudentDAO->retrieve($bid['userid']);
                $bid['balance'] = floatval($student->edollar);
                if ($round == 1) {
                    $bid['status'] = "pending";
                } else {
                    $clearingPrice = getClearingPrice($cid, $sid);
                    if ($bid['amount'] >= $clearingPrice) {
                        $bid['status'] = "success";
                    } else {
                        $bid['status'] = "fail";
                    }
                }
                $bid_arr[] = $bid;
            }
        } else {
            // After bidding rounds
            $BidResultDAO = new BidResultDAO();
            $bid_arr = [];
            $bids = $BidResultDAO->bidStatus($cid, $sid);
            foreach ($bids as $bid) {
                $bid['amount'] = floatval($bid['amount']);
                $student = $StudentDAO->retrieve($bid['userid']);
                $bid['balance'] = floatval($student->edollar);
                if ($round < 4) {
                    $bid_arr[] = $bid;
                } elseif ($bid['status'] != "fail") {
                    // Do not include failed bids after round 2 closed
                    $bid_arr[] = $bid;
                }
            }
        }
        return ["status" => "success", "vacancy" => $numVacancies, "min-bid-amount" => floatval($minBid), "students" => $bid_arr];
    }
    return ["status" => "error", "message" => $message];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
