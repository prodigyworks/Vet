<?php
	require_once("crud.php");
	
	$crud = new Crud();
	$crud->dialogwidth = 450;
	$crud->title = "Animal Types";
	$crud->table = "{$_SESSION['DB_PREFIX']}animaltype";
	$crud->sql = "SELECT A.*
				  FROM  {$_SESSION['DB_PREFIX']}animaltype A
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
				'name'       => 'name',
				'length' 	 => 37,
				'label' 	 => 'Name'
			)
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Species',
				'imageurl'	  => 'images/team.png',
				'application' => 'managespecies.php'
			)
		);
		
	$crud->run();
?>
