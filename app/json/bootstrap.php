<?php
require_once '../include/bootstrap.php';
require_once 'admin_protect.php';

$result = isAdmin();
if (isAdmin() === true) {
    $result = doBootstrap();
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
