<?php
require_once '../include/common.php';
require_once 'admin_protect.php';

$result = isAdmin();
if (isAdmin() === true) {
    $result = bidDump();
}

function bidDump()
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
        // invalid course/section error
        $message = array();
        $CourseDAO = new CourseDAO();
        $SectionDAO = new SectionDAO();
        if ($CourseDAO->retrieve($r['course']) == null) {
            $message[] = "invalid course";
        } elseif ($SectionDAO->retrieve($r['course'], $r['section']) == null) {
            $message[] = "invalid section";
        }
        sort($message);
    }

    if (isEmpty($message)) {
        $BidResultDAO = new BidResultDAO();
        $stu_arr = [];
        $students = $BidResultDAO->dumpSection($r['course'], $r['section']);
        foreach ($students as $student) {
            $student['amount'] = floatval($student['amount']);
            $stu_arr[] = $student;
        }
        return ["status" => "success", "students" => $stu_arr];
    }
    return ["status" => "error", "message" => $message];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
