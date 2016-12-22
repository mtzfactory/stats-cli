<?php
//http://www.jqwidgets.com/bind-jquery-chart-to-mysql-database-using-php/
//http://www.jqwidgets.com/jquery-widgets-documentation/documentation/jqxchart/jquery-chart-data-source.htm

	$DEBUG = false;
	// Turn off all deprecated warnings including them from mysql_*:
	error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE); // E_NOTICE: How to Fix PHP Notice: Undefined index?

	# Include the connect.php file
	include('general.php');
	include('connect.php');

	$table = $_GET['table'];
	$query = "SELECT *," . (strpos($table, 'umts') !== false ? "FddCell" : "GsmCell") . " As Cell FROM $table";
	if (isset($_GET['nodo'])) {
		//$query .= " WHERE FddCell LIKE '" . $_GET['nodo'] . "%' ORDER BY Day_kpi LIMIT 10";
		$query .= " WHERE " . (strpos($table, 'umts') !== false ? "FddCell" : "GsmCell") . " LIKE '" . strtoupper($_GET['nodo']) . "%' AND Day_kpi BETWEEN CURDATE() - INTERVAL " . (isset($_GET['days']) ? $_GET['days'] : 10) . " DAY AND CURDATE() ORDER BY Day_kpi";
	}
	else {
		//$query .= " WHERE FddCell LIKE '0700BX%' ORDER BY `Day_kpi` LIMIT 31";
		$query .= " WHERE " . (strpos($table, 'umts') !== false? "FddCell" : "GsmCell") . " LIKE 'BX01A%' AND Day_kpi BETWEEN CURDATE() - INTERVAL " . (isset($_GET['days']) ? $_GET['days'] : 40) . " DAY AND CURDATE() ORDER BY Day_kpi";
	}

	if ($DEBUG) var_dump($_GET['nodo']);
	if ($DEBUG) var_dump($query);

	$result = mysql_query($query) or die("SQL Error (kpi Select): " . mysql_error() . " -- " . $query);
	$sql = "SELECT FOUND_ROWS() AS `found_rows`;";
	$rows = mysql_query($sql);
	$rows = mysql_fetch_assoc($rows);
	$total_rows = $rows['found_rows'];

	if ($DEBUG) var_dump($total_rows);
	if ($DEBUG) var_dump($result);

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$counters[] = $row;
	}

	$data[] = array(
		'TotalRows' => $total_rows,
		'Rows' => $counters
	);
	
	$data_json = json_encode($counters);
	if ($DEBUG) var_dump($data_json);
	$error = json_last_error();
	if ($error != JSON_ERROR_NONE) {	
		xdebug_debug_zval('query');
		xdebug_debug_zval('total_rows');
		echo json_last_error_msg();
	}
	echo $data_json;
?>