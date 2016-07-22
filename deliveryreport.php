<?php
	require('deliveryreportlib.php');
	
	$pdf = new DeliveryReport( 'P', 'mm', 'A4', $_GET['id']);
	$pdf->Output();
?>