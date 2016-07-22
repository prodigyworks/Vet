<?php
	require_once("crud.php");
	
	$animaltypeid = $_GET['id'];
	$crud = new Crud();
	$crud->dialogwidth = 450;
	$crud->title = "Species";
	$crud->table = "{$_SESSION['DB_PREFIX']}species";
	$crud->sql = "SELECT A.*, B.name AS animaltypename
				  FROM  {$_SESSION['DB_PREFIX']}species A
				  INNER JOIN {$_SESSION['DB_PREFIX']}animaltype B
				  ON B.id = A.animaltypeid
				  WHERE A.animaltypeid = $animaltypeid
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
				'name'       => 'animaltypeid',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'editable' 	 => false,
				'default'	 => $animaltypeid,
				'label' 	 => 'Species'
			),
			array(
				'name'       => 'animaltypename',
				'length' 	 => 37,
				'bind'		 => false,
				'editable'	 => false,
				'label' 	 => 'Animal Type'
			),
			array(
				'name'       => 'name',
				'length' 	 => 37,
				'label' 	 => 'Name'
			)
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Breeds',
				'imageurl'	  => 'images/team.png',
				'application' => 'managebreeds.php'
			)
		);
		
	$crud->run();
?>
