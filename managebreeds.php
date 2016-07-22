<?php
	require_once("crud.php");
	
	$speciesid = $_GET['id'];
	$crud = new Crud();
	$crud->dialogwidth = 450;
	$crud->title = "Breeds";
	$crud->table = "{$_SESSION['DB_PREFIX']}breed";
	$crud->sql = "SELECT A.*, B.name AS speciesname, C.name AS animaltypename
				  FROM  {$_SESSION['DB_PREFIX']}breed A
				  INNER JOIN {$_SESSION['DB_PREFIX']}species B
				  ON B.id = A.speciesid
				  INNER JOIN {$_SESSION['DB_PREFIX']}animaltype C
				  ON C.id = B.animaltypeid
				  WHERE A.speciesid = $speciesid
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
				'name'       => 'speciesid',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'editable' 	 => false,
				'default'	 => $speciesid,
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
				'name'       => 'speciesname',
				'length' 	 => 37,
				'bind'		 => false,
				'editable'	 => false,
				'label' 	 => 'Species'
			),
			array(
				'name'       => 'name',
				'length' 	 => 37,
				'label' 	 => 'Name'
			)
		);

	$crud->run();
?>
