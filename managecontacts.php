<?php
	require_once("system-db.php");
	require_once("crud.php");
	
	$petid = $_GET['id'];
	$memberid = getLoggedOnMemberID();
	
	$crud = new Crud();
	$crud->dialogwidth = 450;
	$crud->title = "Contacts";
	$crud->table = "{$_SESSION['DB_PREFIX']}petcontact";
	$crud->sql = "SELECT A.*, B.name AS petname, C.name AS contacttypename
				  FROM  {$_SESSION['DB_PREFIX']}petcontact A
				  INNER JOIN {$_SESSION['DB_PREFIX']}pet B
				  ON B.id = A.petid
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}petcontacttype C
				  ON C.id = A.contacttypeid
				  WHERE A.petid = $petid
				  AND B.memberid = $memberid
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
				'name'       => 'contacttypeid',
				'type'       => 'DATACOMBO',
				'length' 	 => 18,
				'label' 	 => 'Contact Type',
				'table'		 => 'petcontacttype',
				'required'	 => true,
				'table_id'	 => 'id',
				'alias'		 => 'contacttypename',
				'table_name' => 'name'
			),
			array(
				'name'       => 'name',
				'length' 	 => 32,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'telephone',
				'length' 	 => 32,
				'label' 	 => 'Telephone'
			)
		);
		
	$crud->run();
?>
