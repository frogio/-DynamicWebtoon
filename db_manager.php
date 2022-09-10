<?php

header('Content-Type: text/html; charset=utf-8');

define("SITE_DB_ADDRESS", "localhost", FALSE);
define("SITE_DB_ACCOUNT", "root", FALSE);
define("SITE_DB_PASSWORD", "1248", FALSE);
define("SITE_DB_NAME", "openwebtoon",FALSE);
// db info

function connect_db(){

	$con = mysqli_connect(SITE_DB_ADDRESS, SITE_DB_ACCOUNT, SITE_DB_PASSWORD, SITE_DB_NAME);
	
	
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		exit();
	}
	
	return $con;
}

function release_db($con){
	
	mysqli_close($con);
	
}
