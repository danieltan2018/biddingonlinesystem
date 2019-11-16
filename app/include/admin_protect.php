<?php
require_once 'include/protect.php';
if ($_SESSION['user'] != 'admin') {
    header("Location: index.php");
}
