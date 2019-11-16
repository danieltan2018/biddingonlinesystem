<?php
require_once 'common.php';

function startRound()
{
    $ConfigDAO = new ConfigDAO();
    $round = $ConfigDAO->getRound();

    if ($round >= 4) {
        $result = [
            "status" => "error",
            "message" => ["round 2 ended"]
        ];
    } elseif ($round % 2 == 0) {
        $round += 1;
        $ConfigDAO->setRound($round);
        $rounded = round($round / 2, 0, PHP_ROUND_HALF_UP);
        $result = [
            "status" => "success",
            "round" => $rounded
        ];
    } elseif ($round % 2 != 0) {
        $result = [
            "status" => "success",
            "round" => $round
        ];
    }
    return $result;
}

function stopRound()
{
    $BidDAO = new BidDAO();
    $ConfigDAO = new ConfigDAO();
    $round = $ConfigDAO->getRound();

    if ($round % 2 != 0) {
        $classes = $BidDAO->retrieveClasses();
        foreach ($classes as $class) {
            $cid = $class['cid'];
            $sid = $class['sid'];
            roundClear($cid, $sid);
        }
        $round += 1;
        $ConfigDAO->setRound($round);
        $result = ["status" => "success"];
    } else {
        $result = [
            "status" => "error",
            "message" => ["round already ended"]
        ];
    }
    return $result;
}

function roundClear($cid, $sid)
{
    $SectionDAO = new SectionDAO();
    $BidDAO = new BidDAO();
    $BidResultDAO = new BidResultDAO();
    $ConfigDAO = new ConfigDAO();

    $round = $ConfigDAO->getRound();
    $rounded = round($round / 2, 0, PHP_ROUND_HALF_UP);

    $bids = $BidDAO->retrieveClassBids($cid, $sid);
    $amountArr = array_column($bids, 'amount');
    $studentArr = array_column($bids, 'userid');

    // Retrieve number of vacancies for section
    $section = $SectionDAO->retrieve($cid, $sid);
    $numVacancies = $section->size;
    $numSuccess = $BidResultDAO->countSuccess($cid, $sid);
    if ($numSuccess > 0) {
        $numVacancies -= $numSuccess;
    }

    // Handle bids
    $ranking = 1;
    $clearingPrice = getClearingPrice($cid, $sid);
    foreach ($amountArr as $i => $amt) {
        $amt = (float) $amt;
        if ($amt >= $clearingPrice) {
            // Add success bids
            $bid_result = new BidResult($rounded, $cid, $sid, $ranking, $studentArr[$i], $amt, "Success");
            placeSuccessBid($bid_result);
            $ranking++;
        } else {
            // Add failed bids
            $bid_result = new BidResult($rounded, $cid, $sid, $ranking, $studentArr[$i], $amt, "Fail");
            placeFailedBid($bid_result);
            $ranking++;
        }
    }
}

function getClearingPrice($cid, $sid)
{
    $SectionDAO = new SectionDAO();
    $BidDAO = new BidDAO();
    $BidResultDAO = new BidResultDAO();
    $ConfigDAO = new ConfigDAO();

    $round = $ConfigDAO->getRound();
    $bids = $BidDAO->retrieveClassBids($cid, $sid);
    $amountArr = array_column($bids, 'amount');

    // Retrieve number of vacancies for section
    $section = $SectionDAO->retrieve($cid, $sid);
    $numVacancies = $section->size;
    $numSuccess = $BidResultDAO->countSuccess($cid, $sid);
    if ($numSuccess > 0) {
        $numVacancies -= $numSuccess;
    }

    // Find clearing price
    if ($numVacancies <= 0) {
        // All fail, make clearing price unreachable
        $clearingPrice = (float) $amountArr[0] + 0.01;
    } elseif (count($amountArr) < $numVacancies) {
        // All successful, make clearing price zero
        $clearingPrice = 0;
    } elseif (count($amountArr) == $numVacancies) {
        if ($round == 1) {
            $numVacancies = $numVacancies - 1; // For use as array index
            $clearingPrice = (float) $amountArr[$numVacancies];
            if ($numVacancies > 0 && $amountArr[$numVacancies - 1] == $clearingPrice) {
                foreach ($amountArr as $amt) {
                    if ($amt > $clearingPrice) {
                        $newClearingPrice = $amt;
                    }
                }
                $clearingPrice = $newClearingPrice;
            }
        } else {
            $clearingPrice = 0;
        }
    } else {
        $numVacancies = $numVacancies - 1; // For use as array index
        $clearingPrice = (float) $amountArr[$numVacancies];
        // Check if need to drop bids at clearing price
        if ($amountArr[$numVacancies + 1] == $clearingPrice) {
            foreach ($amountArr as $amt) {
                $amt = (float) $amt;
                if ($amt > $clearingPrice) {
                    $newClearingPrice = $amt;
                }
            }
            $clearingPrice = $newClearingPrice;
        }
    }
    return $clearingPrice;
}

function placeSuccessBid($bid_result)
{
    $BidDAO = new BidDAO();
    $BidResultDAO = new BidResultDAO();
    $userid = $bid_result->userid;
    $cid = $bid_result->cid;
    if ($BidResultDAO->add($bid_result)) {
        $BidDAO->remove($userid, $cid);
    }
}

function placeFailedBid($bid_result)
{
    $StudentDAO = new StudentDAO();
    $BidDAO = new BidDAO();
    $BidResultDAO = new BidResultDAO();
    $userid = $bid_result->userid;
    $cid = $bid_result->cid;
    if ($BidResultDAO->add($bid_result)) {
        $BidDAO->remove($userid, $cid);
        // Refund e-dollar
        $stu = $StudentDAO->retrieve($userid);
        $stu->edollar += $bid_result->amount;
        $StudentDAO->update($stu);
    }
}
