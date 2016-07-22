<?php
	require_once("system-db.php");
	require_once("crud.php");
	
	class AnimalCrud extends Crud {
		
		/* Post header event. */
		public function postHeaderEvent() {
			createDocumentLink();
		}
					
		public function editScreenSetup() {
			include("animalform.php");
		}
		
		public function postEditScriptEvent() {
?>
			$("#animaltypeid").val(node.animaltypeid).trigger("change");			
			$("#speciesid").val(node.speciesid).trigger("change");			
			$("#breedid").val(node.breedid).trigger("change");	
			$(".readonly").attr("readonly", true);	
<?php			
		}
		
		public function postScriptEvent() {
?>
			function editDocuments(node) {
				viewDocument(node, "addanimaldocument.php", node, "animaldocs", "animalid");
			}
<?php			
		}
	}
	
	$memberid = getLoggedOnMemberID();
	
	$crud = new AnimalCrud();
	$crud->dialogwidth = 950;
	$crud->title = "Pets";
	$crud->table = "{$_SESSION['DB_PREFIX']}pet";
	$crud->sql = "SELECT A.*, 
				  B.name AS breedname, B.speciesid, 
				  C.name AS speciesname, C.animaltypeid, 
				  D.name AS animaltypename
				  FROM  {$_SESSION['DB_PREFIX']}pet A
				  INNER JOIN {$_SESSION['DB_PREFIX']}breed B
				  ON B.id = A.breedid
				  INNER JOIN {$_SESSION['DB_PREFIX']}species C
				  ON C.id = B.speciesid
				  INNER JOIN {$_SESSION['DB_PREFIX']}animaltype D
				  ON D.id = C.animaltypeid
				  WHERE A.memberid = $memberid
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
				'length' 	 => 30,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'memberid',
				'default'	 => getLoggedOnMemberID(),
				'editable'	 => false,
				'showInView' => false,
				'length' 	 => 30,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'animaltypeid',
				'bind'		 => false,
				'showInView' => false,
				'length' 	 => 20,
				'label' 	 => 'Animal Type'
			),			
			array(
				'name'       => 'animaltypename',
				'bind'		 => false,
				'editable'	 => false,
				'showInView' => true,
				'length' 	 => 20,
				'label' 	 => 'Animal Type'
			),			
			array(
				'name'       => 'speciesname',
				'bind'		 => false,
				'editable'	 => false,
				'showInView' => true,
				'length' 	 => 20,
				'label' 	 => 'Species'
			),			
			array(
				'name'       => 'breedname',
				'bind'		 => false,
				'editable'	 => false,
				'showInView' => true,
				'length' 	 => 20,
				'label' 	 => 'Breed'
			),			
			array(
				'name'       => 'speciesid',
				'bind'		 => false,
				'showInView' => false,
				'length' 	 => 20,
				'label' 	 => 'Species Type'
			),			
			array(
				'name'       => 'breedid',
				'showInView' => false,
				'length' 	 => 20,
				'label' 	 => 'Breed'
			),			
			array(
				'name'       => 'imageid',
				'type'		 => 'IMAGE',
				'required'   => false,
				'length' 	 => 12,
				'label' 	 => 'Profile Image'
			),			
			array(
				'name'       => 'actualweight',
				'length' 	 => 12,
				'datatype'   => 'double',
				'required'   => false,
				'align'		 => 'right',
				'label' 	 => 'Actual Weight'
			),			
			array(
				'name'       => 'targetweight',
				'length' 	 => 12,
				'datatype'   => 'double',
				'required'   => false,
				'align'		 => 'right',
				'label' 	 => 'Target Weight'
			),			
			array(
				'name'       => 'upperweight',
				'length' 	 => 12,
				'datatype'   => 'double',
				'required'   => false,
				'align'		 => 'right',
				'label' 	 => 'Upper Weight'
			),			
			array(
				'name'       => 'lowerweight',
				'length' 	 => 12,
				'datatype'   => 'double',
				'required'   => false,
				'align'		 => 'right',
				'label' 	 => 'Lower Weight'
			),
			array(
				'name'       => 'other',
				'length' 	 => 40,
				'type'		 => 'TEXTAREA',
				'showInView' => false,
				'label' 	 => 'Other'
			)
		);

	$crud->subapplications = array(
			array(
				'title'		  => 'Documents',
				'imageurl'	  => 'images/document.gif',
				'script' 	  => 'editDocuments'
			),
			array(
				'title'		  => 'Events',
				'imageurl'	  => 'images/history.gif',
				'application' => 'manageevents.php'
			),
			array(
				'title'		  => 'Contacts',
				'imageurl'	  => 'images/team.png',
				'application' => 'managecontacts.php'
			),
			array(
				'title'		  => 'Weights',
				'imageurl'	  => 'images/training.png',
				'application' => 'manageweights.php'
			),
			array(
				'title'		  => 'Photos',
				'imageurl'	  => 'images/camera.png',
				'application' => 'managephotos.php'
			)
		);
		
	$crud->run();
?>
