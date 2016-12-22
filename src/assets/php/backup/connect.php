<?php
	#FileName="connect.php"
	$hostname = "127.0.0.1";
	$database = "huawei";
	$username = "ricardo_martinez";
	$password = "lcclcc";
	$tabla_allsites = "allsitestogether_pending";

	# Connect to the database
	// Connection String
	$connect = mysql_connect($hostname, $username, $password) or die('Could not connect: ' . mysql_error());
	mysql_set_charset("utf8");
	
	// Select the database
	$bool = mysql_select_db($database, $connect);
	if ($bool === False)
	{
		print "can't find $database";
	}
?>