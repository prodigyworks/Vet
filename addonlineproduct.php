<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$row = $_POST['rowid'];
	
?>
<tr>
	<td>
		<?php createLazyCombo("productid" . $row, "id", "description", "{$_SESSION['DB_PREFIX']}product", "", false, 45, "productid[]"); ?>
	</td>
	<td>
		<input type="number" id="qty" name="qty[]" value="" size=5  style="width:40px" />
	</td>
</tr>
