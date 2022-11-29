<?php
$servername = "localhost:3306";
$username = "root";
$password = "비밀번호 변경";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if($conn->connect_error){
	die("Connection failed: " + $conn->connect_error);
}
?>