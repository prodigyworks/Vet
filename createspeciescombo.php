<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$animaltypeid = $_POST['animaltypeid'];
	
	createComboOptions("id", "name", "{$_SESSION['DB_PREFIX']}species", "WHERE animaltypeid = $animaltypeid", false);
?>