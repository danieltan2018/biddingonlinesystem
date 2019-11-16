<?php
require_once '../include/common.php';
require_once 'admin_protect.php';

$result = isAdmin();
if (isAdmin() === true) {
    $result = userDump();
}

function userDump()
{
    $message = array();
    if (isset($_REQUEST["r"])) {
        $r_json = $_REQUEST["r"];
        $r = json_decode($r_json, true);
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
        $student_obj = $StudentDAO->retrieve($r['userid']);
        if ($student_obj == null) {
            $message[] = "invalid userid";
        } else {
            $student_obj->edollar = floatval($student_obj->edollar);
            $student = (array) $student_obj;
            $json_return = array_merge(["status" => "success"], $student);
            return $json_return;
        }
    }
    return ["status" => "error", "message" => $message];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
