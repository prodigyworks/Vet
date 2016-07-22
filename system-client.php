<?php 
	require_once("system-embeddedheader.php"); 

	$customerid = getLoggedOnCustomerID();
?>
	<form action="system-client-exec.php?ts=<?php echo time(); ?>" method="POST" id="loginForm">
		<br>
		<div><label>Client</label></div>
		<br>
<?php 
		$memberid = getLoggedOnMemberID();
		createCombo(
				"clientid", 
				"id", 
				"name", 
				"{$_SESSION['DB_PREFIX']}customerclient", 
				"WHERE A.customerid = $customerid 
				 AND A.id IN
				 (
				 	SELECT B.clientid 
				 	FROM {$_SESSION['DB_PREFIX']}customerclientsite B
				 	INNER JOIN {$_SESSION['DB_PREFIX']}customerclientsiteuser C
				 	ON C.siteid = B.id
				 	WHERE C.memberid = $memberid
				 )"
			);
		
?>
		<br>
		<br>
		<div><label>Site</label></div>

		<br>
		<SELECT id="siteid" name="siteid" style="width:200px">
			<OPTION value=""></OPTION>
		</SELECT>
		<br>
		<br>
		<input type="submit" value="Confirm"></input>
	</form>
	<script>
		$(document).ready(
				function() {
					$("#clientid").change(
							function() {
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
											$("#siteid").html(data);
										}
									});
							}
						);
				}
			);
	</script>
</div>

<?php include("system-embeddedfooter.php"); ?>					
