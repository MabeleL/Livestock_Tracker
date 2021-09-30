<?php

define('TIMEZONE', 'Africa/Nairobi');
date_default_timezone_set(TIMEZONE);

define("DB_HOST", "localhost");
define("DB_USER", "mabele");
define("DB_PASSWORD", "Mabele2020*");
define("DB_DATABASE", "users_ltracker");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if($conn->connect_error)
{
//	echo "Failure";
	die("Connection failed: " .$conn->connect_error);
}else{
//	echo "Connected successfully";
}
?>
