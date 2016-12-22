<?php
// enable CORS para q esta web pueda ser consultada desde otro server, http://enable-cors.org/index.html
	
	$DEBUG = false;
	if ($DEBUG) {
		// Turn off all deprecated warnings including them from mysql_*: display ALL except DEPRECATED and NOTICE...
		error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE); // E_NOTICE: How to Fix PHP Notice: Undefined index?
	}
	else {
		error_reporting(0);
	}
	# Include the connect.php file
	include('general.php');
	include('connect2.php');

	$prov = $_GET['prov'];
	if (!isset($prov)) returnJsonError("param", 1, "falta la provincia.");
	$prov = strtoupper($prov);

	$table = $_GET['table'];
	if (!isset($table)) returnJsonError("param", 1, "falta la tecnología.");
	$table = strtoupper($table)=="GSM" ? "gsm_nokia_report" : "umts_nokia_report";

	$interval = isset($_GET['days']) ? $_GET['days'] : 10;

	$columns = "DTIME, PROVINCE,
				sum(`2G_NORMATTS`) AS G_NORMATTS,
			    sum(`2G_DROPPED`) AS G_DROPPED,
			    sum(`2G_DROPPED`) / sum(`2G_NORMATTS`) AS G_DCR,
			    sum(`2G_NORMSEIZ_NC`) AS G_NORMSEIZ_NC,
			    sum(`2G_NORMATTS`) - sum(`2G_NORMSEIZ`) AS G_NC,
			    sum(`2G_T_CONGESTION`) AS G_T_CONGESTION,
			    sum(`2G_SD_BLOCKS`) AS G_SD_BLOCKS,
			    sum(`2G_SD_PPCAID`) AS G_SD_PPCAID,
			    sum(`2G_OBSCFL`) AS G_OBSCFL,
			    sum(`2G_OMSCFL`) AS G_OMSCFL,
			    sum(`2G_FAIL_DLTBF`) AS G_FAIL_DLTBF";

	$query = "SELECT $columns FROM $table GROUP BY DTIME, PROVINCE ORDER BY PROVINCE, DTIME;" 

	if (!$result = $mysqli->query($query)) {
		returnJsonError("query", $mysqli->errno, $mysqli->error);
	}
	$execution_time1 = number_format(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2);

?>