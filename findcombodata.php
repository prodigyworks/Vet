<?php
	require_once("sqlprocesstoarray.php");
	require_once('php-sql-parser.php');
	require_once('php-sql-creator.php');
	
	$json = new SQLProcessToArray();
	
	if (isset($_GET['sql'])) {
		$qry = base64_decode($_GET['sql']);
	}
	
	if (isset($_GET['name'])) {
		$name = base64_decode($_GET['name']);
	}
	
	if (isset($_GET['term'])) {
		$qry .= " HAVING $name like '%" . $_GET['term'] .  "%' ";
	}
	
	if (isset($_GET['where'])) {
		$qry .= $_GET['where'];
	}
	
	$qry .= " ORDER BY $name";
	$qry .= " LIMIT 50";
	
	$qry = str_replace("\\'", "'", $qry);
		
	echo json_encode($json->fetch($qry));
?>