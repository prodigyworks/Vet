<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$json = array();
	
	$siteid = $_GET['siteid'];
	$qry = "SELECT DISTINCT A.memberid, B.fullname 
		FROM {$_SESSION['DB_PREFIX']}customerclientsiteuser  A 
		INNER JOIN {$_SESSION['DB_PREFIX']}members B 
		ON B.member_id = A.memberid 
		WHERE A.siteid = $siteid
		ORDER BY B.fullname";
	
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$line = array(
					"id" => $member['memberid'], 
					"name" => $member['fullname']
				);  
			
			array_push($json, $line);
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}
	
	echo json_encode($json); 
?>