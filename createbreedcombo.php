<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$speciesid = $_POST['speciesid'];
	
	createComboOptions("id", "name", "{$_SESSION['DB_PREFIX']}breed", "WHERE speciesid = $speciesid", false);
?>