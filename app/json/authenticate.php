<?php

require_once '../include/common.php';
require_once '../include/token.php';

$errors = [
    isMissingOrEmpty('username'),
    isMissingOrEmpty('password')
];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values($errors)
    ];
} else {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == $adminuser && $password == $adminpass) {
        $result = [
            "status" => "success",
            "token" => generate_token($adminuser)
        ];
    } elseif ($username != $adminuser && $password == $adminpass) {
        $result = [
            "status" => "error",
            "message" => "invalid username"
        ];
    } elseif ($username == $adminuser && $password != $adminpass) {
        $result = [
            "status" => "error",
            "message" => "invalid password"
        ];
    } else {
        $result = [
            "status" => "error",
            "message" => "invalid username/password"
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
