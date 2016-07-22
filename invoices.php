<?php
	require_once("crud.php");
	
	class InvoiceCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createConfirmDialog("confirmRemoveDialog", "Confirm removal ?", "confirmRemoval");
			createDocumentLink();
		}
		
		public function afterInsertRow() {
?>
			var status = rowData['status'];

			if (status == "1") {
				$(this).jqGrid('setRowData', rowid, false, { color: '#0000FF' });
		   	}
<?php
		}
		
		public function postUpdateEvent($invoiceid) {
			$items = json_decode($_POST['item_serial'], true);
			$memberid = getLoggedOnMemberID();
			
			$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}invoiceitem 
					WHERE invoiceid = $invoiceid";
			
			$result = mysql_query($qry);
			
			if (! $result) {
				logError($qry . " - " . mysql_error());
			}
			
			foreach ($items as $k=>$item) {
				$qty = $item['quantity'];
				$vatrate = $item['vatrate'];
				$linetotal = $item['linetotal'];
				$vat = $item['vat'];
				$unitprice = $item['priceeach'];
				$productid = $item['productid'];
				$sequence = $item['sequence'];
				
				$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}invoiceitem 
						(invoiceid, sequence, quantity, priceeach, vatrate, vat, linetotal, 
						productid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) 
						VALUES 
						($invoiceid, $sequence, '$qty', '$unitprice', $vatrate, '$vat', $linetotal, 
						'$productid', NOW(), $memberid , NOW(), $memberid)";
				
				$result = mysql_query($qry);
				
				if (! $result) {
					logError($qry . " - " . mysql_error());
				}
			}
			
			$sql = "UPDATE {$_SESSION['DB_PREFIX']}invoice SET
					status = 0,
					converteddatetime = null,
					metacreateddate = NOW()
					WHERE id = $invoiceid";
			$result = mysql_query($sql);
			
			if (! $result) {
				logError($sql . " - " . mysql_error());
			}
		}
		
		public function postInsertEvent() {
			$invoiceid = mysql_insert_id();
			$items = json_decode($_POST['item_serial'], true);
			$memberid = getLoggedOnMemberID();
			
			foreach ($items as $k=>$item) {
				$qty = $item['quantity'];
				$vatrate = $item['vatrate'];
				$linetotal = $item['linetotal'];
				$vat = $item['vat'];
				$unitprice = $item['priceeach'];
				$productid = $item['productid'];
				$sequence = $item['sequence'];
				
				$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}invoiceitem 
						(invoiceid, sequence, quantity, priceeach, vatrate, vat, linetotal, 
						productid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) 
						VALUES 
						($invoiceid, $sequence, '$qty', '$unitprice', $vatrate, '$vat', $linetotal, 
						'$productid', NOW(), $memberid , NOW(), $memberid)";
				
				logError("SQL:$qry", false);
				
				$result = mysql_query($qry);
				
				if (! $result) {
					logError($qry . " - " . mysql_error());
				}
			}
			
		}
		
		public function postAddScriptEvent() {
?>
			$("#customerid").val("").trigger("change");
			$("#clientid").val("").trigger("change");
			$("#crudaddbutton").show();
			$("#revision").val("1");
			$("#deliverycharge").val("0.00");
			$("#discount").val("0.00");
			$("#total").val("0.00");
			$("#invoicedate").val("<?php echo date("d/m/Y"); ?>");
			$("#takenbyid").val("<?php echo getLoggedOnMemberID(); ?>");
			$("#invoiceitemdialog input, #invoiceitemdialog select").removeAttr("disabled");
			itemArray = [];
			
			populateTable();
<?php 
		}
		
		public function postEditScriptEvent() {
			$this->showInvoice(false);
?>
			$("#invoiceitemdialog input, #invoiceitemdialog select").removeAttr("disabled");
			$("#crudaddbutton").show();
<?php 			
		}
		
		public function postViewScriptEvent() {
			$this->showInvoice(true);
?>
			$("#invoiceitemdialog input, #invoiceitemdialog select").attr("disabled", true);
			$("#crudaddbutton").hide();
<?php 			
		}
		
		public function showInvoice($readonly) {
?>
			$("#revision").val(parseInt($("#revision").val()) + 1);
			
			showHeader();
			
			callAjax(
					"finddata.php", 
					{ 
						sql: "SELECT A.*, B.description FROM <?php echo $_SESSION['DB_PREFIX'];?>invoiceitem A LEFT OUTER JOIN <?php echo $_SESSION['DB_PREFIX'];?>product B ON B.id = A.productid WHERE A.invoiceid = " + currentCrudID + " ORDER BY A.sequence"
					},
					function(data) {
						itemArray = data;
						
						populateTable(data);
					},
					false
				);
<?php 
		}
			
		public function editScreenSetup() {
			include("invoiceform.php");
		}
		
		public function postScriptEvent() {
?>
			var currentID = 0;
			var currentItem = -1;
			var itemArray = [];
			
			function showHeader() {
				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT A.*, B.id AS clientid, B.customerid, B.invoiceaddress1, B.invoiceaddress2, " +
								 "B.invoiceaddress3, B.invoicecity, B.invoicepostcode " +
								 "FROM <?php echo $_SESSION['DB_PREFIX'];?>customerclientsite A " +
								 "INNER JOIN <?php echo $_SESSION['DB_PREFIX'];?>customerclient B " +
								 "ON B.id = A.clientid " +
								 "WHERE A.id = " + $("#siteid").val()
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								var invoiceaddress = "";
								var deliveryaddress = "";
								
								if (node.deliveryaddress1 != "") deliveryaddress += node.deliveryaddress1+ "\n";
								if (node.deliveryaddress2!= "") deliveryaddress += node.deliveryaddress2+ "\n";
								if (node.deliveryaddress3!= "") deliveryaddress += node.deliveryaddress3+ "\n";
								if (node.deliverycity!= "") deliveryaddress += node.deliverycity+ "\n";
								if (node.deliverypostcode!= "") deliveryaddress += node.deliverypostcode+ "\n";
								
								if (node.invoiceaddress1!= "") invoiceaddress += node.invoiceaddress1+ "\n";
								if (node.invoiceaddress2!= "") invoiceaddress += node.invoiceaddress2+ "\n";
								if (node.invoiceaddress3!= "") invoiceaddress += node.invoiceaddress3+ "\n";
								if (node.invoicecity!= "") invoiceaddress += node.invoicecity+ "\n";
								if (node.invoicepostcode!= "") invoiceaddress += node.invoicepostcode+ "\n";
								
								if (deliveryaddress == "") {
									deliveryaddress = invoiceaddress;
								}
								
								$("#invoiceaddress").val(invoiceaddress);
								$("#deliveryaddress").val(deliveryaddress);
								
								$("#customerid").val(node.customerid);
								$("#clientid").val(node.clientid);
							}
						},
						false
					);
			}
			
			function customerid_onchange() {
				$.ajax({
						url: "createclientcombo.php",
						dataType: 'html',
						async: false,
						data: {
							customerid: $("#customerid").val()
						},
						type: "POST",
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown);
						},
						success: function(data) {
							$("#clientid").html(data).trigger("change");
						}
					});
			}
			
			function clientid_onchange() {
				$.ajax({
						url: "createclientsitecombo.php",
						dataType: 'html',
						async: false,
						data: {
							clientid: $("#clientid").val()
						},
						type: "POST",
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown);
						},
						success: function(data) {
							$("#siteid").html(data).trigger("change");
						}
					});
			}
			
			function siteid_onchange() {
				showHeader();
			}
			
			function total_onchange() {
				calculate_total();
			}
			
			function calculate_total() {
				var total;
				var deliverycharge;
				var discount;
				
				deliverycharge = parseFloat($("#deliverycharge").val());
				discount = parseFloat($("#discount").val());
				
				total = parseFloat($("#total").val());
				total -= deliverycharge;
				
				if (total < 0) {
					total = 0;
				}
				
				total -= (total * (discount) / 100);
				
				$("#discount").val(new Number(discount).toFixed(2));
				$("#deliverycharge").val(new Number(deliverycharge).toFixed(2));
				$("#total").val(new Number(total).toFixed(2));
			}
			
			function productid_onchange() {
				callAjax(
						"finddata.php", 
						{ 
							sql: "SELECT A.rspnet, A.productcode, B.priceeach, B.qtyfrom, B.qtyto FROM <?php echo $_SESSION['DB_PREFIX'];?>product A LEFT OUTER JOIN <?php echo $_SESSION['DB_PREFIX'];?>pricebreak B ON B.productid = A.id WHERE A.id = " + $("#item_productid").val()
						},
						function(data) {
							var i;
							
							for (i = 0; i < data.length; i++) {
								var node = data[i];
								
								if (i == 0) {
									/* Default to unit price. */
									$("#item_unitprice").val(new Number(node.rspnet).toFixed(2)).trigger("change");
								}
								
								$("#item_productcode").val(node.productcode);
								
								if (node.qtyfrom != null) {
									var qty = parseInt($("#item_quantity").val());
									
									if (node.qtyfrom <= qty && node.qtyto >= qty) {
										/* Use price break. */
										$("#item_unitprice").val(new Number(node.priceeach).toFixed(2)).trigger("change");
									}
								}
							}
						}
					);
			}
			
			function qty_onchange(node) {
				var qty = parseInt($("#item_quantity").val());
				var unitprice = parseFloat($("#item_unitprice").val());
				var vatrate = parseFloat($("#item_vatrate").val());

				if (isNaN(unitprice)) {
					unitprice = 0;
				}
				
				if (isNaN(vatrate)) {
					vatrate = 0;
				}
				
				if (isNaN(qty)) {
					qty = 0;
				}
				
				var total = parseFloat(qty * unitprice);
				var vat = total * (vatrate / 100);
				
				total += vat;
				
				$("#item_vatrate").val(new Number(vatrate).toFixed(2));
				$("#item_vat").val(new Number(vat).toFixed(2));
				$("#item_unitprice").val(new Number(unitprice).toFixed(2));
				$("#item_quantity").val(new Number(qty).toFixed(0));
				$("#item_linetotal").val(new Number(total).toFixed(2));
			}
			
			function printInvoice(id) {
				window.open("invoicereport.php?id=" + id);
			}
			
			function populateTable(data) {
				var total = 0;
				var html = "<TABLE width='100%' class='grid list'><THEAD><?php createHeader(); ?></THEAD>";
				
				if (data != null) {
    				data.sort(
    						function(a, b) {
    						    if(a.sequence < b.sequence) return -1;
    						    if(a.sequence > b.sequence) return 1;
    						    
    						    return 0;
    						}
    					);
				}
										
				$("#item_serial").val(JSON.stringify(data));
											
				if (data != null) {
					for (var i = 0; i < data.length; i++) {
						var node = data[i];
						
						if (node.description != null) {
							html += "<TR>";
							html += "<TD>" +
									"<img src='images/edit.png'  title='Edit item' onclick='editItem(" + i + ")' />&nbsp;" +
									"<img src='images/delete.png'  title='Remove item' onclick='removeItem(" + i + ")' />&nbsp;";
							
							if (i > 0) {
								html += "<img src='images/up.png'  title='Move up' onclick='moveUpItem(" + i + ")' />&nbsp;";
								
							} else {
								html += "<img src='images/up.png'  style='visibility:hidden' />&nbsp;";
							}
							
							if (i < (data.length - 1)) {
								html += "<img src='images/down.png'  title='Move down' onclick='moveDownItem(" + i + ")' />";
								
							} else {
								html += "<img src='images/down.png'  style='visibility:hidden' />&nbsp;";
							}
															
							html +=
									"</TD>";
							html += "<TD>" + node.description + "</TD>";
							html += "<TD align=right>" + new Number(node.quantity).toFixed(0) + "</TD>";
							html += "<TD align=right>" + new Number(node.priceeach).toFixed(2) + "</TD>";
							html += "<TD align=right>" + new Number(node.vatrate).toFixed(2) + "</TD>";
							html += "<TD align=right>" + new Number(node.vat).toFixed(2) + "</TD>";
							html += "<TD align=right>" + new Number(node.linetotal).toFixed(2) + "</TD>";
							html += "</TR>\n";
							
							total += parseFloat(node.linetotal);
						}
					}
				}
				
				if ($("#deliverycharge").val() == "6.50" || $("#deliverycharge").val() == "0.00") {
					if (total < 75) {
						$("#deliverycharge").val("6.50");
						
					} else {
						$("#deliverycharge").val("0.00");
					}
				}
				
				$("#total").val(new Number(total).toFixed(2));
				
				calculate_total();

				html = html + "</TABLE>";
				
				$("#divtable").html(html);
			}
			
			function saveQuoteItem() {
				if (! verifyStandardForm("#invoiceitemform")) {
					pwAlert("Invalid form");
					return false;
				}
				
				if (currentItem == -1) {
					var lastsequence = 1;
					
					if (itemArray.length > 0) {
						lastsequence = itemArray[itemArray.length - 1].sequence + 1; 
					}
					
				} else {
					lastsequence = itemArray[currentItem].sequence; 
				}

				var item = {
						id: $("#item_id").val(),
						sequence: lastsequence,
						quantity: $("#item_quantity").val(),
						priceeach: $("#item_unitprice").val(),
						vatrate: $("#item_vatrate").val(),
						vat: $("#item_vat").val(),
						linetotal: $("#item_linetotal").val(),
						productid: $("#item_productid").val(),
						description: $("#item_productid_lazy").val()
					};

				if (currentItem == -1) {
					itemArray.push(item);
					
				} else {
					itemArray[currentItem] = item;
				}
				
				populateTable(itemArray);
				
				return true;
			}
			
			function moveUpItem(id) {
				var sequence = itemArray[id].sequence;
				var prevsequence = itemArray[id - 1].sequence;
				
				itemArray[id].sequence = prevsequence;
				itemArray[id - 1].sequence = sequence;
				
				populateTable(itemArray)
			}
			
			function moveDownItem(id) {
				var sequence = itemArray[id].sequence;
				var nextsequence = itemArray[id + 1].sequence;
				
				itemArray[id].sequence = nextsequence;
				itemArray[id + 1].sequence = sequence;
				
				populateTable(itemArray)
			}
			
			function removeItem(id) {
				currentItem = id;
				
				$("#confirmRemoveDialog .confirmdialogbody").html("You are about to approve this item.<br>Are you sure ?");
				$("#confirmRemoveDialog").dialog("open");
			} 
			
			function confirmRemoval() {
				var newItemArray = [];
				var i;
				
				$("#confirmRemoveDialog").dialog("close");
				
				for (i = 0; i < itemArray.length; i++) {
					if (currentItem != i) {
						newItemArray.push(itemArray[i]);
					}
				}
				
				itemArray = newItemArray;
				
				populateTable(itemArray);
			}
			
			function editItem(id) {
				currentItem = id;
				var node = itemArray[id];
			
				$("#item_itemid").val(node.id);
				$("#item_productid").val(node.productid).trigger("change");
				$("#item_productid_lazy").val(node.description);
				$("#item_quantity").val(node.quantity);
				$("#item_vat").val(node.vat);
				$("#item_vatrate").val(node.vatrate);
				$("#item_unitprice").val(node.priceeach);
				$("#item_linetotal").val(node.linetotal);
				
				$('#invoiceitemdialog').dialog('open');				
			}
			
			function addQuoteItem() {
				currentItem = -1;
				
				$("#item_itemid").val("0");
				$("#item_productid").val("0");
				$("#item_productid_lazy").val("");
				$("#item_quantity").val("1");
				$("#item_vatrate").val("<?php echo getSiteConfigData()->vatrate; ?>");
				$("#item_vat").val("0.00");
				$("#item_unitprice").val("0.00");
				$("#item_linetotal").val("0.00");
				
				$('#invoiceitemdialog').dialog('open');				
			
			}
			
			function validateForm() {
				return true;
			}
			
			$(document).ready(
					function() {
						$("#item_productid").change(productid_onchange);
						$("#customerid").change(customerid_onchange);
						$("#clientid").change(clientid_onchange);
						$("#siteid").change(siteid_onchange);
						
						$("#invoiceitemdialog").dialog({
								modal: true,
								autoOpen: false,
								show:"fade",
								closeOnEscape: true,
								width: 690,
								hide:"fade",
								title:"Quote Item",
								open: function(event, ui){
									
								},
								buttons: {
									"Save": function() {
										if (saveQuoteItem()) {
											$(this).dialog("close");
											
										}
									},
									Cancel: function() {
										$(this).dialog("close");
									}
								}
							});
					}
				);


			function orderReference(node) {
				return "<?php echo getSiteConfigData()->bookingprefix; ?>" + padZero(node.orderid, 6);
			}

			function bookingReference(node) {
				return "<?php echo getSiteConfigData()->invoiceprefix; ?>" + padZero(node.id, 6);
			}

			function editDocuments(node) {
				viewDocument(node, "addinvoicedocument.php", node, "invoicedocs", "invoiceid");
			}
	
<?php			
		}
	}
	
	$crud = new InvoiceCrud();
	$crud->dialogwidth = 840;
	$crud->title = "Invoices";
	$crud->table = "{$_SESSION['DB_PREFIX']}invoice";
	$crud->sql = "SELECT A.*, B.name AS sitename, C.fullname AS takenbyname, D.name AS clientname, E.name AS customername
				  FROM  {$_SESSION['DB_PREFIX']}invoice A
				  INNER JOIN  {$_SESSION['DB_PREFIX']}customerclientsite B
				  ON B.id = A.siteid
				  LEFT OUTER JOIN  {$_SESSION['DB_PREFIX']}members C
				  ON C.member_id = A.takenbyid
				  INNER JOIN  {$_SESSION['DB_PREFIX']}customerclient D
				  ON D.id = B.clientid
				  INNER JOIN  {$_SESSION['DB_PREFIX']}customer E
				  ON E.id = D.customerid
				  ORDER BY A.id DESC";
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
				'name'       => 'bookingref',
				'function'   => 'bookingReference',
				'sortcolumn' => 'A.id',
				'type'		 => 'DERIVED',
				'length' 	 => 17,
				'editable'	 => false,
				'bind' 	 	 => false,
				'filter'	 => false,
				'label' 	 => 'Invoice Number'
			),
			array(
				'name'       => 'customername',
				'length' 	 => 20,
				'editable'	 => false,
				'bind'	  	 => false,
				'label' 	 => 'Customer'
			),			
			array(
				'name'       => 'clientname',
				'length' 	 => 20,
				'editable'	 => false,
				'bind'	  	 => false,
				'label' 	 => 'Customer Client'
			),			
			array(
				'name'       => 'siteid',
				'type'       => 'LAZYDATACOMBO',
				'length' 	 => 30,
				'label' 	 => 'Site',
				'table'		 => 'customerclientsite',
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'sitename',
				'table_name' => 'name'
			),
			array(
				'name'       => 'revision',
				'length' 	 => 10,
				'readonly'	 => true,
				'label' 	 => 'Revision'
			),			
			array(
				'name'       => 'invoicedate',
				'length' 	 => 12,
				'datatype'   => 'date',
				'label' 	 => 'Invoice Date'
			),
			array(
				'name'       => 'orderref',
				'function'   => 'orderReference',
				'sortcolumn' => 'A.orderid',
				'type'		 => 'DERIVED',
				'length' 	 => 17,
				'editable'	 => false,
				'bind' 	 	 => false,
				'filter'	 => false,
				'label' 	 => 'Order Number'
			),
			array(
				'name'       => 'takenbyid',
				'type'       => 'DATACOMBO',
				'length' 	 => 18,
				'label' 	 => 'Taken By',
				'table'		 => 'members',
				'required'	 => true,
				'table_id'	 => 'member_id',
				'alias'		 => 'takenbyname',
				'table_name' => 'fullname'
			),
			array(
				'name'       => 'deliverycharge',
				'length' 	 => 13,
				'datatype'   => 'double',
				'align'		 => 'right',
				'label' 	 => 'Delivery Charge'
			),
			array(
				'name'       => 'discount',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Discount %'
			),	
			array(
				'name'       => 'total',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Total'
			)	
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Documents',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'editDocuments'
			),
			array(
				'title'		  => 'Print',
				'imageurl'	  => 'images/print.png',
				'script' 	  => 'printInvoice'
			)
		);
		
	$crud->messages = array(
			array('id'		  => 'invoiceid')
		);
		
	$crud->run();
?>
