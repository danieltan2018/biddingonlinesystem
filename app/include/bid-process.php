<?php
require_once 'common.php';

function checkbid($userid, $amount, $cid, $sid)
{
    $StudentDAO = new StudentDAO();
    $CourseDAO = new CourseDAO();
    $SectionDAO = new SectionDAO();
    $PrerequisiteDAO = new PrerequisiteDAO();
    $CourseCompletedDAO = new CourseCompletedDAO();
    $BidDAO = new BidDAO();
    $BidResultDAO = new BidResultDAO();
    $MinBidDAO = new MinBidDAO();
    $ConfigDAO = new ConfigDAO();

    $messages = [];

    $round = $ConfigDAO->getRound();
    $rounded = round($round / 2, 0, PHP_ROUND_HALF_UP);
    $oldbids = $BidDAO->retrieve($userid);
    $newbids = $BidResultDAO->retrieveSuccess($userid);
    $bids = array_merge($oldbids, $newbids);
    $stu = $StudentDAO->retrieve($userid);
    $stu_school = $stu->school;
    $course = $CourseDAO->retrieve($cid);
    $course_school = $course->school;
    if (($rounded == 1) && ($stu_school != $course_school)) {
        $messages[] = "not own school course";
    }

    $class = $SectionDAO->retrieve($cid, $sid);
    $current_day = $class->dayweek;
    $current_start = $class->starttime;
    $current_end = $class->endtime;
    foreach ($bids as $bid) {
        $other_cid = $bid->cid;
        $other_sid = $bid->sid;
        $other_class = $SectionDAO->retrieve($other_cid, $other_sid);
        $other_day = $other_class->dayweek;
        $other_start = $other_class->starttime;
        $other_end = $other_class->endtime;
        $sameday = ($current_day == $other_day);
        if ($sameday) {
            $is_before = ((strtotime($current_start) < strtotime($other_start)) && (strtotime($current_end) <= strtotime($other_start)));
            $is_after = ((strtotime($current_end) > strtotime($other_end)) && (strtotime($current_start) >= strtotime($other_end)));
            if (!$is_before && !$is_after) {
                $messages[] = "class timetable clash";
                break;
            }
        }
    }

    $exam_date = $course->examdate;
    $exam_start = $course->examstart;
    $exam_end = $course->examend;
    foreach ($bids as $bid) {
        $other_cid = $bid->cid;
        $other_exam = $CourseDAO->retrieve($other_cid);
        $other_exam_date = $other_exam->examdate;
        $other_exam_start = $other_exam->examstart;
        $other_exam_end = $other_exam->examend;
        $samedate = ($exam_date == $other_exam_date);
        if ($samedate) {
            $is_before = ((strtotime($exam_start) < strtotime($other_exam_start)) && (strtotime($exam_end) <= strtotime($other_exam_start)));
            $is_after = ((strtotime($exam_end) > strtotime($other_exam_end)) && (strtotime($exam_start) >= strtotime($other_exam_end)));
            if (!$is_before && !$is_after) {
                $messages[] = "exam timetable clash";
                break;
            }
        }
    }

    $prerequired_arr = $PrerequisiteDAO->retrieve($cid);
    $prerequired = array_column($prerequired_arr, 'prerequisite');
    $completed_arr = $CourseCompletedDAO->retrieve($userid);
    $completed = array_column($completed_arr, 'cid');

    foreach ($prerequired as $cname) {
        if (!in_array($cname, $completed)) {
            $messages[] = "incomplete prerequisites";
            break;
        }
    }

    if (in_array($cid, $completed)) {
        $messages[] = "course completed";
    }

    if (count($bids) == 5) {
        $messages[] = "section limit reached";
    }

    $stu_balance = $stu->edollar;
    if ($amount > $stu_balance) {
        $messages[] = "not enough e-dollar";
    }

    $minbid = $MinBidDAO->getMinBid($cid, $sid);
    if ($amount < $minbid) {
        $messages[] = "bid too low";
    }

    $numVacancies = $class->size - $BidResultDAO->countSuccess($cid, $sid);
    if ($numVacancies <= 0) {
        $messages[] = "no vacancy";
    }

    if ($BidResultDAO->retrieveEnrollment($userid, $cid) != null) {
        $messages[] = "course enrolled";
    }

    sort($messages);
    return $messages;
}
