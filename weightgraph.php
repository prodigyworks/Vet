<?php
	include("system-header.php");
	
	$name = "Unknown";
	$firstdate = null;
	$lastdate = null;
	
	$sql = "SELECT name, lowerweight, upperweight, targetweight
			FROM {$_SESSION['DB_PREFIX']}pet
			WHERE id = {$_GET['id']}";
	$result = mysql_query($sql);
	
	if (! $result) logError("Error: $sql " . mysql_error());
	
	while (($member = mysql_fetch_assoc($result))) {
		$name = $member['name'];
		$lowerweight = $member['lowerweight'];
		$upperweight = $member['upperweight'];
		$targetweight = $member['targetweight'];
	}
?>
<span style="position:absolute; z-index:20000; margin-left:920px" class="rgap5 wrapper"><a class='rgap2 link1' href="javascript:back()"><em><b><img src='images/back2.png' /> Back</b></em></a></span>
<br>
<div id="chartContainer"></div>
<script type="text/javascript" src="js/jquery.canvasjs.min.js"></script>
<script>
	function back() {
		window.location.href = "<?php echo base64_decode($_GET['puri']); ?>";
	}
	
	$(document).ready(
			function() {
				var chart = new CanvasJS.Chart("chartContainer",
						{

							title:{
								text: "Weight of <?php echo $name; ?>",
								fontSize: 30
							},
				                        animationEnabled: true,
							axisX:{

								gridColor: "Silver",
								tickColor: "silver",
								valueFormatString: "DD/MMM/YY"

							},                        
				                        toolTip:{
				                          shared:true
				                        },
							theme: "theme2",
							axisY: {
								gridColor: "Silver",
								tickColor: "silver"
							},
							legend:{
								verticalAlign: "center",
								horizontalAlign: "right"
							},
							data: [
							{        
								type: "line",
								showInLegend: true,
								lineThickness: 2,
								name: "Weight (Kg)",
								markerType: "square",
								color: "#F08080",
								dataPoints: [
<?php 
	$sql = "SELECT 
			weight, 
			DATE_FORMAT(weightdate, '%Y') AS weightyear,
			DATE_FORMAT(weightdate, '%m') AS weightmonth,
			DATE_FORMAT(weightdate, '%d') AS weightday
			FROM {$_SESSION['DB_PREFIX']}petweight
			WHERE petid = {$_GET['id']}";
	$result = mysql_query($sql);
	
	if (! $result) logError("Error: $sql " . mysql_error());
	
	while (($member = mysql_fetch_assoc($result))) {
		$year = $member['weightyear'];
		$month = $member['weightmonth'] - 1;
		$day = $member['weightday'];
		$weight = $member['weight'];
		
		if ($firstdate == null) {
			$firstdate = array($year, $month, $day);
		}
		
		$lastdate = array($year, $month, $day);
		
		echo "{ x: new Date($year, $month, $day), y: $weight },\n";
	}
?>												
								]
							},
							{        
								type: "line",
								showInLegend: true,
								name: "Lower Weight",
								color: "#20B2AA",
								lineThickness: 2,

								dataPoints: [
									{ x: new Date(<?php echo $firstdate[0]; ?>, <?php echo $firstdate[1] - 1; ?>, <?php echo $firstdate[2]; ?>), y: <?php echo $lowerweight; ?> },
									{ x: new Date(<?php echo $lastdate[0]; ?>, <?php echo $lastdate[1] - 1; ?>, <?php echo $lastdate[2]; ?>), y: <?php echo $lowerweight; ?> }
								]
							},
							{        
								type: "line",
								showInLegend: true,
								name: "Upper Weight",
								color: "#B220AA",
								lineThickness: 2,

								dataPoints: [
									{ x: new Date(<?php echo $firstdate[0]; ?>, <?php echo $firstdate[1] - 1; ?>, <?php echo $firstdate[2]; ?>), y: <?php echo $upperweight; ?> },
									{ x: new Date(<?php echo $lastdate[0]; ?>, <?php echo $lastdate[1] - 1; ?>, <?php echo $lastdate[2]; ?>), y: <?php echo $upperweight; ?> }
								]
							},
							{        
								type: "line",
								showInLegend: true,
								name: "Target Weight",
								color: "#AAB220",
								lineThickness: 2,

								dataPoints: [
									{ x: new Date(<?php echo $firstdate[0]; ?>, <?php echo $firstdate[1] - 1; ?>, <?php echo $firstdate[2]; ?>), y: <?php echo $targetweight; ?> },
									{ x: new Date(<?php echo $lastdate[0]; ?>, <?php echo $lastdate[1] - 1; ?>, <?php echo $lastdate[2]; ?>), y: <?php echo $targetweight; ?> }
								]
							}

							
							],
				          legend:{
				            cursor:"pointer",
				            itemclick:function(e){
				              if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
				              	e.dataSeries.visible = false;
				              }
				              else{
				                e.dataSeries.visible = true;
				              }
				              chart.render();
				            }
				          }
						});

				chart.render();
			}
		);
</script>
<?php 
	include("system-footer.php");
?>