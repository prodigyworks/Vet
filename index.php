<?php
	include("system-db.php");
	
	start_db();

	if (strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") ||
		strpos($_SERVER['HTTP_USER_AGENT'], "Archos ")) {
		header("location: onlineordering.php");
		
	} else {
		header("location: manageanimals.php");
	}
?>
