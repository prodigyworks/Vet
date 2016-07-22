<?php 
	include("system-db.php"); 
	require('orderreportlib.php');
	
	start_db();

	$siteid = getLoggedOnSiteID();
	$takenbyid = getLoggedOnMemberID();
	
	$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}order 
			(
				siteid, orderdate, status, revision, takenbyid
			)
			VALUES
			(
				$siteid, NOW(), 0, 1, $takenbyid
			)";
				
	$result = mysql_query($sql);

	if (! $result) {
		logError($sql . " = " . mysql_error());
	}
	
	$orderid = mysql_insert_id();
	
	for ($row = 0; $row < count($_POST['productid']); $row++) {
		$productid = $_POST['productid'][$row];
		$qty = $_POST['qty'][$row];
		
		if ($qty <= 0 || $productid == "" || $productid == "0") {
			continue;
		}
		
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}frequentproducts 
				(
					siteid, productid, frequency
				)
				VALUES
				(
					$siteid, $productid, $qty
				)";
					
		$result = mysql_query($sql);
		
		if (mysql_errno() == 1062) {
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}frequentproducts SET
					frequency = frequency + $qty
					WHERE siteid = $siteid
					AND productid = $productid";
			
			$result = mysql_query($sql);
						
			if (! $result) {
				logError($sql . " = " . mysql_error());
			}
			
		} else if (! $result) {
			logError($sql . " = " . mysql_error());
		}
		
		$sql = "INSERT INTO {$_SESSION['DB_PREFIX']}orderitem
				(
					orderid, productid, quantity
				)
				VALUES
				(
					$orderid, $productid, $qty
				)";
					
		$result = mysql_query($sql);
	
		if (! $result) {
			logError($sql . " = " . mysql_error());
		}
	}

	$file = "uploads/order-" . session_id() . "-" . $orderid . ".pdf";
	$pdf = new OrderReport( 'P', 'mm', 'A4', $orderid);
	$pdf->Output($file, "F");
	
	mysql_query("COMMIT");
	
	sendRoleMessage("JRM", "Confirmed order", "Confirmed order ........", array($file));
	
	sendCustomerMessage(getLoggedOnCustomerID(), "Confirmed customer order", "Your order has been sent at " . date("d/m/Y h:i:sa"), array($file));
	header("location: processorderconfirm.php?orderid=$orderid");
?>
