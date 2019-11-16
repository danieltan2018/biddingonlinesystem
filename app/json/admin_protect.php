<?php

require_once '../include/token.php';

function isAdmin()
{
    $error = isMissingOrEmpty('token');
    if (isEmpty($error)) {
        if (verify_token($_REQUEST['token']) != 'admin') {
            $error = 'invalid token';
        }
    }
    if (isEmpty($error)) {
        return TRUE;
    } else {
        return ["status" => "error", "messages" => $error];
    }
}
