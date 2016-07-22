<?php
	require_once("system-db.php");
	require_once("crud.php");
	
	$petid = $_GET['id'];
	$memberid = getLoggedOnMemberID();
	
	class WeightCrud extends Crud {
		
		public function postHeaderEvent() {
?>
	<link rel="stylesheet" type="text/css" media="all" href="css/jquery.lightbox-0.5.css">
	<script type="text/javascript" src="js/jquery.lightbox-0.5.min.js"></script>			
	<div id="w" class="lightbox hidden"></div>
<?php 
		}
		
		/* Post script event. */
		public function postScriptEvent() {
?>
			function slideShow() {
				$.ajax({
						url: "petphotos.php",
						dataType: 'html',
						async: false,
						data: {
							id: <?php echo $_GET['id']; ?>
						},
						type: "POST",
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown);
						},
						success: function(data) {
							$(".lightbox").html(data);
							
						  	$(".lightbox a").lightBox({
								    overlayBgColor: '#FFF',
								    overlayOpacity: 0.6,
								    imageLoading: 'images/lightbox-ico-loading.gif',
								    imageBtnClose: 'images/lightbox-btn-close.gif',
								    imageBtnPrev: 'images/lightbox-btn-prev.gif',
								    imageBtnNext: 'images/lightbox-btn-next.gif',
								    containerResizeSpeed: 350,
								    txtImage: 'Image',
								    txtOf: 'of'
							   	});
							   	
							$(".lightbox .first").trigger("click");
						}
					});
			}
<?php
		}
	}
	
	$crud = new WeightCrud();
	$crud->dialogwidth = 650;
	$crud->title = "Photos";
	$crud->table = "{$_SESSION['DB_PREFIX']}petimages";
	$crud->sql = "SELECT A.*, B.name AS petname, C.name
				  FROM  {$_SESSION['DB_PREFIX']}petimages A
				  INNER JOIN {$_SESSION['DB_PREFIX']}pet B
				  ON B.id = A.petid
				  INNER JOIN {$_SESSION['DB_PREFIX']}images C
				  ON C.id = A.imageid
				  WHERE A.petid = $petid
				  AND B.memberid = $memberid
				  ORDER BY A.id DESC";
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
				'length' 	 => 37,
				'label' 	 => 'Title'
			),
			array(
				'name'       => 'imageid',
				'type'		 => 'IMAGE',
				'required'   => false,
				'length' 	 => 12,
				'label' 	 => 'Image'
			)
		);

	$crud->applications = array(
			array(
				'title'		  => 'Slideshow',
				'imageurl'	  => 'images/barchart.png',
				'script' 	  => 'slideShow'
			)
		);
		
	$crud->run();
?>
