<?php 
	include("system-embeddedheader.php"); 
	
	function removeFrequentProduct() {
		$id = $_POST['pk1'];
		$sql = "DELETE FROM {$_SESSION['DB_PREFIX']}frequentproducts WHERE id = $id";
		
		if (! mysql_query($sql)) {
			logError($sql . " - " . mysql_error());
		}
	}
?>
<div id="orderapp">
	<form id="orderform" method="POST" action="processorder.php">
		<table width='100%' cellspacing=0 cellpadding=0 id="ordertable">
			<tr class="header">
				<td>Product</td>
				<td>Qty</td>
			</tr>
<?php 
	$siteid = getLoggedOnSiteID();
	$sql = "SELECT A.*, B.description 
			FROM {$_SESSION['DB_PREFIX']}frequentproducts A
			INNER JOIN {$_SESSION['DB_PREFIX']}product B 
			ON B.id = A.productid 
			WHERE siteid = $siteid 
			ORDER BY A.frequency DESC 
			LIMIT 20";
	
	$result = mysql_query($sql);
	$row = 1;
	
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$row++;
			$product = mysql_escape_string($member['description']);
?>
			<tr>
				<td class="favourite">
					<div><?php echo $product; ?></div>
					<input type="hidden" id="productid" name="productid[]" value="<?php echo $member['productid']; ?>" />
				</td>
				<td>
					<input type="number" id="qty" name="qty[]" size=5 value="" style="width:40px" />
				</td>
				<td>
					<input type="button" class="btnRemove" value="x" style="width:12px" frequentid="<?php echo $member['id']; ?>" />
				</td>
			</tr>
<?php
		}
		
	} else {
		logError($sql . " - " . mysql_error());
	}
	
?>
		</table>
		<br>
		<input class="submitbutton" type="button" onclick="addProductRow()" value="Add Product"></input>
		<br>
		<input class="submitbutton" type="button" onclick="processorder()" value="Process"></input>
	</form>
	<script>
	$(document).ready(
			function() {
				addProductRow();

				$(".btnRemove").click(
						function() {
							call("removeFrequentProduct", {pk1: $(this).attr("frequentid")});
						}
					);
			}
		);
	
	function addProductRow() {
		$.ajax({
				url: "addonlineproduct.php",
				dataType: 'html',
				async: false,
				data: {
					 rowid: $('#ordertable tr:last').index() + 1 
				},
				type: "POST",
				error: function(jqXHR, textStatus, errorThrown) {
					alert(errorThrown);
				},
				success: function(data) {
					$("#ordertable tr:last").after(data);
					$("#ordertable tr:last input").first().focus();
				}
			});
	}
	
	function processorder() {
		$("#orderform").submit();
	}
	</script>
</div>
<?php include("system-embeddedfooter.php"); ?>

