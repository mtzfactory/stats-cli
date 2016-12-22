<?php
// http://mtzfactory.me/php/counters2.php?nodo=0088hu&table=umts_hua_report_full
// http://mtzfactory.local/php/counters2.php?nodo=VIABET&days=30&table=gsm_nokia_report

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

	$table = $_GET['table'];
	if (!isset($table)) returnJsonError("param", 1, "falta la tecnología.");

	$node = $_GET['nodo'];
	if (!isset($node)) returnJsonError("param", 2, "falta la celda o nodo.");
	$node = strtoupper($node);

	$interval = isset($_GET['days']) ? $_GET['days'] : 10;
	$fieldCell = strpos($table, 'umts') !== false ? "CELL" : "CELL";

	$umts_columns = "*";

	$gsm_columns = "DTIME, CELL, CELLLACCI, RED, BSC, COD_EMPLA, VENDOR, 
	2G_NORMATTS																							as G_NORMATTS, 
	2G_NORMSEIZ																							as G_NORMSEIZ, 
	2G_T_CONGESTION																						as G_T_CONGESTION, 
	2G_NORMSEIZ_NC																						as G_NORMSEIZ_NC,
	2G_TRAFFIC																							as G_TRAFFIC, 
	2G_TCH_SEIZURES																						as G_TCH_SEIZURES, 
	2G_DROPPED																							as G_DROPPED, 
	G_DCR, 
	2G_SD_ATTEMPTS																						as G_SD_ATTEMPTS, 
	2G_SD_SEIZURES																						as G_SD_SEIZURES, 
	2G_SD_BLOCKS																						as G_SD_BLOCKS, 
	2G_SD_PPCAID																						as G_SD_PPCAID, 
	2G_INTRAOK																							as G_INTRAOK, 
	2G_OUTOK																							as G_OUTOK, 
	2G_OBSCOK																							as G_OBSCOK, 
	2G_OMSCOK																							as G_OMSCOK, 
	2G_OBSCFL																							as G_OBSCFL, 
	2G_OMSCFL																							as G_OMSCFL, 
	2G_DL_GPRS																							as G_DL_GPRS, 
	2G_UL_GPRS																							as G_UL_GPRS, 
	2G_DL_EDGE																							as G_DL_EDGE, 
	2G_UL_EDGE																							as G_UL_EDGE, 
	2G_EST_DLTBF																						as G_EST_DLTBF, 
	2G_FAIL_DLTBF																						as G_FAIL_DLTBF, 
	G_DLQUAL345,
	G_DLQUAL67,
	G_ULQUAL345,
	G_ULQUAL67";
	/*
	(2G_NORMATTS - 2G_NORMSEIZ)																			as G_NC, 
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
	*/

# http://www.media-division.com/using-mysql-generate-daily-sales-reports-filled-gaps/

	$columns = strpos($table, 'umts') !== false ? $umts_columns : $gsm_columns;

	$query = "SELECT $columns FROM $table WHERE $fieldCell LIKE '$node%' AND DTIME BETWEEN CURDATE() - INTERVAL $interval DAY AND CURDATE() ORDER BY $fieldCell, DTIME";
	if (!$result = $mysqli->query($query)) {
		returnJsonError("query", $mysqli->errno, $mysqli->error);
	}
	$execution_time1 = number_format(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2);
	
	if ($result->num_rows === 0) {
		returnJsonError("query", 3, "No hay registros para $node.");
	}

	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$counters[] = $row;
	}
	$execution_time2 = number_format(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2);

	$keys = array_keys($counters[0]);
	$cells = array_unique(array_column($counters, $fieldCell));
	$lacci = array_column($counters, 'CELLLACCI', $fieldCell);
	$network = array_column($counters, 'RED', $fieldCell);
	$bsc = array_unique(array_column($counters, 'BSC'));
	$site = array_unique(array_column($counters, 'COD_EMPLA'));
	$vendor = array_unique(array_column($counters, 'VENDOR'));
	$period = array_column(array_filter($counters, function($v) use ($cells, $fieldCell) { return $v[$fieldCell] == $cells[0]; }), 'DTIME');

	$data = [];
	foreach ($keys as $key) {
		if (0 === strpos($key, 'G_')) {
			$x = 0;
			$child = new stdClass();
			$child->counter = $key;
			foreach ($cells as $cell) {
				$child->datasets[] = new stdClass();
				$child->datasets[$x]->label = $cell;
				$counters_per_cell = array_filter($counters, function($v) use ($cell) { return $v['CELL'] == $cell; });
				$child->datasets[$x]->data = array_column($counters_per_cell, $key);
				$child->datasets[$x]->fill = count($cells) > 3 ? false :  true;
				$child->datasets[$x]->hidden = false;
				$x++;
			}
			$data[] = $child;
			$child = null;
		}
	}
	$execution_time3 = number_format(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2);

	$time = new stdClass();
	$time->time_query = $execution_time1;
	$time->time_fetch = $execution_time2;
	$time->time_filter = $execution_time3;

	$result = new stdClass();
	$result->execution = $time;
	$result->cells = $cells;
	$result->lacci = $lacci;
	$result->network = $network;
	$result->bsc = $bsc[0];
	$result->site = $site[0];
	$result->vendor = $vendor[0];
	$result->period = $period;
	$result->counters = $data;
	
	if ($DEBUG) {
		echo '<h1>counters</h1>';	echo '<pre>'; print_r($counters);	echo '</pre>';
		echo '<h1>cells</h1>';		echo '<pre>'; print_r($cells);		echo '</pre>';
		echo '<h1>types</h1>';		echo '<pre>'; print_r($types);		echo '</pre>';
		echo '<h1>period</h1>';		echo '<pre>'; print_r($period);		echo '</pre>';
		echo '<h1>keys</h1>';		echo '<pre>'; print_r($keys);		echo '</pre>';
		echo '<h1>json ready</h1>';	echo '<pre>'; print_r($result);		echo '</pre>';
	}
	else {
		$data_json = json_encode($result);
		$error = json_last_error();
		if ($error != JSON_ERROR_NONE) {	
			xdebug_debug_zval('query');
			xdebug_debug_zval('total_rows');
			echo json_last_error_msg();
		}
		header('Content-Type: application/json');
		echo $data_json;
	}
	$result->free();
	$mysqli->close();

	if ($DEBUG) { 
		$execution_time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
		echo '<p>Total execution time: ' . number_format($execution_time, 2) . " seconds</p>"; 
	}
?>