<?php
require_once 'admin_protect.php';
require_once '../include/round-process.php';

$result = isAdmin();
if (isAdmin() === true) {
    $result = stopRound();
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
