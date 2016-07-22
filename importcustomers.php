<?php
	include("system-header.php"); 
	
	$substringstart = 0;
	
	function startsWith($Haystack, $Needle){
	    // Recommended version, using strpos
	    return strpos($Haystack, $Needle) === 0;
	}
	
	class PriceItem {
	    // property declaration
	    public $from = 0;
	    public $to = 0;
	}
 
	class ProductLength {
	    // property declaration
	    public $length = 0;
	    public $longline = 0;
	}

	if (isset($_FILES['customerfile']) && $_FILES['customerfile']['tmp_name'] != "") {
		if ($_FILES["customerfile"]["error"] > 0) {
			echo "Error: " . $_FILES["customerfile"]["error"] . "<br />";
			
		} else {
		  	echo "Upload: " . $_FILES["customerfile"]["name"] . "<br />";
		  	echo "Type: " . $_FILES["customerfile"]["type"] . "<br />";
		  	echo "Size: " . ($_FILES["customerfile"]["size"] / 1024) . " Kb<br />";
		  	echo "Stored in: " . $_FILES["customerfile"]["tmp_name"] . "<br>";
		}
		
		$subcat1 = "";
		$row = 1;
		$calabash = 37288;
		
		if (($handle = fopen($_FILES['customerfile']['tmp_name'], "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        $num = count($data);
		        $index = 0;
		        
		        $clientname = mysql_escape_string($data[$index++]);
		        $street = mysql_escape_string($data[$index++]);
		        $city = mysql_escape_string($data[$index++]);
		        $postcode = mysql_escape_string($data[$index++]);
		        		        
		        if ($data[3] != "") {
		        	echo "<div>Customer: $clientname - $street</div>";
		        	
					$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}customerclient
							(
								customerid, name, accountnumber
							) 
							VALUES 
							(
								$calabash, '$clientname', '$clientname'
							)";
							
					$result = mysql_query($qry);
        	
		        	if (! $result) {
						if (mysql_errno() != 1062) {
							logError($qry . " - " . mysql_error());
						}
						
						$qry = "SELECT id 
								FROM {$_SESSION['DB_PREFIX']}customerclient 
								WHERE name = '$clientname'";
						
						$result = mysql_query($qry);
						
						if($result) {
							while (($member = mysql_fetch_assoc($result))) {
								$clientid = $member['id'];
							}
						}
						
		        	} else {
			        	$clientid =  mysql_insert_id();
		        	}
		        	
					$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}customerclientsite
							(
								clientid, name, deliveryaddress1, deliverycity, deliverypostcode
							) 
							VALUES 
							(
								$clientid, '$street', '$street', '$city', '$postcode'
							)";
							
					$result = mysql_query($qry);
								
		        	if (! $result) {
						if (mysql_errno() != 1062) {
							logError($qry . " - " . mysql_error());
						}
		        	}
		        }
		    }
		    
		    fclose($handle);
			echo "<h1>" . $row . " downloaded</h1>";
		}
	}
	
	if (! isset($_FILES['customerfile'])) {
?>	
		
<form class="contentform" method="post" enctype="multipart/form-data" onsubmit="return askPassword()">
	<label>Upload customer CSV file </label>
	<input type="file" name="customerfile" id="customerfile" /> 
	
	<br />
	 	
	<div id="submit" class="show">
		<input type="submit" value="Upload" />
	</div>
</form>
<?php
	}
	
	include("system-footer.php"); 
?>