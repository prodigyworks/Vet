<?php
	require('orderreportlib.php');
	
	$pdf = new OrderReport( 'P', 'mm', 'A4', $_GET['id']);
	$pdf->Output();
?>