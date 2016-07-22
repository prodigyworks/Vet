<style>
.entryform .bubble {
	display: none;
}
</style>
<table width="100%" cellpadding="0" cellspacing="4" class="entryformclass">
	<tbody>
		<tr valign="center">
			<td>Company</td>
			<td colspan=2>
				<?php createCombo("customerid", "id", "name", "{$_SESSION['DB_PREFIX']}customer", "", true); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Client</td>
			<td colspan=2>
				<?php createCombo("clientid", "id", "name", "{$_SESSION['DB_PREFIX']}customerclient", "", true); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Site</td>
			<td colspan=2>
				<?php createCombo("siteid", "id", "name", "{$_SESSION['DB_PREFIX']}customerclientsite", "", true); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>&nbsp;</td>
			<td>
				<div>Invoice Address</div>
				<textarea readonly id="invoiceaddress" name="invoiceaddress" cols="50" rows="6"></textarea>
			</td>
			<td>
				<div>Delivery Address</div>
				<textarea readonly id="deliveryaddress" name="deliveryaddress" cols="50" rows="6"></textarea>
			</td>
		</tr>
		<tr valign="center">
			<td>Revision</td>
			<td colspan=2>
				<input type="text" style="width:60px" id="revision" readonly name="revision">
				<input type="hidden" id="item_serial" name="item_serial" />
			</td>
		</tr>
		<tr valign="center">
			<td>Order Date</td>
			<td colspan=2>
				<input type="text" class="datepicker" id="orderdate" name="orderdate">
			</td>
		</tr>
		<tr valign="center">
			<td>Taken By</td>
			<td colspan=2>
				<?php createUserCombo("takenbyid"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Delivery Charge</td>
			<td colspan=2>
				<input type="text" style="width:70px" id="deliverycharge" name="deliverycharge" onchange="total_onchange()" value="0.00">
			</td>
		</tr>
		<tr valign="center">
			<td>Discount %</td>
			<td colspan=2>
				<input type="text" style="width:70px" id="discount" name="discount" onchange="total_onchange()" value="0.00">
			</td>
		</tr>
		<tr valign="center">
			<td>Total</td>
			<td colspan=2>
				<input type="text" style="width:70px" id="total" name="total" readonly value="0.00">
			</td>
		</tr>
	</tbody>
</table>

<table>
	<tr>
		<td>
		   	<span class="wrapper">
		   		<a disabled id="crudaddbutton" class='subapp rgap2 link1' href="javascript:addQuoteItem()">
		   			<em>
		   				<b>
		   					<img src='images/add.png' /> Add
		   				</b>
		   			</em>
		   		</a>
		   	</span>
		</td>
		<td>
		   	<span class="wrapper">
		   		<a disabled class='subapp rgap2 link1' href="javascript:printQuote(currentCrudID)">
		   			<em>
		   				<b>
		   					<img src='images/print.png' /> Print
		   				</b>
		   			</em>
		   		</a>
		   	</span>
		</td>
	</tr>
</table>
<?php 
function createHeader() {
?><tr><td>Actions</td><td>Product</td><td align='right'>Quantity</td><td align='right'>Unit Price</td><td align='right'>VAT Rate</td><td align='right'>VAT</td><td align='right'>Total</td></tr><?php 
}
?>
<div id="divtable">
	<table width="100%" class="grid list">
		<thead>
		<?php createHeader(); ?>
		</thead>
	</table>
</div>

<div id="invoiceitemdialog" class="modal">
	<table width="100%" cellpadding="0" cellspacing="4" class="entryformclass">
		<tbody>
			<tr valign="center">
				<td>Product</td>
				<td>
					<?php createLazyCombo("item_productid", "id", "description", "{$_SESSION['DB_PREFIX']}product", "", true, 80); ?>
				</td>
			</tr>
			<tr valign="center">
				<td>Product Code</td>
				<td>
					<input type="text" id="item_productcode" name="item_productcode" size=19 readonly />
				</td>
			</tr>
			<tr valign="center">
				<td>Quantity</td>
				<td>
					<input type="text" id="item_quantity" name="item_quantity" size=9 onchange="productid_onchange()" />
					<input type="hidden" id="item_id" name="item_id" />
				</td>
			</tr>
			<tr valign="center">
				<td>Unit Price</td>
				<td>
					<input type="text" id="item_unitprice" name="item_unitprice" size=9 onchange="qty_onchange()" />
				</td>
			</tr>
			<tr valign="center">
				<td>VAT Rate</td>
				<td>
					<input type="text" id="item_vatrate" name="item_vatrate" size=9 onchange="qty_onchange()" />
				</td>
			</tr>
			<tr valign="center">
				<td>VAT</td>
				<td>
					<input type="text" id="item_vat" name="item_vat" size=9 readonly />
				</td>
			</tr>
			<tr valign="center">
				<td>Line Total</td>
				<td>
					<input type="text" id="item_linetotal" name="item_linetotal" size=9 onchange="qty_onchange()" />
				</td>
			</tr>
		</tbody>
	</table>
</div>