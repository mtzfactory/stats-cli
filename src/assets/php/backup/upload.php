<?php
	// Turn off all deprecated warnings including them from mysql_*:
	error_reporting(E_ALL ^ E_DEPRECATED);

	#Include the connect.php file
	include('connect.php');
	include('general.php');
	
	#Variables
	$error = 0;
	$msg = "";
	$message = "No es una llamada ajax";
	$file_allowed_types = array(
		"text/csv",
		"text/plain",
		"application/csv",
		"text/comma-separated-values",
		"application/vnd.ms-excel"
	);
	
	#Empezamos
	if (is_ajax()) {
	
		$file_att = $_FILES["form_upload_file_att"]["name"];
		$file_type = $_FILES['form_upload_file_att']['type'];
		$file_allowed = in_array($file_type, $file_allowed_types);
		$file_table = $_POST["form_upload_file_table"];
		$file_target = $_SERVER['DOCUMENT_ROOT'] . "upload/uploaded/$file_att";
		$file_temp = $_FILES["form_upload_file_att"]["tmp_name"];

		$msg .= ", file_att: $file_att";
		$msg .= ", file_type: $file_type";
		$msg .= ", file_allowed: " . ( $file_allowed ? 'true' : 'false' );
		$msg .= ", file_allowed_types: " . implode(",", $file_allowed_types);
		$msg .= ", file_table: $file_table";
		$msg .= ", file_target: $file_target";
		$msg .= ", file_temp: $file_temp";
	
		if ( !empty($file_att) and $file_allowed and isset($_POST["form_upload_file_table"]) && !empty($_POST["form_upload_file_table"]) ) {
			if ( move_uploaded_file($file_temp, $file_target) ) {
				switch($file_table) { //Switch case for value of action
					case "allsitestogether": 
						$intotable = "allsitestogether"; 
						$columns = " (AllSites_id, New_Site_Code, BTS_Name, NodeB_Name, U900, Cluster, Cluster_3G, BSC_Name, @2G_swap_date, @Transfer_2G_to_VdF, RNC_Name, @3G_RR_date, @Transfer_3G_to_VdF, @U900_Date, Notes, GSM, DCS, Nombre_Elemento, Swap_or_New, Celda_OSS, Comentarios_del_SWAP, Comentarios_del_RR)
										SET 2G_swap_date = NULLIF(STR_TO_DATE(@2G_swap_date,'%d/%m/%Y'), '0000-00-00'),
											Transfer_2G_to_VdF = NULLIF(STR_TO_DATE(@Transfer_2G_to_VdF,'%d/%m/%Y'), '0000-00-00'),
											3G_RR_date = NULLIF(STR_TO_DATE(@3G_RR_date,'%d/%m/%Y'), '0000-00-00'),
											Transfer_3G_to_VdF = NULLIF(STR_TO_DATE(@Transfer_3G_to_VdF,'%d/%m/%Y'), '0000-00-00'),
											U900_Date = NULLIF(STR_TO_DATE(@U900_Date,'%d/%m/%Y'), '0000-00-00')"; 
					break;
					case "mycom3g": 
						$intotable = "mycom3g"; 
						$columns = ""; 
					break;
					default:
						$error = 1;
						$message = "Accion no contemplada. $msg";
					break;
				}
				
				if ( !$error ) {
					#Connect to the database
					//connection String
					$connect = mysql_connect($hostname, $username, $password, false, 128) or die( "Error al conectar a la base de datos: $database " . mysql_error() );
					mysql_set_charset("utf8");
					
					//Select The database
					$bool = mysql_select_db($database, $connect);
					if ($bool === False)
					{
						$message = "Error al abrir la base de datos: $database";
					}
					
					$sql = "TRUNCATE TABLE $intotable";
					$result = mysql_query( $sql ) or die( "SQL Error (UPLOAD truncate): $intotable, " . mysql_error() );
					
					$sql = "LOAD DATA LOCAL INFILE '$file_target'";
					$sql .= " INTO TABLE $intotable";
					$sql .= " FIELDS TERMINATED BY ';' OPTIONALLY ENCLOSED BY '\"'";
					$sql .= " LINES TERMINATED BY '\\r\\n'";
					$sql .= " IGNORE 1 LINES";
					$sql .= "$columns";
					$result = mysql_query( $sql ) or die( "SQL Error (UPLOAD load): $intotable, " . mysql_error() );
					
					$affected_rows = mysql_affected_rows();
					if ( $affected_rows > 1 ) {
						$message = $affected_rows;
					} else {
						$error = 1;
						$message = mysql_error(); 
					}
					mysql_close( $connect );
				}
			} else {
				$error = 1; 
				$message = "Error al subir el fichero! $msg";
			}
		}
		else { 
			$error = 1; 
			$message = "Archivo vacio o no CSV. $msg" ;
		}
	}

	$data[] = array(
		'error'		=> $error,
		'message'	=> $message,
	);
	$data_json = json_encode($data);
	$error = json_last_error();
	if ($error != JSON_ERROR_NONE) {	
		echo json_last_error_msg();
	}
	echo $data_json;
	
	//
	//<form id="form" action="upload.php" method="post" enctype="multipart/form-data">
    //<input type="file" name="file_att" id="file_att" size = "30px;" class="easyui-validatebox" required="true"/>
	//<input type="hidden" name="MAX_FILE_SIZE" value="5000">
	//<input type="submit" value="Upload Image" name="submit">
	//</form>
	//
?>
