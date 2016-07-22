<?php
	require_once("system-db.php");
	require_once("crud.php");
	
	$petid = $_GET['id'];
	$memberid = getLoggedOnMemberID();
	
	class WeightCrud extends Crud {
		
		/* Post script event. */
		public function postScriptEvent() {
?>
			function weightgraph() {
				window.location.href = "weightgraph.php?id=<?php echo $_GET['id']; ?>&puri=<?php echo $_GET['puri']; ?>";
			}
<?php
		}
	}
	
	$crud = new WeightCrud();
	$crud->dialogwidth = 450;
	$crud->title = "Weights";
	$crud->table = "{$_SESSION['DB_PREFIX']}petweight";
	$crud->sql = "SELECT A.*, B.name AS petname
				  FROM  {$_SESSION['DB_PREFIX']}petweight A
				  INNER JOIN {$_SESSION['DB_PREFIX']}pet B
				  ON B.id = A.petid
				  WHERE A.petid = $petid
				  AND B.memberid = $memberid
				  ORDER BY A.weightdate";
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
				'name'       => 'petid',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'editable' 	 => false,
				'default'	 => $petid,
				'label' 	 => 'Pet'
			),
			array(
				'name'       => 'petname',
				'length' 	 => 37,
				'bind'		 => false,
				'editable'	 => false,
				'label' 	 => 'Pet Name'
			),
			array(
				'name'       => 'weightdate',
				'datatype'	 => 'date',
				'length' 	 => 12,
				'label' 	 => 'Date'
			),
			array(
				'name'       => 'weight',
				'datatype'	 => 'double',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Weight'
			)
		);

	$crud->applications = array(
			array(
				'title'		  => 'Graph',
				'imageurl'	  => 'images/barchart.png',
				'script' 	  => 'weightgraph'
			)
		);
		
	$crud->run();
?>
