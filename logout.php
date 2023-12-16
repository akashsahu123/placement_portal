<?php
session_start();
$_SESSION = array();
session_destroy();
if (!isset($_GET['ref']))
    $ref = 'index.php';
else
    $ref = $_GET['ref'];

header("location: $ref");
exit;
?>