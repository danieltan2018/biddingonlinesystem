<?php
require_once '../include/common.php';
require_once 'admin_protect.php';

$result = isAdmin();
if (isAdmin() === true) {
    $result = dropSection();
}

function dropSection()
{
    $message = array();
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
        if (!isset($r['userid'])) {
            $message[] = "missing userid";
        } elseif (empty($r['userid'])) {
            $message[] = "blank userid";
        }
    } else {
        $message[] = "missing request";
    }

    if (isEmpty($message)) {
        $StudentDAO = new StudentDAO();
        $CourseDAO = new CourseDAO();
        $SectionDAO = new SectionDAO();
        $BidResultDAO = new BidResultDAO();

        // invalid course/section error
        if ($CourseDAO->retrieve($r['course']) == null) {
            $message[] = "invalid course";
        } elseif ($SectionDAO->retrieve($r['course'], $r['section']) == null) {
            $message[] = "invalid section";
        }

        // invalid userid error
        if ($StudentDAO->retrieve($r['userid']) == null) {
            $message[] = "invalid userid";
        }

        // round not active error
        $ConfigDAO = new ConfigDAO();
        $round = $ConfigDAO->getRound();
        if ($round % 2 == 0) {
            $message[] = "round not active";
        }
        sort($message);
    }

    // so such enrollment record error
    if (isEmpty($message)) {
        $enroll = $BidResultDAO->retrieveEnrollment($r['userid'], $r['course']);
        if ($enroll == null) {
            $message[] = "no such enrollment record";
        } else {
            $enroll_amount = $enroll->amount;
        }
        sort($message);
    }

    // Perform Delete
    if (isEmpty($message)) {
        if ($BidResultDAO->remove($r['userid'], $r['course'])) {
            // Refund e$
            $stu = $StudentDAO->retrieve($r['userid']);
            $stu->edollar += $enroll_amount;
            $StudentDAO->update($stu);
            return ["status" => "success"];
        }
    }
    return ["status" => "error", "message" => $message];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
