<?php

spl_autoload_register(function ($class) {
    require_once "$class.php";
});

session_start();

function isMissingOrEmpty($name)
{
    if (!isset($_REQUEST[$name])) {
        return "missing $name";
    }

    // client did send the value over
    $value = $_REQUEST[$name];
    if (empty($value)) {
        return "blank $name";
    }
}

function isEmpty($var)
{
    if (isset($var) && is_array($var))
        foreach ($var as $key => $value) {
            if (empty($value)) {
                unset($var[$key]);
            }
        }

    if (empty($var))
        return TRUE;
}
