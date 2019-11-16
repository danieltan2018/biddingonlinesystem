<?php
require_once "JWT.php";

const SECRET_KEY = "g8t8";
$adminuser = 'admin';
$adminpass = 'admin@G8T8';

function generate_token($username)
{
    return JWT::generate_token($username, SECRET_KEY);
}

function verify_token($token)
{
    return JWT::verify_token($token, SECRET_KEY);
}
