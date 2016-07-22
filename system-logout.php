<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	logout();

	header("location: system-login.php");
?>
