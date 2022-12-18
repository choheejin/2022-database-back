<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// $httpOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
// if (in_array($httpOrigin, [
// 	// 여기 Array에 허용하려는 IP 또는 DOMAIN을 추가하면 된다.
// 	'http://localhost:3000', // Dev Client Server using CORS
// 	'http://client.google.com', // Prod Client Server using CORS
// ]))
// header("Access-Control-Allow-Origin: *");

// header('Access-Control-Allow-Credentials: true');
// header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
// header("Content-type:text/html;charset=utf-8");


function cors() {
    
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }
    
    echo "You have CORS!";
}


include_once 'dbconfig.php';
include_once 'app.php';
include_once 'auth.php';
include_once 'comment.php';
include_once 'article.php';
?>