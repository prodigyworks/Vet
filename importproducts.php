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

	if (isset($_FILES['productfile']) && $_FILES['productfile']['tmp_name'] != "") {
		if ($_FILES["productfile"]["error"] > 0) {
			echo "Error: " . $_FILES["productfile"]["error"] . "<br />";
			
		} else {
		  	echo "Upload: " . $_FILES["productfile"]["name"] . "<br />";
		  	echo "Type: " . $_FILES["productfile"]["type"] . "<br />";
		  	echo "Size: " . ($_FILES["productfile"]["size"] / 1024) . " Kb<br />";
		  	echo "Stored in: " . $_FILES["productfile"]["tmp_name"] . "<br>";
		}
		
		$subcat1 = "";
		$row = 1;
		
		if (($handle = fopen($_FILES['productfile']['tmp_name'], "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        if ($row++ == 1) {
		        	continue;
		        }
		        
		        $num = count($data);
		        $index = 0;
		        
		        $productcode = mysql_escape_string($data[0]);
		        $description = mysql_escape_string($data[1]);
		        		        
		        if ($data[0] != "") {
//		        	echo "<div>Product: $productcode</div>";
		        	
					$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}product 
							(
							productcode, description
							)  
							VALUES  
							(
							'$productcode', '$description'
							)";
							
					$result = mysql_query($qry);
        	
					if (! $result) {
						if (mysql_errno() != 1062) {
							logError(mysql_error() . " : " .  $qry);
						} else {
							echo "<h5>Duplicate : $productcode</h5>";
						}
					}
		        }
		    }
		    
		    fclose($handle);
			echo "<h1>" . $row . " downloaded</h1>";
		}
	}
	
	if (! isset($_FILES['productfile'])) {
?>	
		
<form class="contentform" method="post" enctype="multipart/form-data">
	<label>Upload product CSV file </label>
	<input type="file" name="productfile" id="productfile" /> 
	
	<br />
	 	
	<div id="submit" class="show">
		<input type="submit" value="Upload" />
	</div>
</form>
<?php
	}
	
	include("system-footer.php"); 
?>