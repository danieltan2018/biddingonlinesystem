<?php
require_once '../include/common.php';
require_once 'admin_protect.php';
require_once '../include/bid-process.php';

$result = isAdmin();
if (isAdmin() === true) {
    $result = updateBid();
}

function updateBid()
{
    $message = array();
    if (isset($_REQUEST["r"])) {
        $r_json = $_REQUEST["r"];
        $r = json_decode($r_json, true);
        if (!isset($r['amount'])) {
            $message[] = "missing amount";
        } elseif (empty($r['amount'])) {
            $message[] = "blank amount";
        }
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
        if (!isset($r['userid'])) {
            $message[] = "missing userid";
        } elseif (empty($r['userid'])) {
            $message[] = "blank userid";
        }
    } else {
        $message[] = "missing request";
    }

    if (isEmpty($message)) {
        // round ended error
        $ConfigDAO = new ConfigDAO();
        $round = $ConfigDAO->getRound();
        $rounded = round($round / 2, 0, PHP_ROUND_HALF_UP);
        if ($round % 2 == 0) {
            $message[] = "round ended";
        }
    }

    if (isEmpty($message)) {
        $StudentDAO = new StudentDAO();
        $CourseDAO = new CourseDAO();
        $SectionDAO = new SectionDAO();
        $BidDAO = new BidDAO();
        $MinBidDAO = new MinBidDAO;

        // Input Validation
        if ($StudentDAO->retrieve($r['userid']) == null) {
            $message[] = "invalid userid";
        }
        if (floatval($r['amount']) < 10 || !preg_match('/^\d+(\.(\d){1,2})?$/', $r['amount'])) {
            $message[] = "invalid amount";
        }
        if ($CourseDAO->retrieve($r['course']) == null) {
            $message[] = "invalid course";
        } elseif ($SectionDAO->retrieve($r['course'], $r['section']) == null) {
            $message[] = "invalid section";
        }
        sort($message);
    }

    // Logical Validation
    if (isEmpty($message)) {
        $minbid = $MinBidDAO->getMinBid($r['course'], $r['section']);
        $previous_bid = $BidDAO->retrieveBid($r['userid'], $r['course']);
        $stu = $StudentDAO->retrieve($r['userid']);
        if ($previous_bid != null) {
            $previous_amount = $previous_bid->amount;
            $BidDAO->remove($r['userid'], $r['course']);
            $stu->edollar += $previous_amount;
            $StudentDAO->update($stu);
        }
        $invalid_bid = checkbid($r['userid'], $r['amount'], $r['course'], $r['section']);
        if (!isEmpty($invalid_bid)) {
            foreach ($invalid_bid as $bid_error) {
                $message[] = $bid_error;
            }
        }
        $message = array_diff($message, ["not enough e-dollar"]);
        if ($stu->edollar - $r['amount'] < 0) {
            $message[] = "insufficient e$";
        }
    
        if ($previous_bid != null && !isEmpty($message)) {
            $BidDAO->add($previous_bid);
            $stu->edollar -= $previous_amount;
            $StudentDAO->update($stu);
        }
        sort($message);
    }

    // Perform Update
    if (isEmpty($message)) {
        if ($BidDAO->replace(new Bid($r['userid'], $r['amount'], $r['course'], $r['section']))) {
            $stu->edollar -= $r['amount']; // Deduct current bid
            $StudentDAO->update($stu);
            // Change MinBid if round 2
            if ($rounded == 2) {
                // Find number of vacancies
                $section = $SectionDAO->retrieve($r['course'], $r['section']);
                $numVacancies = $section->size;
                $BidResultDAO = new BidResultDAO;
                $numSuccess = $BidResultDAO->countSuccess($r['course'], $r['section']);
                if ($numSuccess > 0) {
                    $numVacancies -= $numSuccess;
                }
                // Get sorted array of bid amounts
                $bids = $BidDAO->retrieveClassBids($r['course'], $r['section']);
                $AmountArr = array_column($bids, 'amount');
                // Update MinBid if more bids than vacancies
                if ($numVacancies <= count($AmountArr)) {
                    $newMinBid = $AmountArr[$numVacancies - 1] + 1;
                    if ($newMinBid > $minbid) { // MinBid cannot go down
                        $MinBidDAO->updateMinBid($r['course'], $r['section'], $newMinBid);
                    }
                }
            }
            return ["status" => "success"];
        }
    }
    return ["status" => "error", "message" => $message];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
