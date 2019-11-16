<?php
require_once 'include/common.php';
unset($_SESSION['user']);
header("Location: login.php");
