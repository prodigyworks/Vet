<?php
	require_once('system-db.php');
	require_once('pdfreport.php');
	require_once("simple_html_dom.php");
	
	class InvoiceReport extends PDFReport {
		private $headermember = null;
		
		function newPage() {
			$this->AddPage();
			
			$this->Image("images/logomain.png", 135.6, 10);
			
			$this->addText( 15, 13, "JRM Facility Services Ltd", 12, 4, 'B') + 5;
			$this->addText(15, 20, getSiteConfigData()->address, 8, 3) + 9.5;
			$dynamicY = 47.5;
			
			$this->addText( 15, $dynamicY, "Customer Name & Address", 8, 3, 'B');
			$this->addText( 75, $dynamicY, "Delivery Address", 8, 3, 'B');
			$this->addText( 164, $dynamicY, "INVOICE", 8, 3, 'B');
			
			$this->addText( 145, $dynamicY + 5, "FAO:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 5, $this->headermember['firstname'] . " " . $this->headermember['lastname'], 8, 2.4, '', 30);
			
			$this->addText( 145, $dynamicY + 10, "Invoice Date:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 10, $this->headermember['invoicedate'], 8, 3);
			
			$this->addText( 145, $dynamicY + 15, "Your Acc No:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 15, $this->headermember['accountnumber'], 8, 3);

			$this->addText( 145, $dynamicY + 20, "Taken By:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 20, $this->headermember['takenbyname'], 8, 3);

			$this->addText( 145, $dynamicY + 25, "Our Invoice No:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 25, getSiteConfigData()->invoiceprefix . sprintf("%06d", $this->headermember['id'], 6), 8, 3, 'B');

			$this->addText( 145, $dynamicY + 30, "Your Order No:", 8, 3, 'B');
			$this->addText( 170, $dynamicY + 30, getSiteConfigData()->bookingprefix . sprintf("%06d", $this->headermember['orderid'], 6), 8, 3, 'B');
			
			$invoiceaddress = "";
			$deliveryaddress = "";
			
			if (trim($this->headermember['deliveryaddress1']) != "") $deliveryaddress .= $this->headermember['deliveryaddress1'] . "\n";
			if (trim($this->headermember['deliveryaddress2']) != "") $deliveryaddress .= $this->headermember['deliveryaddress2'] . "\n";
			if (trim($this->headermember['deliveryaddress3']) != "") $deliveryaddress .= $this->headermember['deliveryaddress3'] . "\n";
			if (trim($this->headermember['deliverycity']) != "") $deliveryaddress .= $this->headermember['deliverycity'] . "\n";
			if (trim($this->headermember['deliverypostcode']) != "") $deliveryaddress .= $this->headermember['deliverypostcode'] . "\n";
			
			if (trim($this->headermember['invoiceaddress1']) != "") $invoiceaddress .= $this->headermember['invoiceaddress1'] . "\n";
			if (trim($this->headermember['invoiceaddress2']) != "") $invoiceaddress .= $this->headermember['invoiceaddress2'] . "\n";
			if (trim($this->headermember['invoiceaddress3']) != "") $invoiceaddress .= $this->headermember['invoiceaddress3'] . "\n";
			if (trim($this->headermember['invoicecity']) != "") $invoiceaddress .= $this->headermember['invoicecity'] . "\n";
			if (trim($this->headermember['invoicepostcode']) != "") $invoiceaddress .= $this->headermember['invoicepostcode'] . "\n";
			
			if ($deliveryaddress == "") {
				$deliveryaddress = $invoiceaddress;
			}
			
			$this->addText(15, $dynamicY + 5, $this->headermember['customername'] . "\n" . $invoiceaddress, 8, 3.5, '', 60);
			$this->addText(75, $dynamicY + 5, $this->headermember['customername'] . "\n" . $deliveryaddress, 8, 3.5, '', 60);
			
			$this->RoundedRect(13, 46, 128, 38, 5, '1234', 'BD');
			$this->RoundedRect(143, 46, 58, 38, 5, '1234', 'BD');
			$this->Line(143, 51.5, 201, 51.5);
			$this->Line(161, 234, 200, 234);
			$this->Line(161, 240, 200, 240);
			$this->Line(161, 246, 200, 246);
			$this->Line(161, 252, 200, 252);
			$this->Line(161, 258, 200, 258);
			$this->Line(161, 264, 200, 264);
			
			if ($this->headermember['revision'] != 1) {
				$this->addText(55, $dynamicY + 38, "This is Invoice Revision No " . $this->headermember['revision'] . " and Supersedes all Previous Issues", 8, 3.5, 'B');
			}
			
			$this->addText( 10, 270, "VAT Registration No: XXX XXXX XX\nCompany Reg No: XXXXXXXX", 7, 3);
			$this->addText( 170, 270, "Printed: " . date("d/m/Y H:i"), 7, 3);
			$this->addText( 186, 273, "Page " . $this->PageNo() . " of {nb}", 7, 3);
			
			$this->SetFont('Arial','', 8);
				
			$cols=array( "Quantity"    => 18,
			             "Code"  => 28,		
						 "Description"  => 105,
			             "Price Each"  => 19.5,
			             "Line Total"  => 19.5
				);
		
			$this->addCols( $dynamicY + 45, $cols);
			
			$cols=array( "Quantity"    => "L",
			             "Code"  => "L",		
						 "Description"  => "L",
			             "Price Each"  => "R",
			             "Line Total"  => "R"
				);
			$this->addLineFormat( $cols);
			
			return $this->GetY();
		}
		
		function __construct($orientation, $metric, $size, $id) {
			$dynamicY = 0;

			start_db();
				
	        parent::__construct($orientation, $metric, $size);
			
			try {
				$sql = "SELECT A.*, DATE_FORMAT(A.invoicedate, '%d/%m/%Y') AS invoicedate,
						D.name AS customername, D.accountnumber, D.invoiceaddress1, D.invoiceaddress2, D.invoiceaddress3, 
						D.invoicecity, D.invoicepostcode, B.deliveryaddress1, B.deliveryaddress2, 
						B.deliveryaddress3, B.deliverycity, B.deliverypostcode, D.firstname, D.lastname,
						E.fullname AS takenbyname
					    FROM  {$_SESSION['DB_PREFIX']}invoice A
					    INNER JOIN  {$_SESSION['DB_PREFIX']}customerclientsite B
					    ON B.id = A.siteid
					    INNER JOIN  {$_SESSION['DB_PREFIX']}customerclient C
					    ON C.id = B.clientid
					    INNER JOIN  {$_SESSION['DB_PREFIX']}customer D
					    ON D.id = C.customerid
					    LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}members E
					    ON E.member_id = A.takenbyid
					    WHERE A.id = $id
					    ORDER BY A.id DESC";
				$result = mysql_query($sql);
				
				if ($result) {
					while (($this->headermember = mysql_fetch_assoc($result))) {
						$shipping = $this->headermember['deliverycharge'];
						$discount = $this->headermember['discount'];
						$total = 0;
						$dynamicY = $this->newPage() + 7;
						
						$sql = "SELECT A.*, B.productcode, B.description
								FROM {$_SESSION['DB_PREFIX']}invoiceitem A 
								INNER JOIN {$_SESSION['DB_PREFIX']}product B 
								ON B.id = A.productid 
								WHERE A.invoiceid = $id 
								ORDER BY A.sequence";
						$itemresult = mysql_query($sql);
						
						if ($itemresult) {
							while (($itemmember = mysql_fetch_assoc($itemresult))) {
								$line = array( 
										 "Quantity"    => $itemmember['quantity'],
							             "Code"  => $itemmember['productcode'],		
										 "Description"  => $itemmember['description'],
							             "Price Each"  => number_format($itemmember['priceeach'], 2),
							             "Line Total"  => number_format(($itemmember['priceeach'] * $itemmember['quantity']), 2)
							         );
								             
								$size = $this->addLine( $dynamicY, $line );
								$dynamicY += $size + 1;
								
								if ($dynamicY > 225) {
									$dynamicY = $this->newPage();
									$dynamicY = 102;
								}
			
								$total = $total + ($itemmember['priceeach'] * $itemmember['quantity']);

								$totalvat += $itemmember['vat'];
							}
							
						} else {
							logError($qry . " - " . mysql_error());
						}
						
						$line = array( 
								 "Quantity"    => " ",
					             "Code"  => " ",		
								 "Description"  => " " ,
					             "Price Each"  => "Goods Net:",
					             "Line Total"  => number_format($total, 2)
					         );
						             
						$size = $this->addLine( 236, $line );
						
						$line = array( 
								 "Quantity"    => " ",
					             "Code"  => " ",		
								 "Description"  => " " ,
					             "Price Each"  => "Delivery:",
								 "Line Total"  => number_format($shipping, 2)
					         );
						             
						$size = $this->addLine( 242, $line );
						
						$line = array( 
								 "Quantity"    => " ",
					             "Code"  => " ",		
								 "Description"  => " " ,
					             "Price Each"  => "Invoice Net:",
								 "Line Total"  => number_format($shipping + $total, 2)
					         );
						             
						$size = $this->addLine( 248, $line );
						$totalvat += ($shipping * (getSiteConfigData()->vatrate / 100));
						
						$line = array( 
								 "Quantity"    => " ",
					             "Code"  => " ",		
								 "Description"  => " " ,
					             "Price Each"  => "VAT:",
								 "Line Total"  => number_format($totalvat, 2)
					         );
						             
						$size = $this->addLine( 254, $line );
						
						$line = array( 
								 "Quantity"    => " ",
					             "Code"  => " ",		
								 "Description"  => " " ,
					             "Price Each"  => "Total:",
								 "Line Total"  => number_format(($totalvat + $shipping + $total) - $discount, 2)
					         );
						             
						$size = $this->addLine( 260, $line );
						

						$this->addText( 162, 265, "Pounds Sterling", 6, 3);
					}
					
				} else {
					logError($sql . " - " . mysql_error());
				}
				
			} catch (Exception $e) {
				logError($e->getMessage());
			}
			
			$this->AliasNbPages();
		}
	}
?>