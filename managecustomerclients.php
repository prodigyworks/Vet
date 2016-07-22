<?php
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
		
		public function postScriptEvent() {
?>
			function editDocuments(node) {
				viewDocument(node, "addcustomerdocument.php", node, "customerdocs", "customerid");
			}
	
			/* Derived invoice address callback. */
			function fullInvoiceAddress(node) {
				var address = "";
				
				if ((node.invoiceaddress1) != "" && (node.invoiceaddress1) != null) {
					address = address + node.invoiceaddress1;
				} 
				
				if ((node.invoiceaddress2) != "" && (node.invoiceaddress2) != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.invoiceaddress2;
				} 
				
				if ((node.invoiceaddress3) != "" && (node.invoiceaddress3) != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.invoiceaddress3;
				} 
				
				if ((node.invoicecity) != "" && (node.invoicecity) != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.invoicecity;
				} 
				
				if ((node.invoicepostcode) != "" && (node.invoicepostcode) != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.invoicepostcode;
				} 
				
				if ((node.invoicecountry) != "" && (node.invoicecountry) != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.invoicecountry;
				} 
				
				return address;
			}
<?php			
		}
	}
	
	$customerid = $_GET['id'];
	
	$crud = new CustomerCrud();
	$crud->dialogwidth = 650;
	$crud->title = "Clients";
	$crud->table = "{$_SESSION['DB_PREFIX']}customerclient";
	$crud->sql = "SELECT A.*, B.name AS customername
				  FROM  {$_SESSION['DB_PREFIX']}customerclient A
				  INNER JOIN {$_SESSION['DB_PREFIX']}customer B
				  ON B.id = A.customerid
				  WHERE A.customerid = $customerid
				  ORDER BY A.name";
	$crud->columns = array(
			array(
				'name'       => 'id',
				'viewname'   => 'uniqueid',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'customerid',
				'datatype'	 => 'integer',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'editable' 	 => false,
				'default'	 => $customerid,
				'label' 	 => 'Customer'
			),
			array(
				'name'       => 'customername',
				'length' 	 => 27,
				'filter'	 => false,
				'editable' 	 => false,
				'bind' 	 	 => false,
				'label' 	 => 'Customer'
			),			
			array(
				'name'       => 'accountnumber',
				'length' 	 => 17,
				'label' 	 => 'Account Number'
			),			
			array(
				'name'       => 'name',
				'length' 	 => 27,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'firstname',
				'length' 	 => 15,
				'label' 	 => 'First Name'
			),			
			array(
				'name'       => 'lastname',
				'length' 	 => 15,
				'label' 	 => 'Last Name'
			),			
			array(
				'name'       => 'invoiceaddress1',
				'length' 	 => 60,
				'showInView' => false,
				'label' 	 => 'Invoice Address 1'
			),
			array(
				'name'       => 'invoiceaddress2',
				'length' 	 => 60,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Invoice Address 2'
			),
			array(
				'name'       => 'invoiceaddress3',
				'length' 	 => 60,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Invoice Address 3'
			),
			array(
				'name'       => 'invoicecity',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'Invoice City'
			),
			array(
				'name'       => 'invoicepostcode',
				'length' 	 => 10,
				'showInView' => false,
				'label' 	 => 'Invoice Post Code'
			),
			array(
				'name'       => 'invoicecountry',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'Invoice Country'
			),
			array(
				'name'       => 'invoiceaddress',
				'length' 	 => 90,
				'editable'   => false,
				'bind'		 => false,
				'type'		 => 'DERIVED',
				'function'	 => 'fullInvoiceAddress',
				'label' 	 => 'Invoice Address'
			),
			array(
				'name'       => 'email',
				'length' 	 => 40,
				'label' 	 => 'Email'
			),
			array(
				'name'       => 'telephone',
				'length' 	 => 12,
				'label' 	 => 'Telephone'
			),
			array(
				'name'       => 'fax',
				'length' 	 => 12,
				'required' 	 => false,
				'label' 	 => 'Fax'
			)
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Documents',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'editDocuments'
			),
			array(
				'title'		  => 'Sites',
				'imageurl'	  => 'images/team.png',
				'application' => 'managecustomerclientsites.php'
			)
		);
		
	$crud->run();
?>
