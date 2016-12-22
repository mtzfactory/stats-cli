<?php
	// Turn off all deprecated warnings including them from mysql_*:
	error_reporting(E_ALL ^ E_DEPRECATED);

	# Include the connect.php file
	include('general.php');
	include('connect.php');

	# Emepezamos
	if (isset($_GET['update']))			// UPDATE COMMAND 
	{

		$SwapDate = "NULL";
		if ( $_GET['Swap_date'] != "" and $_GET['Swap_date'] != "null" ) {
			$temp_date = new DateTime($_GET['Swap_date']);
			$temp_string = $temp_date->format('Y-m-d');
			$SwapDate = "'" . $temp_string . "'";
		}

		$Transfer2GtoVdF = "NULL";
		if ( $_GET['Transfer_2G_to_VdF'] != "" and $_GET['Transfer_2G_to_VdF'] != "null" ) {
			$temp_date = new DateTime($_GET['Transfer_2G_to_VdF']);
			$temp_string = $temp_date->format('Y-m-d');
			$Transfer2GtoVdF = "'" . $temp_string . "'";
		}

		$RRdate = "NULL";
		if ( $_GET['RR_date'] != "" and $_GET['RR_date'] != "null" ) {
			$temp_date = new DateTime($_GET['RR_date']);
			$temp_string = $temp_date->format('Y-m-d');
			$RRdate = "'" . $temp_string . "'";
		}

		$Transfer3GtoVdF = "NULL";
		if ( $_GET['Transfer_3G_to_VdF'] != "" and $_GET['Transfer_3G_to_VdF'] != "null" ) {
			$temp_date = new DateTime($_GET['Transfer_3G_to_VdF']);
			$temp_string = $temp_date->format('Y-m-d');
			$Transfer3GtoVdF = "'" . $temp_string . "'";
		}

		$U900Date = "NULL";
		if ( $_GET['U900_Date'] != "" and $_GET['U900_Date'] != "null" ) {
			$temp_date = new DateTime($_GET['U900_Date']);
			$temp_string = $temp_date->format('Y-m-d');
			$U900Date = "'" . $temp_string . "'";
		}

		$update_query = "UPDATE `$tabla_allsites` SET `New_Site_Code`='" . $_GET['New_Site_Code'] . "',
		`BTS_Name`='" . $_GET['BTS_Name'] . "',
		`NodeB_Name`='" . $_GET['NodeB_Name'] . "',
		`U900`='" . $_GET['U900'] . "',
		`Cluster`='" . $_GET['Cluster'] . "',
		`Cluster_3G`='" . $_GET['Cluster_3G'] . "',
		`BSC_Name`='" . $_GET['BSC_Name'] . "',
		`Swap_date`=" . $SwapDate . ",
		`Transfer_2G_to_VdF`=" . $Transfer2GtoVdF . ",
		`RNC_Name`='" . $_GET['RNC_Name'] . "',
		`RR_date`=" . $RRdate . ",
		`Transfer_3G_to_VdF`=" . $Transfer3GtoVdF . ",
		`U900_Date`=" . $U900Date . ",
		`GSM`='" . $_GET['GSM'] . "',
		`DCS`='" . $_GET['DCS'] . "',
		`Nombre_Elemento`='" . $_GET['Nombre_Elemento'] . "',
		`Notes`='" . $_GET['Notes'] . "',
		`Swap_or_New`='" . $_GET['Swap_or_New'] . "',
		`Celda_OSS`='" . $_GET['Celda_OSS'] . "',
		`Comentarios_del_SWAP`='" . $_GET['Comentarios_del_SWAP'] . "',
		`Comentarios_del_RR`='" . $_GET['Comentarios_del_RR'] . "' WHERE `AllSites_id`='" . $_GET['AllSites_id'] . "'";
		$result = mysql_query($update_query) or die("SQL Error (Update): " . mysql_error() . " -- " . $query);
		echo $result;
	}
	else if (isset($_GET['insert']))	// INSERT COMMAND
	{ 
		$insert_query = "INSERT INTO `$tabla_allsites` (
			'New_Site_Code',
			'BTS_Name',
			'NodeB_Name',
			'U900',
			'Cluster',
			'Cluster_3G',
			'BSC_Name',
			'Swap_date',
			'Transfer_2G_to_VdF',
			'RNC_Name',
			'RR_date',
			'Transfer_3G_to_VdF',
			'U900_Date',
			'Notes',
			'GSM',
			'DCS',
			'Nombre_Elemento',
			'Swap_or_New',
			'Celda_OSS',
			'Comentarios_del_SWAP',
			'Comentarios_del_RR') VALUES ('"
			. $_GET['New_Site_Code'] . "','"
			. $_GET['BTS_Name'] . "','"
			. $_GET['NodeB_Name'] . "','"
			. $_GET['U900'] . "','"
			. $_GET['Cluster'] . "','"
			. $_GET['Cluster_3G'] . "','"
			. $_GET['BSC_Name'] . "',"
			. ($_GET['Swap_date'] == "" ? "NULL" : "'" . $_GET['Swap_date'] . "'") . ","
			. ($_GET['Transfer_2G_to_VdF'] == "" ? "NULL" : "'" . $_GET['Transfer_2G_to_VdF'] . "'") . ",'"
			. $_GET['RNC_Name'] . "',"
			. ($_GET['RR_date'] == "" ? "NULL" : "'" . $_GET['RR_date'] ."'") . ","
			. ($_GET['Transfer_3G_to_VdF'] == "" ? "NULL" : "'" . $_GET['Transfer_3G_to_VdF'] . "'") . ","
			. ($_GET['U900_Date'] == "" ? "NULL" : "'" . $_GET['U900_Date'] . "'") . ",'"
			. $_GET['Notes'] . "','"
			. $_GET['GSM'] . "','"
			. $_GET['DCS'] . "','"
			. $_GET['Nombre_Elemento'] . "','"
			. $_GET['Swap_or_New'] . "','"
			. $_GET['Celda_OSS'] . "','"
			. $_GET['Comentarios_del_SWAP'] . "','"
			. $_GET['Comentarios_del_RR'] . "')";
		$result = mysql_query($insert_query) or die("SQL Error (Insert): " . mysql_error());
		echo $result;
	}
	else if (isset($_GET['delete']))	// DELETE COMMAND
	{
		$delete_query = "DELETE FROM `$tabla_allsites` WHERE `AllSites_id`='" . $_GET['AllSites_id'] . "'";
		$result = mysql_query($delete_query) or die("SQL Error (Delete): " . mysql_error());
		echo $result;
	}
	else 								// SELECT COMMAND
	{
		$maintotalrows = 0;
		// PAGINACION
		$pagenum = $_GET['pagenum'];
		$pagesize = $_GET['pagesize'];
		$start = $pagenum * $pagesize;
		
		// VIRTUAL SCROLLING
		// get first visible row.
		$firstvisiblerow = $_GET['recordstartindex'];
		// get the last visible row.
		$lastvisiblerow = $_GET['recordendindex'];
		$rowscount = $lastvisiblerow - $firstvisiblerow;
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM $tabla_allsites LIMIT $firstvisiblerow, $rowscount";

		//var_dump($query);
		//xdebug_debug_zval('tabla_allsites');

		// filter data.
		if (isset($_GET['filterscount']))
		{
			$filterscount = $_GET['filterscount'];
			if ($filterscount > 0)
			{
				$where = " WHERE ( ";
				$tmpdatafield = "";
				$tmpfilteroperator = "";
				for ($i=0; $i < $filterscount; $i++)
			    {
					// get the filter's value.
					$fv = $_GET["filtervalue" . $i];
					$filtervalue = validateDate($fv) ? date("Y-m-d", strtotime($fv) ) : $fv ;
					
					if ($filtervalue <> '') {						
						// get the filter's condition.
						$filtercondition = $_GET["filtercondition" . $i];
						// get the filter's column.
						$filterdatafield = $_GET["filterdatafield" . $i];
						// get the filter's operator.
						$filteroperator = $_GET["filteroperator" . $i];
						
						if ($tmpdatafield == "")
						{
							$tmpdatafield = $filterdatafield;			
						}
						else if ($tmpdatafield <> $filterdatafield)
						{
							$where .= " ) AND ( ";
						}
						else if ($tmpdatafield == $filterdatafield)
						{
							if ($tmpfilteroperator == 0)
							{
								$where .= " AND ";
							}
							else $where .= " OR ";	
						}
						
						// build the "WHERE" clause depending on the filter's condition, value and datafield.
						switch($filtercondition)
						{
							case "CONTAINS": $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."%'";	break;
							case "DOES_NOT_CONTAIN": $where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";	break;
							case "EQUAL": $where .= " " . $filterdatafield . " = '" . $filtervalue ."'"; break;
							case "NOT_EQUAL": $where .= " " . $filterdatafield . " <> '" . $filtervalue ."'";	break;
							case "GREATER_THAN": $where .= " " . $filterdatafield . " > '" . $filtervalue ."'"; break;
							case "LESS_THAN": $where .= " " . $filterdatafield . " < '" . $filtervalue ."'"; break;
							case "GREATER_THAN_OR_EQUAL": $where .= " " . $filterdatafield . " >= '" . $filtervalue ."'"; break;
							case "LESS_THAN_OR_EQUAL": $where .= " " . $filterdatafield . " <= '" . $filtervalue ."'"; break;
							case "STARTS_WITH": $where .= " " . $filterdatafield . " LIKE '" . $filtervalue ."%'"; break;
							case "ENDS_WITH": $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."'"; break;
						}
										
						if ($i == $filterscount - 1)
						{
							$where .= " )";
						}
						$tmpfilteroperator = $filteroperator;
						$tmpdatafield = $filterdatafield;
					}
				}
				// build the query.
				$query = "SELECT * FROM $tabla_allsites " . $where;
				$filterquery = $query;

				$result = mysql_query($query) or die("SQL Error (Filter total_rows): " . mysql_error() . " -- " . $query);
				$sql = "SELECT FOUND_ROWS() AS `found_rows`;";
				$rows = mysql_query($sql);
				$rows = mysql_fetch_assoc($rows);
				$total_rows = $rows['found_rows'];

				$query = "SELECT * FROM $tabla_allsites " . $where . " LIMIT $start, $total_rows";
				//xdebug_debug_zval('query');
			}
		}

		// sort data.
		if (isset($_GET['sortdatafield']))
		{
			$sortfield = $_GET['sortdatafield'];
			$sortorder = $_GET['sortorder'];
			if ($sortorder != '')
			{
				if ($_GET['filterscount'] == 0)
				{
					if ($sortorder == "desc")
					{
						$query = "SELECT SQL_CALC_FOUND_ROWS * FROM $tabla_allsites ORDER BY " . $sortfield . " DESC LIMIT $firstvisiblerow, $rowscount";
					}
					else if ($sortorder == "asc")
					{
						$query = "SELECT SQL_CALC_FOUND_ROWS * FROM $tabla_allsites ORDER BY " . $sortfield . " ASC LIMIT $firstvisiblerow, $rowscount";
					}
				}
				else
				{
					if ($sortorder == "desc")
					{
						$filterquery .= " ORDER BY " . $sortfield . " DESC LIMIT $start, $total_rows";
					}
					else if ($sortorder == "asc")	
					{
						$filterquery .= " ORDER BY " . $sortfield . " ASC LIMIT $start, $total_rows";
					}
					$query = $filterquery;
				}		
			}
		}
		//xdebug_debug_zval('query');
		$result = mysql_query($query) or die("SQL Error (Normal): " . mysql_error() . " -- " . $query);
		$sql = "SELECT FOUND_ROWS() AS `found_rows`;";
		$rows = mysql_query($sql);
		$rows = mysql_fetch_assoc($rows);
		$total_rows = $rows['found_rows'];
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$allsites[] = array(
				'AllSites_id' => $row['AllSites_id'],
				'New_Site_Code' => $row['New_Site_Code'],
				'BTS_Name' => $row['BTS_Name'],
				'NodeB_Name' => $row['NodeB_Name'],
				'U900' => $row['U900'],
				'Cluster' => $row['Cluster'],
				'Cluster_3G' => $row['Cluster_3G'],
				'BSC_Name' => $row['BSC_Name'],
				'Swap_date' => $row['Swap_date'],
				'Transfer_2G_to_VdF' => $row['Transfer_2G_to_VdF'],
				'RNC_Name' => $row['RNC_Name'],
				'RR_date' => $row['RR_date'],
				'Transfer_3G_to_VdF' => $row['Transfer_3G_to_VdF'],
				'U900_Date' => $row['U900_Date'],
				'Notes' => $row['Notes'],
				'GSM' => $row['GSM'],
				'DCS' => $row['DCS'],
				'Nombre_Elemento' => $row['Nombre_Elemento'],
				'Swap_or_New' => $row['Swap_or_New'],
				'Celda_OSS' => $row['Celda_OSS'],
				'Comentarios_del_SWAP' => $row['Comentarios_del_SWAP'],
				'Comentarios_del_RR' => $row['Comentarios_del_RR']
			);
		}

		$data[] = array(
				'TotalRows' => $total_rows,
				'FirstVisibleRow' => $firstvisiblerow,
				'LastVisibleRow' => $lastvisiblerow,
				'Rows' => $allsites
		);
		
		$data_json = json_encode($data);
		$error = json_last_error();
		if ($error != JSON_ERROR_NONE) {	
			xdebug_debug_zval('query');
			xdebug_debug_zval('total_rows');
			xdebug_debug_zval('allsites');
			echo json_last_error_msg();
		}
		echo $data_json;
	}
?>