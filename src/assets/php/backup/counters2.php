<?php
// http://mtzfactory.me/php/counters2.php?nodo=0088hu&table=umts_hua_report_full
// http://mtzfactory.local/php/counters2.php?nodo=VIABET&days=30&table=gsm_nokia_report

// enable CORS para q esta web pueda ser consultada desde otro server, http://enable-cors.org/index.html
	
	$DEBUG = false;
	// Turn off all deprecated warnings including them from mysql_*:
	error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE); // E_NOTICE: How to Fix PHP Notice: Undefined index?

	# Include the connect.php file
	include('general.php');
	include('connect2.php');

	$table = $_GET['table'];
	$node = strtoupper($_GET['nodo']);
	$interval = isset($_GET['days']) ? $_GET['days'] : 10;
	$fieldCell = strpos($table, 'umts') !== false ? "CELL" : "CELL";

	$umts_columns = "*";
	$gsm_columns = "DTIME, CELL, RED, 

	2G_NORMATTS								as G_NORMATTS, 
	2G_NORMSEIZ								as G_NORMSEIZ, 

	2G_T_CONGESTION							as G_T_CONGESTION, 
	(2G_NORMATTS - 2G_NORMSEIZ)				as G_NC, 
	
	2G_TRAFFIC								as G_TRAFFIC, 
	2G_TCH_SEIZURES							as G_TCH_SEIZURES, 

	2G_DROPPED								as G_DROPPED, 
	(2G_DROPPED / 2G_NORMSEIZ)*100			as G_DCR, 

	2G_SD_ATTEMPTS							as G_SD_ATTEMPTS, 
	2G_SD_SEIZURES							as G_SD_SEIZURES, 
	2G_SD_BLOCKS							as G_SD_BLOCKS, 
	2G_SD_PPCAID							as G_SD_PPCAID, 
	
	2G_INTRAOK								as G_INTRAOK, 
	2G_OUTOK								as G_OUTOK, 
	2G_OBSCOK								as G_OBSCOK, 
	2G_OMSCOK								as G_OMSCOK, 
	2G_OBSCFL								as G_OBSCFL, 
	2G_OMSCFL								as G_OMSCFL, 
 
	2G_DL_GPRS								as G_DL_GPRS, 
	2G_UL_GPRS								as G_UL_GPRS, 
	2G_DL_EDGE								as G_DL_EDGE, 
	2G_UL_EDGE								as G_UL_EDGE, 
	2G_EST_DLTBF							as G_EST_DLTBF, 
	2G_FAIL_DLTBF							as G_FAIL_DLTBF, 
	
	@DLQUAL:=(2G_DLQUAL0+2G_DLQUAL1+2G_DLQUAL2+2G_DLQUAL3+2G_DLQUAL4+2G_DLQUAL5+2G_DLQUAL6+2G_DLQUAL7)	as DLQUAL, 
	@DLQUAL345:=(2G_DLQUAL3+2G_DLQUAL4+2G_DLQUAL5)														as DLQUAL345, 
	@DLQUAL67:=(2G_DLQUAL6+2G_DLQUAL7)																	as DLQUAL67, 
	@DLQUAL345/@DLQUAL * 100																			as G_DLQUAL345,
	@DLQUAL67/@DLQUAL * 100																				as G_DLQUAL67,
	@ULQUAL:=(2G_ULQUAL0+2G_ULQUAL1+2G_ULQUAL2+2G_ULQUAL3+2G_ULQUAL4+2G_ULQUAL5+2G_ULQUAL6+2G_ULQUAL7)	as ULQUAL, 
	@ULQUAL345:=(2G_ULQUAL3+2G_ULQUAL4+2G_ULQUAL5)														as ULQUAL345,
	@ULQUAL67:=(2G_ULQUAL6+2G_ULQUAL7)																	as ULQUAL67, 	
	@ULQUAL345/@ULQUAL * 100																			as G_ULQUAL345, 	
	@ULQUAL67/@ULQUAL * 100																				as G_ULQUAL67";

# http://www.media-division.com/using-mysql-generate-daily-sales-reports-filled-gaps/

	$query_columns = strpos($table, 'umts') !== false ? $umts_columns : $gsm_columns;

	$query = "SELECT DISTINCT $fieldCell As Cell FROM $table WHERE $fieldCell LIKE '%$node%' ORDER by $fieldCell";
	$resultCells = $mysqli->query($query);
	while ($cell = $resultCells->fetch_array(MYSQLI_NUM)) {
		$cells[] = $cell[0];
		$query = "SELECT " . $query_columns . " FROM $table WHERE $fieldCell = '" . $cell[0] . "' AND DTIME BETWEEN CURDATE() - INTERVAL $interval DAY AND CURDATE() ORDER BY DTIME";
		$resultCounters = $mysqli->query($query);
		while ($row = $resultCounters->fetch_array(MYSQLI_ASSOC)) {
			$rows[] = $row;
		}
		$counters[] = $rows; //array( $cell[0] => $rows );
		$rows = [];
	}

	$data = new stdClass();
	$data->cells = $cells;
	$data->counters = $counters;
	
	if ($DEBUG) {
		foreach ($counters as $arr) {
			$prev[] = array_column($arr, 'G_NORMATTS');//, 'CELL');
		}
		var_dump($prev);
		echo "\n";
		$keys = array_keys($counters[0]);
		var_dump($keys);
	}
	else {
		$data_json = json_encode($data);
		if ($DEBUG) var_dump($data_json);
		$error = json_last_error();
		if ($error != JSON_ERROR_NONE) {	
			xdebug_debug_zval('query');
			xdebug_debug_zval('total_rows');
			echo json_last_error_msg();
		}
		echo $data_json;
	}
?>