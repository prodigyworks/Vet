<style>
.entryform .bubble {
	display: none;
}

.entryform img {
	width: 32,
	height: 32
}
</style>
<table border="0" cellspacing="4px" width='900px' style='table-layout: fixed;'>
	<tr>
		<td width='100px'>Pet Name</td>
		<td colspan="2">
			<input type="text" id="name" name="name" size="30" />
		</td>
	</tr>
	<tr>
		<td>Animal Type</td>
		<td colspan="2">
			<?php createCombo("animaltypeid", "id", "name", "{$_SESSION['DB_PREFIX']}animaltype") ?>
		</td>
	</tr>
	<tr>
		<td>Species</td>
		<td colspan="2">
			<select name="speciesid" id="speciesid">
			</select>
		</td>
	</tr>
	<tr>
		<td>Breed</td>
		<td colspan="2">
			<select name="breedid" id="breedid">
			</select>
		</td>
	</tr>
	<tr>
		<td>Profile Image</td>
		<td colspan="2">
			<img id="imageid_img" src="images/no-image.gif" height=32 /> <br />
			<input type="file" id="imageid" name="imageid" size=90 />
		</td>
	</tr>
	<tr>
		<td>Actual Weight</td>
		<td colspan="2">
			<input class="readonly" readonly type="text" name="actualweight" id="actualweight" size="12" />
		</td>
	</tr>
	<tr>
		<td>Target Weight</td>
		<td colspan="2">
			<input class="readonly" readonly type="text" name="targetweight" id="targetweight" size="12" />
		</td>
	</tr>
	<tr>
		<td>Lower Weight</td>
		<td colspan="2">
			<input class="readonly" readonly type="text" name="lowerweight" id="lowerweight" size="12" />
		</td>
	</tr>
	<tr>
		<td>Upper Weight</td>
		<td colspan="2">
			<input class="readonly" readonly type="text" name="upperweight" id="upperweight" size="12" />
		</td>
	</tr>
	<tr>
		<td>Other</td>
		<td colspan="2">
			<textarea class="tinyMCE" name="other" id="other"></textarea>
		</td>
	</tr>
</table>
<script>
	$(document).ready(
			function() {
				$("#animaltypeid").change(
						function() {
							$.ajax({
								url: "createspeciescombo.php",
								dataType: 'html',
								async: false,
								data: {
								animaltypeid: $("#animaltypeid").val()
								},
								type: "POST",
								error: function(jqXHR, textStatus, errorThrown) {
									alert(errorThrown);
								},
								success: function(data) {
									$("#speciesid").html(data).trigger("change");
								}
							});
						}
					);
				
				$("#speciesid").change(
						function() {
							$.ajax({
								url: "createbreedcombo.php",
								dataType: 'html',
								async: false,
								data: {
								speciesid: $("#speciesid").val()
								},
								type: "POST",
								error: function(jqXHR, textStatus, errorThrown) {
									alert(errorThrown);
								},
								success: function(data) {
									$("#breedid").html(data).trigger("change");
								}
							});
						}
					);
			}
		);
</script>
