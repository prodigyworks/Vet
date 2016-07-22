<?php
	require_once("crud.php");
	
	$crud = new Crud();
	$crud->dialogwidth = 450;
	$crud->title = "Contact Types";
	$crud->table = "{$_SESSION['DB_PREFIX']}petcontacttype";
	$crud->sql = "SELECT A.*
				  FROM  {$_SESSION['DB_PREFIX']}petcontacttype A
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
	$crud->run();
?>
