<?php
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
?>
			<script src='js/jquery.picklists.js' type='text/javascript'></script>
			
			<div id="userDialog" class="modal">
				<form id="usersForm" name="usersForm" method="post">
					<input type="hidden" id="siteid" name="siteid" />
					<input type="hidden" id="usercmd" name="usercmd" value="X" />
					<select class="listpicker" name="users[]" multiple="true" id="users" >
						<?php createComboOptions("member_id", "fullname", "{$_SESSION['DB_PREFIX']}members", "WHERE customerid IS NOT NULL AND customerid != 0", false); ?>
					</select>
				</form>
			</div>
<?php
		}
		
		/* Pre command event. */
		public function preCommandEvent() {
			if (isset($_POST['usercmd'])) {
				if (isset($_POST['users'])) {
					$counter = count($_POST['users']);
		
				} else {
					$counter = 0;
				}
				
				$siteid = $_POST['siteid'];
				$loggedinid = getLoggedOnMemberID();
				
				$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}customerclientsiteuser 
					WHERE siteid = $siteid";
				$result = mysql_query($qry);
				
				if (! $result) {
					logError(mysql_error());
				}
		
				for ($i = 0; $i < $counter; $i++) {
					$memberid = $_POST['users'][$i];
					
					$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}customerclientsiteuser 
						(
							memberid, siteid, 
							metacreateddate, metacreateduserid, 
							metamodifieddate, metamodifieduserid
						) 
						VALUES 
						(
							$memberid, $siteid, 
							NOW(), $loggedinid, 
							NOW(), $loggedinid
						)";
					$result = mysql_query($qry);
				};
			}
		}
		
		public function postScriptEvent() {
?>
			$(document).ready(
					function() {
						$("#users").pickList({
								removeText: 'Remove User',
								addText: 'Add User',
								testMode: false
							});
						
						$("#userDialog").dialog({
								autoOpen: false,
								modal: true,
								width: 800,
								title: "Users",
								buttons: {
									Ok: function() {
										$("#usersForm").submit();
									},
									Cancel: function() {
										$(this).dialog("close");
									}
								}
							});
					}
				);
				
			function userSites(siteid) {
				getJSONData('findusersites.php?siteid=' + siteid, "#users", function() {
					$("#siteid").val(siteid);
					$("#userDialog").dialog("open");
				});
			}
			
			function editDocuments(node) {
				viewDocument(node, "addcustomerdocument.php", node, "customerdocs", "customerid");
			}
	
			/* Derived delivery address callback. */
			function fullDeliveryAddress(node) {
				var address = "";
				
				if ((node.deliveryaddress1) != "" && (node.deliveryaddress1) != null) {
					address = address + node.deliveryaddress1;
				} 
				
				if ((node.deliveryaddress2) != "" && (node.deliveryaddress2) != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.deliveryaddress2;
				} 
				
				if ((node.deliveryaddress3) != "" && (node.deliveryaddress3) != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.deliveryaddress3;
				} 
				
				if ((node.deliverycity) != "" && (node.deliverycity) != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.deliverycity;
				} 
				
				if ((node.deliverypostcode) != "" && (node.deliverypostcode) != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.deliverypostcode;
				} 
				
				if ((node.deliverycountry) != "" && (node.deliverycountry) != null) {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.deliverycountry;
				} 
				
				return address;
			}
<?php			
		}
	}
	
	$clientid = $_GET['id'];
	
	$crud = new CustomerCrud();
	$crud->dialogwidth = 650;
	$crud->title = "Sites";
	$crud->table = "{$_SESSION['DB_PREFIX']}customerclientsite";
	$crud->sql = "SELECT A.*, B.name AS clientname
				  FROM  {$_SESSION['DB_PREFIX']}customerclientsite A
				  INNER JOIN {$_SESSION['DB_PREFIX']}customerclient B
				  ON B.id = A.clientid
				  INNER JOIN {$_SESSION['DB_PREFIX']}customer C
				  ON C.id = B.customerid
				  WHERE A.clientid = $clientid
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
				'name'       => 'clientid',
				'datatype'	 => 'integer',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'editable' 	 => false,
				'default'	 => $clientid,
				'label' 	 => 'Customer'
			),
			array(
				'name'       => 'clientname',
				'length' 	 => 27,
				'bind' 	 	 => false,
				'filter'	 => false,
				'editable' 	 => false,
				'label' 	 => 'Client'
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
				'name'       => 'deliveryaddress1',
				'length' 	 => 60,
				'showInView' => false,
				'label' 	 => 'Delivery Address 1'
			),
			array(
				'name'       => 'deliveryaddress2',
				'length' 	 => 60,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Delivery Address 2'
			),
			array(
				'name'       => 'deliveryaddress3',
				'length' 	 => 60,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Delivery Address 3'
			),
			array(
				'name'       => 'deliverycity',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'Delivery City'
			),
			array(
				'name'       => 'deliverypostcode',
				'length' 	 => 10,
				'showInView' => false,
				'label' 	 => 'Delivery Post Code'
			),
			array(
				'name'       => 'deliverycountry',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'Delivery Country'
			),
			array(
				'name'       => 'deliveryaddress',
				'length' 	 => 90,
				'editable'   => false,
				'bind'		 => false,
				'type'		 => 'DERIVED',
				'function'	 => 'fullDeliveryAddress',
				'label' 	 => 'Delivery Address'
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
				'title'		  => 'Users',
				'imageurl'	  => 'images/user.png',
				'script' 	  => 'userSites'
			),
			array(
				'title'		  => 'Documents',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'editDocuments'
			)
		);
		
	$crud->run();
?>