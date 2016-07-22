<?php 
	include("system-embeddedheader.php"); 
?>
<br>
<h4>Order <?php echo getSiteConfigData()->bookingprefix . sprintf("%06d", $_GET['orderid']); ?> has been processed.</h4>
<br>
<a href="system-client.php">Create New Order</a>
<?php
	include("system-embeddedfooter.php"); 
?>

