<?php
	#FileName="connect.php"
	$hostname = "127.0.0.1";
	$username = "ricardo_martinez";
	$password = "lcclcc";
	$database = "nokia";

	# Connect to the database
	// Connection String

	$mysqli = new mysqli($hostname, $username, $password, $database); 
	if ($mysqli->connect_errno) {
    	//echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    	returnJsonError("mysql", $mysqli->connect_errno, $mysqli->connect_error);
    	//exit();
	}
?>