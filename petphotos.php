<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$petid = $_POST['id'];
	
	$qry = "SELECT A.*, B.name AS petname, C.name, C.image
		  	FROM  {$_SESSION['DB_PREFIX']}petimages A
		  	INNER JOIN {$_SESSION['DB_PREFIX']}pet B
		  	ON B.id = A.petid
		  	INNER JOIN {$_SESSION['DB_PREFIX']}images C
		  	ON C.id = A.imageid
		  	WHERE A.petid = $petid
		  	ORDER BY A.id DESC";
	$result = mysql_query($qry);
	$node = "first";
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$imageid = $member['imageid'];
			$title = $member['title'];
			$name = $member['name'];
			$content = $member['image'];
			$dirname = "uploads/image$imageid";
			
			if (! is_dir($dirname)) {
				try {
					if (! is_dir($dirname)) {
						error_reporting(0);
						
						mkdir($dirname);
						chmod($dirname, 0777);
					}
	
					file_put_contents("$dirname/$name", ($content));
					
				} catch (Exception $e) {
					logError($e->getMessage(), false);
				}
			}
						
			echo "<a class='$node' href='$dirname/$name' title='$title'><img src='$dirname/$name' alt='$title'></a>\n";
			
			$node = "";
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}
?>