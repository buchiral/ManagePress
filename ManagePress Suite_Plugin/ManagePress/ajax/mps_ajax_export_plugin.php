<?php
global $wpdb;
global $table_prefix;
global $global_posttype_table;
global $plugname;

$posttypes_ids = $_REQUEST['f'];

$mps_safe_mode = true;
$mps_safe_mode = $_REQUEST['mps_safe_mode'];


$tmp_path		=	WP_PLUGIN_DIR."/temp_mps/ManagePress/";
$plugin_path 	=	WP_PLUGIN_DIR."/ManagePress/";
$zip_save_path	= 	WP_PLUGIN_DIR."/temp_mps/";



if(!is_dir($tmp_path)){
mkdir($tmp_path,0,true);} //create temp folder



$where = '';


// only export table, if posttype_id are not empty
if(!empty($posttypes_ids)){
	
		$where = '(';
		foreach ($posttypes_ids as $pt_id){
		$where .= ''.$pt_id.',';	
		}
		$where=substr($where, 0, -1);//delete last comma
		$where .= ')'; // (x,x,x,x,x)	


		$all_fields = $wpdb->get_results( "SELECT * FROM  `".$table_prefix."mps_core_fields` where posttype_id in ".$where." ",ARRAY_A);

		$sql 			= 	rb_create_insert_query('mps_core_fields',$where);
		$export_string .= '$import_sql[0] ="'.replace($sql).'";'."\n\n";
		
		$sql 			=	rb_create_insert_query('mps_core_posttype',$where);
		$export_string .= '$import_sql[1] ="'.replace($sql).'";'."\n\n";

		
		// create sql for all posttype-tables
		$index = 2;
		foreach($global_posttype_table as $pt_row){
			
			if(in_array($pt_row['ID'],$posttypes_ids)){
			$string 	= 	mps_export_show_create_sql('mps_pt_'.$pt_row['posttype']);
			$string 	=	'$import_sql['.$index.']="'.replace($string).'";';
			$export_string .= $string."\n\n";
			$index++;
			}
		
		}
		
		// create sql for all field-tables
		foreach ($all_fields as $field) {
			$field_table_name	= $field['field_table_name'];
			
			$string 			= 	mps_export_show_create_sql('mps_field_'.$field_table_name);
			$string 			=	'$import_sql['.$index.']="'.replace($string).'";';
			$export_string 		.= $string."\n\n";
		
		$index++;
		}
}



// copy the whole plugin to the temp ordner
$empty = copy_directory($plugin_path,$tmp_path);


// generate new mps_config.php
$myFile			= $tmp_path."mps_config.php";

$filecontent 	= '<?php
					$mps_safe_mode = '.$mps_safe_mode.';
					
					$import_sql = \'\';
					
					'.$export_string.'   
					?>';

$handle 		= fopen($myFile, 'w');

	if (!fwrite($handle, $filecontent)) {
			
			}

    fclose($handle);




// -----------------------------------------------------------------
// -----------------------------------------------------------------
// Generate ZIP File------------------------------------------------
// -----------------------------------------------------------------
// -----------------------------------------------------------------


$newplugin_name	= 	'ManagePress';
$archive_name 	= 	'ManagePress_'.uniqid().'.zip';



// increase script timeout value
ini_set("max_execution_time", 300);
// create object
$zip = new ZipArchive();
// open archive
if ($zip->open($zip_save_path."".$archive_name, ZIPARCHIVE::CREATE) !== TRUE) {
die ("Could not open archive");
}
// initialize an iterator
// pass it the directory to be processed
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmp_path));
	// iterate over the directory
	// add each file found to the archive
		foreach ($iterator as $key=>$value) {
			$file_path 	= realpath($key);
			$plugin_dir = realpath($tmp_path).'\\';
			//$zip_path 	= $newplugin_name.'\\'.str_replace($plugin_dir, '', $file_path);
			$zip_path 	= "ManagePress/".str_replace($plugin_dir, '', $file_path);
			
			$zip->addFile($key, $zip_path) or die ("ERROR: Could not add file: $key");
		}
// close and save archive
$zip->close();


rrmdir($tmp_path); // delete Temp-folder


$archiv_hhtp = '';

echo "<h2>Plugin Export</h2>";
echo "<br>Exportarchive created successfully and is ready to download.";
echo '<br>';
echo  '<a href="'.plugins_url().'/temp_mps/'.$archive_name.'" >'.$archive_name.'</a> ';;
echo '<br>';

print_r($array);







// -----------------------------------------------------------------
// -----------------------------------------------------------------
// Functions--------------------------------------------------------
// -----------------------------------------------------------------
// -----------------------------------------------------------------
function mps_export_show_create_sql($select_table){
	global $wpdb;
	global $table_prefix;
	
	$tablename_full = $table_prefix.''.$select_table;
	$result = $wpdb->get_row( 'SHOW CREATE TABLE '.$tablename_full.' ',ARRAY_A,0);
	
	$sql_create = $result['Create Table'].';';
	$table 		= $result['Table'];
	$new_table 	= '%prefix%'.$select_table;
	
	$exe = str_replace($table, $new_table, $sql_create);
	$out =  	$exe;

	return $out;
};

function rb_create_insert_query($select_table,$where){
	global $wpdb;
	global $table_prefix;


	$tablename_full = $table_prefix.''.$select_table;
	$new_table 	= '%prefix%'.$select_table;
	$extra = '';
	
	switch ($select_table) {
		case 'mps_core_fields':
			$extra = 'where posttype_id in '.$where;
			break;
		case 'mps_core_posttype':
			$extra = 'where id in '.$where;
			break;
		}
		

	$all= $wpdb->get_results( 'SELECT * FROM '.$tablename_full.' '.$extra.'  ' ,ARRAY_A);

	$query .= 'INSERT INTO '.$new_table.' VALUES ';

	$query .= "\n";
	// for each row	
	$i = 1;
	foreach($all as $row){
			
		
		$query .= '(';
					
					// for each column
						$max = count($row);
						$ii = 1;
					foreach($row as $col){
					$query .= "'".$col."'";
						
						if( $ii < $max){
						$query .= ",";	
						} 
						
					$ii++;
					};
		
		$query.=')';
		
		if( $i < count($all)){
			$query .= ",";	
		} else {
		$query .= ";";	
		}
		
		
$query .= "\n";
		$i++;
		};
 return $query;
};

// function 
function replace($str){
	$out = $str;
	$out = addcslashes($out,'"');
	$out = str_replace('%prefix%', '".$table_prefix."', $out);
	return $out;
}


// function 
function copy_directory( $source, $destination ) {
	if ( is_dir( $source ) ) {
		@mkdir( $destination );
		$directory = dir( $source );
		while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
			if ( $readdirectory == '.' || $readdirectory == '..' ) {
				continue;
			}
			$PathDir = $source . '/' . $readdirectory; 
			if ( is_dir( $PathDir ) ) {
				copy_directory( $PathDir, $destination . '/' . $readdirectory );
				continue;
			}
			copy( $PathDir, $destination . '/' . $readdirectory );
		}
 
		$directory->close();
	}else {
		copy( $source, $destination );
	}
}


function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}
die();
?>