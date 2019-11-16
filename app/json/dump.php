<?php
require_once '../include/common.php';
require_once 'admin_protect.php';

$result = isAdmin();
if (isAdmin() === true) {
    $result = doDump();
}

function doDump()
{
    $ConfigDAO = new ConfigDAO();
    $round = $ConfigDAO->getRound();
    if (isEmpty($round)) {
        // Database empty or system reset
        return ["status" => "error", "message" => "do bootstrap first"];
    }

    $CourseDAO = new CourseDAO();
    $course = $CourseDAO->retrieveAll();

    $SectionDAO = new SectionDAO();
    $section = $SectionDAO->retrieveAll();

    $StudentDAO = new StudentDAO();
    $student = $StudentDAO->retrieveAll();
    $stu_arr = [];
    foreach ($student as $stu) {
        $stu['edollar'] = floatval($stu['edollar']);
        $stu_arr[] = $stu;
    }

    $PrerequisiteDAO = new PrerequisiteDAO();
    $prerequisite = $PrerequisiteDAO->retrieveAll();

    $CourseCompletedDAO = new CourseCompletedDAO();
    $completed_course = $CourseCompletedDAO->retrieveAll();

    $BidDAO = new BidDAO();
    $BidResultDAO = new BidResultDAO();
    $bid_arr = [];

    if ($round % 2 != 0) {
        // Bidding round active
        $bids = $BidDAO->retrieveAll();
        foreach ($bids as $bid) {
            $bid['amount'] = floatval($bid['amount']);
            $bid_arr[] = $bid;
        }
    } else {
        // No active round
        $bids = $BidResultDAO->retrieveAll($round / 2);
        foreach ($bids as $bid) {
            $bid['amount'] = floatval($bid['amount']);
            $bid_arr[] = $bid;
        }
    }

    $section_student = $BidResultDAO->retrieveSectionStudent();
    $s_s_arr = [];
    foreach ($section_student as $s_s) {
        $s_s['amount'] = floatval($s_s['amount']);
        $s_s_arr[] = $s_s;
    }

    $json_return = [
        "status" => "success",
        "course" => $course,
        "section" => $section,
        "student" => $stu_arr,
        "prerequisite" => $prerequisite,
        "bid" => $bid_arr,
        "completed-course" => $completed_course,
        "section-student" => $s_s_arr
    ];
    return $json_return;
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
