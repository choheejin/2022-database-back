<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include_once 'dbconfig.php';
include_once 'auth.php';
include_once 'comment.php';
include_once 'article.php';
?>