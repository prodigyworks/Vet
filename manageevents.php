<?php
	require_once("system-db.php");
	require_once("crud.php");
	
	$petid = $_GET['id'];
	$memberid = getLoggedOnMemberID();
	
	$crud = new Crud();
	$crud->dialogwidth = 900;
	$crud->title = "Events";
	$crud->table = "{$_SESSION['DB_PREFIX']}petevent";
	$crud->sql = "SELECT A.*, B.name AS petname
				  FROM  {$_SESSION['DB_PREFIX']}petevent A
				  INNER JOIN {$_SESSION['DB_PREFIX']}pet B
				  ON B.id = A.petid
				  WHERE A.petid = $petid
				  AND B.memberid = $memberid
				  ORDER BY A.datetime DESC";
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
				'name'       => 'title',
				'length' 	 => 32,
				'label' 	 => 'Title'
			),
			array(
				'name'       => 'datetime',
				'datatype'	 => 'datetime',	
				'length' 	 => 20,
				'label' 	 => 'Date / Time'
			),
			array(
				'name'       => 'description',
				'type'		 => 'TEXTAREA',
				'length' 	 => 32,
				'showInView' => false,
				'label' 	 => 'Description'
			)
		);
		
	$crud->run();
?>
