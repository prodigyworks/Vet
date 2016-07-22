<?php
require_once('php-sql-parser.php');
require_once('php-sql-creator.php');
require_once("sqlfunctions.php");

class Crud  {
	private $pkName = "id";
	private $pkViewName = "uniqueid";
	private $orderColumn = "";
	private $fromrow = 0;
	private $torow = 18;
	private $pagesize = 18;
	private $rowcount = 0;
	private $pages = 1;
	private $sortby = "";
	private $sortdirection = "ASC";
	private $geolocation = false;
	
	public $onDblClick = null;
	public $validateForm = null;
	public $onClickCallback = "";
	public $postDataRefreshEvent = null;
	public $preAddScriptEvent = null;
	public $preDeleteScriptEvent = null;
	public $postDeleteScriptEvent = null;
	public $autoPopulate = true;
	public $preEditScript = null;
	public $allowAdd = true;
	public $allowEdit = true;
	public $allowFilter = true;
	public $allowRemove = true;
	public $allowView = true;
	public $table = "";
	public $title = "";
	public $sql = "";
	public $dialogwidth = 500;
	public $subapplications = array();
	public $applications = array();
	public $messages = array();
	public $checkconstraints = array();
	public $defaultappcolumn = array(
				'id'		  => '',
				'title'		  => '',
				'imageurl'	  => '',
				'application' => '',
				'script' 	  => '',
				'tooltip'	  => '',
				'action' 	  => '',
				'rule'		  => ''
			);
			
	public $defaultsubappcolumn = array(
				'id'		  => '',
				'title'		  => '',
				'imageurl'	  => '',
				'application' => '',
				'hide' 	  	  => false,
				'tooltip'	  => '',
				'script' 	  => '',
				'action' 	  => '',
				'rule'		  => ''
			);
			
	public $defaultcolumn = array(
				'name'       		=> 'id',
				'viewname'          => '',
				'type'       		=> 'TEXTBOX',
				'bind' 		 		=> true,
				'default' 	 		=> '',
				'editable' 	 		=> true,
				'validate' 			=> '',
				'length' 	 		=> 20,
				'hidden'	 		=> false,
				'viewlength' 	 	=> 20,
				'sortable'	 		=> true,
				'alias' 	 		=> '',
				'align' 	 		=> 'left',
				'where'				=> " ",
				'filterprefix'		=> null,
				'locked'		    => false,
				'datatype'   		=> 'string',
				'filter'			=> true,
				'required'   		=> true,
				'role'				=> null,
				'pk'   		 		=> false,
				'parentid'	 		=> false,
				'sortby'	 		=> false,
				'unique'   	 		=> false,
				'associated'		=> false,
				'onchange'			=> null,
				'sortcolumn'		=> null,
				'filtercolumn'		=> null,
				'formatter'			=> '',
				'associatedcolumns' => array(
					array()
				),
				'options' 	 		=> array(
					array()
				),
				'showInView' 		=> true,
				'readonly'   		=> false,
				'label' 	 		=> 'ID',
				'suffix' 	 		=> ''
		);
	public $columns = array();
	private $errorDescriptions = array();
	
	function __construct() {
		require_once('system-db.php');
		
		start_db();
		initialise_db();
		
		if (isset($_GET['from'])) {
			$this->fromrow = $_GET['from'];
		}
		
		if (isset($_GET['to'])) {
			$this->torow = $_GET['to'];
		}
		
		if (isset($_GET['direction'])) {
			$this->sortdirection = $_GET['direction'];
		}
		
		if (isset($_GET['sort'])) {
			$this->sortby = $_GET['sort'];
		}
		
		$this->pagesize = ($this->torow - $this->fromrow);
	}
	
	public function setSQL($str) {
		$this->sql = $str;
	}
	
	public function preScriptEvent() {
		
	}
	
	public function postEditScriptEvent() {
		/* Event for override. */
	}
	
	public function postViewScriptEvent() {
		/* Event for override. */
	}
	
	public function postAddScriptEvent() {
		/* Event for override. */
	}
		
	public function preCommandEvent() {
		/* Event for pre-command. */
	}
	
	public function postLoadScriptEvent() {
		
	}
	
	public function postScriptEvent() {
		/* Event for override. */
	}
	
	public function preEditScreenMarkup() {
		
	}
	
	public function postUpdateScriptEvent() {
		
	}
	
	public function afterInsertRow() {
	}
	
	public function postDeleteEvent($id) {
		
	}
	
	public function postHeaderEvent() {
		/* Event for header. */
	}
	
	public function postToolbarEvent() {
		/* Event. */
	}

	public function postUpdateEvent($id) {
		/* Event. */
	}

	public function preUpdateEvent($id) {
		/* Event. */
	}
	
	public function postInsertEvent() {
		/* Event. */
	}
	
	public function triggerRefresh() {
		if (count($this->errorDescriptions) > 0) {
			echo "<html><body><script>window.parent.showError('" . $this->errorDescriptions[0] . "');</script>";
			
		} else {
			echo "<html><body><script>window.parent.refreshData();</script>";
		}
	}
	
	public function run() {
		for ($i = 0; $i < count($this->subapplications); $i++) {
			$this->subapplications[$i] = array_merge( $this->defaultsubappcolumn, $this->subapplications[$i]);
		}
		
		for ($i = 0; $i < count($this->applications); $i++) {
			$this->applications[$i] = array_merge( $this->defaultappcolumn, $this->applications[$i]);
		}
		
		for ($i = 0; $i < count($this->columns); $i++) {
			$this->columns[$i] = array_merge( $this->defaultcolumn, $this->columns[$i]);
			
			if ($this->columns[$i]['type'] == "GEOLOCATION") {
				$this->geolocation = true;
			}
			
			if ($this->columns[$i]['viewname'] == "") {
				$this->columns[$i]['viewname'] = $this->columns[$i]['name'];
			}
		
			if ($this->columns[$i]['viewname'] == "id") {
				$this->columns[$i]['viewname'] = "uniqueid";
			}
			
			if ($this->columns[$i]['pk'] == true) {
				$this->pkName = $this->columns[$i]['name'];
				$this->pkViewName = $this->columns[$i]['viewname'];
			}
			
			if ($this->columns[$i]['sortby'] == true) {
				$this->orderColumn = $this->columns[$i]['name'];
			}
			
			if ($this->columns[$i]['role'] != null) {
				$allowed = false;

				foreach ($this->columns[$i]['role'] as $roleid) {
					if (isUserInRole($roleid)) {
						$allowed = true;
						break;
					}
				}
				
				if (! $allowed) {
					$this->columns[$i]['showInView'] = false;
					$this->columns[$i]['editable'] = false;
					$this->columns[$i]['filter'] = false;
				}
			}
		}
		
		if ($this->geolocation) {
			foreach ($this->columns as $col) {
				if ($col['type'] == 'GEOLOCATION') {
					$this->columns[count($this->columns)] = array_merge( 
							$this->defaultcolumn,
							array(
								'name'       => $col['name'] . '_lat',
								'datatype'	 => 'float',
								'length' 	 => 10,
								'required'   => false,
								'showInView' => false,
								'hidden'	 => true,
								'label' 	 => 'Latitude'
							)
						);
						
					$this->columns[count($this->columns)] = array_merge( 
							$this->defaultcolumn,
							array(
								'name'       => $col['name'] . '_lng',
								'datatype'	 => 'float',
								'length' 	 => 10,
								'showInView' => false,
								'required'   => false,
								'hidden'	 => true,
								'label' 	 => 'Longtitude'
							)
						);
				}
			}
		}		
		for ($i = 0; $i < count($this->columns); $i++) {
			foreach ($this->columns[$i]['associatedcolumns'] as $associated) {
				for ($j = 0; $j < count($this->columns); $j++) {
					if ($associated == $this->columns[$j]['name']) {
						$this->columns[$j]['associated'] = true;
					}
				}
			}
		}
		
		if ($this->orderColumn == "") {
			$this->orderColumn = $this->columns[0]['name'];
		}
		
		$this->preCommandEvent();
		
		if (isset($_POST['crudcmd'])) {
			if ($_POST['crudcmd'] == "update") {
				$this->update($_POST['crudid']);
				$this->triggerRefresh();
				
			} else if ($_POST['crudcmd'] == "insert") {
				$this->insert();
				$this->triggerRefresh();
				
			} else if ($_POST['crudcmd'] == "filtersave") {
				$this->filterSave();
				$this->view();
				
			} else if ($_POST['crudcmd'] == "filter") {
				$this->fromrow = 0;
				$this->torow = $this->pagesize;
				$this->autoPopulate = true;
				$this->view();
				
			} else {
				$_POST['crudcmd']($this);
				
				if ($_POST['triggerrefresh'] != "") {
					$this->triggerRefresh();
				}
			}
			
			mysql_query("COMMIT");
				
		} else {
			$this->view();
		}
	}
	
	public function view() {
		$this->filter();
		
		require_once("system-header.php");
		require_once("confirmdialog.php");
		require_once("tinymce.php");
		
		?>
		<script src="js/i18n/grid.locale-en.js" type="text/javascript"></script>
		<script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>
		<script src='js/jquery.ui.timepicker.js'></script>
		<script src="js/jquery.multiselect.filter.min.js" type="text/javascript"></script>
		<script src="js/jquery.multiselect.min.js" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="css/jquery.multiselect.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery.multiselect.filter.css" />
		
		<?php
		if ($this->geolocation) {
			?>
			<script type='text/javascript' src='jsc/jquery.autocomplete.js'></script>
			<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
			<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places" type="text/javascript"></script>
			<script src="http://www.google.com/uds/api?file=uds.js&v=1.0" type="text/javascript"><;/script>
			<script src="http://maps.google.com/maps/api/js?v=3.1&sensor=false&region=PH"></script>
			<script type="text/javascript">
				var directionsService = new google.maps.DirectionsService();
				
				function getLatLng(name, address)  {
				    var geocoder = new google.maps.Geocoder();

				    geocoder.geocode(
				    		{ 
				    			'address' : address 
				    		}, 
				    		function( results, status ) {
						        if (status == google.maps.GeocoderStatus.OK ) {
							        $("#" + name + "_lat").val(results[0].geometry.location.lat());
									$("#" + name + "_lng").val(results[0].geometry.location.lng());
									
						        } else {
//						            pwAlert( "Geocode was not successful for the following reason: " + status );
						        }
						    }
						);            
				}
				
				$(document).ready(function() {
					    var pacContainerInitialized = false; 
					<?php
						foreach ($this->columns as $col) {
							if ($col['type'] == "GEOLOCATION") {
					?>
                        $('#<?php echo $col['name']; ?>').keypress(function() { 
                                if (!pacContainerInitialized) { 
                                        $('.pac-container').css('z-index', '9999'); 
                                        pacContainerInitialized = true; 
                                } 
                        }); 

					
						$("#<?php echo $col['name']; ?>").change(function() {
								setTimeout(
										function() { 
											getLatLng("<?php echo $col['name']; ?>", $("#<?php echo $col['name']; ?>").val());
										},
										500
									);
										
								
							});
					<?php
								
							}
						}
					?>
					});
				
				function initialize() {
			        var options = {
			        		types: ['(cities)'],
			        		componentRestrictions: {country: ["uk"]}       
			        	};
			
					<?php
						foreach ($this->columns as $col) {
							if ($col['type'] == "GEOLOCATION") {
					?>
						    var input = document.getElementById('<?php echo $col['name']; ?>');
						    var autocomplete = new google.maps.places.Autocomplete(input, options);
					<?php
							}
						}
					?>
				}
				
/*				function search() {
					setTimeout( 
							function() { 
								$('#jobform').submit(); 
							}, 
							500
						);
				}
					*/
				
				google.maps.event.addDomListener(window, 'load', initialize);
			</script>
			<?php
		}
		
		?>
		<link href="css/ui.jqgrid.css" rel="stylesheet" type="text/css" />
		<?php
		
		createConfirmDialog("confirmdialog", "Remove item ?", "crudDelete");
		
		/* Event post header. */
		$this->crud_cache_function("postHeaderEvent", array("pageid" => $_SESSION['pageid']));
		$this->crud_cache_function("createFilterScreen", array("pageid" => $_SESSION['pageid']));
		$this->crud_cache_function("createEditScreen", array("pageid" => $_SESSION['pageid']));
		$this->createView();
	}
	
	public function createEditScreen() {
		$this->preEditScreenMarkup ();
?>
<div class="modal" id="editdialog">
	<div id="editpanel" class="entryform">
		<form id="editform" method="POST" enctype="multipart/form-data">
			<input type="hidden" id="crudid" name="crudid" value="" /> <input
				type="hidden" id="triggerrefresh" name="triggerrefresh" value="" />
			<input type="hidden" id="crudcmd" name="crudcmd" value="" /> <input
				type="hidden" id="fromrow" name="fromrow" value="" />
<?php
		foreach ( $this->messages as $message ) {
			if (! isset ( $message ['array'] )) {
				echo "<input type=\"hidden\" id=\"" . $message ['id'] . "\" name=\"" . $message ['id'] . "\" value=\"\" />\n";
			}
		}
		
		$this->editScreenSetup ();
?>
		</form>
	</div>
</div>
<?php
	}
	
	public function editScreenSetup() {
?>
<table width='100%' cellpadding=0 cellspacing=4 class="entryformclass">
<?php
		foreach ( $this->columns as $col ) {
			if ($col ['editable'] && $col ['associated'] == false) {
				if ($col ['hidden']) {
					echo "<tr valign=center style='display:none'>\n";
	
				} else {
					echo "<tr valign=center>\n";
				}
				
				echo "<td valign=center nowrap>" . $col ['label'] . "</td>\n";
				echo "<td align='left' nowrap>";
				
				$this->showEditBox ( $col );
				
				foreach ( $col ['associatedcolumns'] as $associated ) {
					foreach ( $this->columns as $subcol ) {
						if ($associated == $subcol ['name']) {
							$this->showEditBox ( $subcol );
							
							echo $subcol ['label'];
						}
					}
				}
				
				echo "</td>";
				echo "</tr>\n";
			}
		}
?>
</table>
<?php
	}
		
	private function showEditBox($col) {
		if ($col['type'] == "TEXTBOX") {
			if ($col['datatype'] == "timestamp" || $col['datatype'] == "datetime") {
				echo "<input class='" .($col['readonly'] != true ? "datepicker" : "") . "' " . ($col['required'] == true ? "required='true' " : "") . " " . ($col['readonly'] == true ? "readonly " : "") . " type='text' id='" . $col['name'] . "' name='" . $col['name'] . "' />\n";
				echo "<input class='" .($col['readonly'] != true ? "timepicker" : "") . "' " . ($col['required'] == true ? "required='true' " : "") . " " . ($col['readonly'] == true ? "readonly " : "") . " type='text' id='" . $col['name'] . "_time' name='" . $col['name'] . "_time' />\n";
				
			} else if ($col['datatype'] == "date") {
				echo "<input class='" .($col['readonly'] != true ? "datepicker" : "") . "' " . ($col['required'] == true ? "required='true' " : "") . " " . ($col['readonly'] == true ? "readonly " : "") . " type='text' id='" . $col['name'] . "' name='" . $col['name'] . "' />\n";
								
			} else if ($col['datatype'] == "time") {
				echo "<input class='" .($col['readonly'] != true ? "timepicker" : "") . "' " . ($col['required'] == true ? "required='true' " : "") . " " . ($col['readonly'] == true ? "readonly " : "") . " type='text' id='" . $col['name'] . "' name='" . $col['name'] . "' />\n";
				
			} else if ($col['datatype'] == "contact") {
				createContactCombo($col['name'], "", $col['required']);
				
			} else if ($col['datatype'] == "typist") {
				createTypistCombo($col['name'], "", $col['required']);
				
			} else if ($col['datatype'] == "user") {
				createUserCombo($col['name'], "", $col['required']);
				
			} else {
				echo "<input " . ($col['required'] == true ? "required='true' " : "") . " " . ($col['readonly'] == true ? "readonly " : "") . " type='text' style='width:" . ($col['length'] * 6) . "px' id='" . $col['name'] . "' name='" . $col['name'] . "' />\n";
			}
			
		} else if ($col['type'] == "GEOLOCATION") {
			echo "<input " . ($col['required'] == true ? "required='true' " : "") . " " . ($col['readonly'] == true ? "readonly " : "") . " type='text' style='width:" . ($col['length'] * 6) . "px' id='" . $col['name'] . "' name='" . $col['name'] . "' />\n";
			
		} else if ($col['type'] == "CHECKBOX") {
			echo "<input " . ($col['required'] == true ? "required='true' " : "") . " " . ($col['readonly'] == true ? "readonly " : "") . " type='checkbox' id='" . $col['name'] . "' name='" . $col['name'] . "' />\n";
			
		} else if ($col['type'] == "DERIVED") {
			echo "<input readonly type='text' style='width:" . ($col['length'] * 6) . "px' id='" . $col['name'] . "' name='" . $col['name'] . "' />\n";
			
		} else if ($col['type'] == "PASSWORD") {
			echo "<input " . ($col['required'] == true ? "required='true' " : "") . " " . ($col['readonly'] == true ? "readonly " : "") . " type='password' style='width:" . ($col['length'] * 6) . "px' id='" . $col['name'] . "' name='" . $col['name'] . "' />\n";
			
		} else if ($col['type'] == "FILE") {
			echo "<input " . ($col['required'] == true ? "required='true' " : "") . " " . ($col['readonly'] == true ? "readonly " : "") . " type='file' style='width:400px' id='" . $col['name'] . "' name='" . $col['name'] . "' />\n";
			
		} else if ($col['type'] == "IMAGE") {
			echo "<img style='height:" . ($col['length']) . "px' id='" . $col['name'] . "_img' />\n<br>";
			echo "<input " . ($col['required'] == true ? "required='true' " : "") . " " . ($col['readonly'] == true ? "readonly " : "") . " type='file' style='width:400px' id='" . $col['name'] . "' name='" . $col['name'] . "' />\n";

		} else if ($col['type'] == "BASICTEXTAREA") {
			echo "<textarea style='width:600px' rows=6 cols=80 " .($col['readonly'] == true ? "readonly " : "") . " id='" . $col['name'] . "' name='" . $col['name'] . "'></textarea>\n";

		} else if ($col['type'] == "TEXTAREA") {
			echo "<textarea class='tinyMCE' id='" . $col['name'] . "' name='" . $col['name'] . "'></textarea>\n";
			
		} else if ($col['type'] == "DATACOMBO") {
			createCombo($col['name'], $col['table_id'], $col['table_name'], $_SESSION['DB_PREFIX'] . $col['table'], $col['where'], ($col['required'] == true ? true : false));
			
		} else if ($col['type'] == "LAZYDATACOMBO") {
			createLazyCombo($col['name'], $col['table_id'], $col['table_name'], $_SESSION['DB_PREFIX'] . $col['table'], $col['where'], ($col['required'] == true ? true : false), $col['length']);
			
		} else if ($col['type'] == "MULTIDATACOMBO") {
			createCombo($col['name'], $col['table_id'], $col['table_name'], $_SESSION['DB_PREFIX'] . $col['table'], $col['where'], ($col['required'] == true), true, array("class" => "multiselect", "size" => "1", "multiple" => "true"), false);
			
		} else if ($col['type'] == "COMBO") {
			echo "<SELECT id='" . $col['name'] . "' name='" . $col['name'] . "'>\n";
			echo "<OPTION value=''></OPTION>\n";
			
			foreach ($col['options'] as $opt) {
				echo "<OPTION value='" . $opt['value'] . "'>" . $opt['text'] . "</OPTION>\n";
			}
			
			echo "</SELECT>";
		}
		
		if ($col['onchange'] != null) {
		?>
			<SCRIPT>
				$(document).ready(
						function() {
							$("#<?php echo $col['name']; ?>").change(<?php echo $col['onchange']; ?>);
						}
					);
			</SCRIPT>
		
		<?php
		}
		
		echo "&nbsp;" . $col['suffix'];
	}
	
	public function createFilterScreen() {
	?>
	<iframe style="display:none" id="submitframe" name="submitframe">
	</iframe>
	<div class="modal" id="filtersavedialog">
		<label>Filter name</label>
		<input type="text" id="filtername" name="filtername" size=60 />
	</div>
	
	<div class="modal" id="filterdialog">
		<div id="filterpanel">
		  <?php
		  	$filterformaction = $_SERVER['PHP_SELF'] . "?filtering=true";
		  	
		  	foreach ( $_GET as $key => $value ) {
		  		if (strpos($key, "filtering") != 0 && strpos($key, "filter_") != 0	) {
			  		$filterformaction .= "&" . $key . "=" . $value;
		  		}
			}
		  ?>
		  <form id="filterform" method="POST" enctype="multipart/form-data" action="<?php echo $filterformaction; ?>" >
			<input type="hidden" id="triggerrefresh" name="triggerrefresh" value="" />
			<input type="hidden" id="crudcmd" name="crudcmd" value="" />
			<input type="hidden" id="savefiltername" name="savefiltername" value="" />
			<table width='100%' cellpadding=0 cellspacing=4>
	<?php
		foreach ($this->columns as $col) {
			if ($col['filter']) {
				if ($col['type'] == "DERIVED" ||
					$col['type'] == "PASSWORD" ||
					$col['type'] == "IMAGE" ||
					$col['type'] == "BASICTEXTAREA" ||
					$col['type'] == "TEXTAREA" ||
					$col['hidden'] == true) {
					continue;
				}
				
				echo "<tr valign=center>\n";
				echo "<td valign=center nowrap>" . $col['label'] . "</td>\n";
				echo "<td nowrap>";
				$filterValue = "";
				
				if (isset($_GET['filter_' . $col['name']])) {
					$filterValue = base64_decode($_GET['filter_' . $col['name']]);
				}
				
				if (isset($_POST['filter_' . $col['name']])) {
					$filterValue = $_POST['filter_' . $col['name']];
				}
			
				if ($col['type'] == "TEXTBOX") {
					if ($col['datatype'] == "timestamp" || $col['datatype'] == "datetime") {
						echo "<input class='datepicker' type='text' id='filter_" . $col['name'] . "' name='filter_" . $col['name'] . "' value='$filterValue' />\n";
						
					} else if ($col['datatype'] == "date") {
						echo "<input class='datepicker' type='text' id='filter_" . $col['name'] . "' name='filter_" . $col['name'] . "' value='$filterValue' />\n";
						
					} else if ($col['datatype'] == "time") {
						echo "<input class='timepicker' type='text' id='filter_" . $col['name'] . "' name='filter_" . $col['name'] . "' value='$filterValue' />\n";
						
					} else if ($col['datatype'] == "contact") {
						createContactCombo("filter_" . $col['name']);
						
					} else if ($col['datatype'] == "typist") {
						createTypistCombo("filter_" . $col['name']);
						
					} else if ($col['datatype'] == "user") {
						createUserCombo("filter_" . $col['name']);
												
					} else {
						echo "<input  type='text' style='width:" . ($col['length'] * 6) . "px' id='filter_" . $col['name'] . "' name='filter_" . $col['name'] . "'  value='$filterValue' />\n";
					}
					
				} else if ($col['type'] == "GEOLOCATION") {
					echo "<input  type='text' style='width:" . ($col['length'] * 6) . "px' id='filter_" . $col['name'] . "' name='filter_" . $col['name'] . "'  value='$filterValue' />\n";
					
				} else if ($col['type'] == "CHECKBOX") {
					echo "<input  type='checkbox' id='filter_" . $col['name'] . "' name='filter_" . $col['name'] . "'  value='$filterValue' />\n";
					
				} else if ($col['type'] == "BASICTEXTAREA") {
					echo "<div  style='width:600px; height: 150px; overflow:auto' id='filter_" . $col['name'] . "' name='filter_" . $col['name'] . "'>$filterValue</div>\n";
					
				} else if ($col['type'] == "TEXTAREA") {
					echo "<div  style='width:400px; height: 150px; overflow:auto' id='filter_" . $col['name'] . "' name='filter_" . $col['name'] . "'>$filterValue</div>\n";
					
				} else if ($col['type'] == "DATACOMBO") {
					createCombo("filter_" . $col['name'], $col['table_id'], $col['table_name'], $_SESSION['DB_PREFIX'] . $col['table'], $col['where']);
					
				} else if ($col['type'] == "LAZYDATACOMBO") {
					createLazyCombo("filter_" . $col['name'], $col['table_id'], $col['table_name'], $_SESSION['DB_PREFIX'] . $col['table'], $col['where'], false, $col['length']);
					
				} else if ($col['type'] == "MULTIDATACOMBO") {
					createCombo("filter_" . $col['name'], $col['table_id'], $col['table_name'], $_SESSION['DB_PREFIX'] . $col['table']);
					
				} else if ($col['type'] == "COMBO") {
					echo "<SELECT id='filter_" . $col['name'] . "' name='filter_" . $col['name'] . "'>\n";
					echo "<OPTION value=''></OPTION>\n";
					
					foreach ($col['options'] as $opt) {
						echo "<OPTION value='" . $opt['value'] . "'>" . $opt['text'] . "</OPTION>\n";
					}
					
					echo "</SELECT>";
				}
			
				echo "&nbsp;" . $col['suffix'];
				
				echo "</td>";
				echo "</tr>\n";
			}
		}
	?>
			</table>
		  </form>
		</div>
	</div>
	<?php
	}
	
	public function filterSave() {
		$id = 0;
		$memberid = getLoggedOnMemberID();
		$pageid = $_SESSION['pageid'];
		$description = $_POST['savefiltername'];
		
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}filter " .
				"(memberid, pageid, description, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) " .
				"VALUES " .
				"($memberid, $pageid, '$description', NOW(), $memberid, NOW(), $memberid) ";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " - " . mysql_error());
		}
		
		$id = mysql_insert_id();
		
		foreach ($this->columns as $col) {
			if ($col['filter'] && isset($_POST['filter_' . $col['name']]) && $_POST['filter_' . $col['name']] != "") {
				
				$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}filterdata " .
						"(filterid, columnname, value, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) " .
						"VALUES " .
						"($id, '" . $col['name'] . "', '" . $_POST['filter_' . $col['name']] . "', NOW(), $memberid, NOW(), $memberid) ";
				$result = mysql_query($qry);
				
				if (! $result) {
					logError($qry . " - " . mysql_error());
				}
			}
		}
	}
	
	public function filter() {
		$parser = new PHPSQLParser($this->sql);
		$prefix = $this->table . ".";
		
//		print_r($parser->parsed);

		if ($parser->parsed['FROM'][0]['alias'] != "") {
			$prefix = $parser->parsed['FROM'][0]['alias']['name'] . ".";
		}
		
		foreach ($this->columns as $col) {
			$filterValue = "";
			
			if (isset($_GET['filter_' . $col['name']])) {
				$filterValue = base64_decode($_GET['filter_' . $col['name']]);
			}
			
			if (isset($_POST['filter_' . $col['name']])) {
				$filterValue = $_POST['filter_' . $col['name']];
			}
			
			if ($col['filter'] && $filterValue != "") {
				
				if ($col['type'] == "MULTIDATACOMBO" || 
					$col['type'] == "DATACOMBO" || 
					$col['type'] == "LAZYDATACOMBO" || 
					$col['datatype'] == "user" || 
					$col['datatype'] == "typist" || 
					$col['datatype'] == "contact") {
					if ($filterValue == "0") {
						continue;
					}
				}
				
				if ($col['filterprefix'] != null) {
					$prefix = $col['filterprefix'] . ".";
				}
				
				$filtercolumn = $prefix . $col['name'];
				
				if ($col['filtercolumn'] != null) {
					$filtercolumn = $prefix . $col['filtercolumn'];
				}
				
				if (! isset($parser->parsed['WHERE'])) {
					/* Create where clause. */
					$parser->parsed['WHERE'] = array();
								
				} else {
					/* Add to the where clause. */
					$parser->parsed['WHERE'][] = 
							array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "AND",
									"sub_tree"			=> ""
								);
				}
							
				$parser->parsed['WHERE'][] = 
						array(
								"expr_type" 		=> "colref",
								"base_expr"			=> $filtercolumn,
								"sub_tree"			=> ""
							);
							
				if ($col['datatype'] == "string") {
					$parser->parsed['WHERE'][] = 
							array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "LIKE",
									"sub_tree"			=> ""
								);
					
				} else {
					$parser->parsed['WHERE'][] = 
							array(
									"expr_type" 		=> "operator",
									"base_expr"			=> "=",
									"sub_tree"			=> ""
								);
				}
							
				$parser->parsed['WHERE'][] = 
						array(
								"expr_type" 		=> "const",
								"base_expr"			=> "'$filterValue'",
								"sub_tree"			=> ""
							);
					
			}
		}
		
		try {
			$creator = new PHPSQLCreator($parser->parsed);
			$created = $creator->created;			
			
		} catch (Exception $e) {
			logError($e->getMessage());
		}
		
		$this->sql = $created;
		
//		logError($this->sql);
	}
	
	public function showHTMLAssets() {
	?>
	<div style='height:12px'>
		<?php
		foreach ($this->applications as $app) {
			$okToRun = true;
			
//			if ($app['rule'] != "") {
//				$okToRun = ($app['rule']($member));
//			}
			
			if ($okToRun && $app['action'] != "") {
				$okToRun = isUserAccessPermitted($app['action'], $app['title']);
			}
			
			if ($okToRun) {
				if ($app['application'] != "") {
				?>
				   	<span title="<?php echo $app['tooltip']; ?>" id="<?php echo $app['id']; ?>"  class="wrapper"><a class='rgap2 link1' href="javascript:application('<?php echo $app['application']; ?>')"><em><b><img src='<?php echo $app['imageurl']; ?>' /> <?php echo $app['title']; ?></b></em></a></span>
				<?php
					
				} else {
				?>
				   	<span  title="<?php echo $app['tooltip']; ?>" id="<?php echo $app['id']; ?>"  class="wrapper"><a class='rgap2 link1' href="javascript:<?php echo $app['script']; ?>()"><em><b><img src='<?php echo $app['imageurl']; ?>' /> <?php echo $app['title']; ?></b></em></a></span>
				<?php
				}
			}
		}
		?>
		
		<?php
			if ($this->allowFilter) {
				if (isUserAccessPermitted('Filter')) {
		?> 
	   	<span id="filterbutton"  class="wrapper">
	   		<?php
	   			$memberid = getLoggedOnMemberID();
	   			$pageid = $_SESSION['pageid'];
	   			$qry = "SELECT id, description " .
	   					"FROM {$_SESSION['DB_PREFIX']}filter " .
	   					"WHERE memberid = $memberid " .
	   					"AND pageid = $pageid";
				$result = mysql_query($qry);
				$first = true;
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($first) {
							$first = false;
						?>
					   	<ul class="submenu">
						<?php	
						}
						echo "<li class='menuitem' onclick='selectFilter(" . $member['id'] . ")'>" . $member['description'] . "</li>";
					}
					
					if (! $first) {
					?>
				   	</ul>
					<?php	
					}
					
				} else {
					logError($qry . " - " . mysql_error());
				}
	   		?>
	   	<a class='rgap2 link1' href="javascript:filter()"><em><b><img src='images/filter.png' /> Filter</b></em></a>
	   	</span>
		<?php
				}
			}
		?>
		
		<?php
			if ($this->allowAdd) {
				if (isUserAccessPermitted('AddItem')) {
		?> 
	   	<span class="wrapper"><a class='rgap2 link1' href="javascript:addCrudItem()"><em><b><img src='images/add.png' /> Add</b></em></a></span>
		<?php
				}
			}
		?>
		
		<?php
			if ($this->allowView) {
				if (isUserAccessPermitted('ViewItem')) {
		?> 
	   	<span class="wrapper"><a disabled class='subapp rgap2 link1' href="javascript:viewSelectedRow()"><em><b><img src='images/view.png' /> View</b></em></a></span>
		<?php
				}
			}
		?>
		
		<?php
			if ($this->allowEdit) {
				if (isUserAccessPermitted('EditItem')) {
		?> 
	   	<span class="wrapper"><a disabled class='subapp rgap2 link1' href="javascript:editSelectedRow()"><em><b><img src='images/edit.png' /> Edit</b></em></a></span>
		<?php
				}
			}
		?>
		
		<?php
			if ($this->allowRemove) {
				if (isUserAccessPermitted('RemoveItem')) {
		?> 
	   	<span class="wrapper"><a disabled class='subapp rgap2 link1' href="javascript:removeSelectedRow()"><em><b><img src='images/delete.png' /> Remove</b></em></a></span>
		<?php
				}
			}
		?>
		
		<?php
	foreach ( $this->subapplications as $app ) {
			$okToRun = true;
			
			if ($app ['action'] != "") {
				$okToRun = isUserAccessPermitted ( $app ['action'], $app ['title'] );

			} else if ($app ['script'] != "") {
				$okToRun = isUserAccessPermitted ( $app ['script'], $app ['title'] );
			}
			
			if ($okToRun) {
				if ($app ['submenu'] != null) {
?>
   	<span title="<?php echo $app['tooltip']; ?>" class="submenuwrapper wrapper">
		<ul class="submenu">
<?php
					foreach ( $app ['submenu'] as $submenu ) {
						echo "<li id='" . $submenu ['id'] . "' class='menuitem' onclick='if (this.disabled != true) " . $submenu ['script'] . "(getPK())'>" . $submenu ['title'] . "</li>";
					}
?>
		</ul> 
		<a disabled class='subapp rgap2 link1' href="javascript:void()"> 
			<em> 
				<b> 
					<img width=16 height=16 src='<?php echo $app['imageurl']; ?>' /> <?php echo $app['title']; ?>
	   			</b>
			</em>
		</a>
	</span>
<?php
				} else {
					if ($app ['application'] != "") {
?>
   	<span title="<?php echo $app['tooltip']; ?>" class="wrapper"> 
   		<a disabled class='subapp rgap2 link1' id="<?php echo $app['id']; ?>" href="javascript: subApp('<?php echo $app['application']; ?>', getPK())">
			<em> 
				<b> 
					<img width=16 height=16 src='<?php echo $app['imageurl']; ?>' /> <?php echo $app['title']; ?>
   				</b>
			</em>
		</a>
	</span>
<?php
					} else {
?>
   	<span title="<?php echo $app['tooltip']; ?>" class="wrapper"> 
   		<a disabled class='subapp rgap2 link1' id="<?php echo $app['id']; ?>" href="javascript: <?php echo $app['script']; ?>(getPK())"> 
   			<em> 
   				<b> 
   					<img width=16 height=16 src='<?php echo $app['imageurl']; ?>' /> <?php echo $app['title']; ?>
   				</b>
			</em>
		</a>
	</span>
<?php
					}
				}
			}
		}
		?>
		
		
		<?php
			if (isset($_GET['puri'])) {
		?>
		   	<span class="rgap5 wrapper"><a class='rgap2 link1' href="javascript:back()"><em><b><img src='images/back2.png' /> Back</b></em></a></span>
		
		<?php
			} else {
				echo "<br>";
			}
		?>
	</div>
	<br>
	
	<table id="tempgrid">
	</table>
	
	<div id="tempgrid_pager"></div>
	
	<?php
		$link = "";
		$linkfields = "";
		$firstlink = true;
		$where = "";
		
		if ($this->sql == "") {
			logError("No SQL provided");
		}
	?>
	<script>
		<?php
			$this->preScriptEvent();
		?>
		var currentCrudID = null;
		var sortByColumn = "<?php echo $this->sortby; ?>";
		var sortByDirection = "<?php echo $this->sortdirection; ?>";
		var fromRow = 0;
		var toRow = "<?php echo $this->torow; ?>";
		var pages = "<?php echo $this->pages; ?>";
		var pageSize = <?php echo $this->pagesize; ?>;
		
		function verifyUniqueKey(keyName, keyValue) {
			var retValue = true;

			callAjax(
					"finddatarow.php", 
					{ 
						id: keyValue,
						pkname: keyName,
						table: "<?php echo $this->table; ?>"
					},
					function(data) {
						if (data.length > 0) {
							pwAlert("Row aleady exists");
							retValue = false;
						}
					},
					false
				);
				
			return retValue;
		}
		
		function verifyCrudForm() {
			<?php
			if ($this->validateForm != null) {
				echo "if (" . $this->validateForm . "() == false) return false;\n";
			}
			?>
			return verifyStandardForm("#editform");
		}
		
		function subApp(app) {
			$filterurl = "";

<?php
			foreach ($this->columns as $col) {
				if ($col['filter'] && $_POST['filter_' . $col['name']] != "") {
					if ($col['type'] == "DATACOMBO" || $col['type'] == "MULTIDATACOMBO") {
						if ($_POST['filter_' . $col['name']] == "0") {
							continue;
						}
					}
					
					$filterurl .= "&filter_" . $col['name'] . "=";
					$filterurl .= base64_encode($_POST['filter_' . $col['name']]);
				}
			}
?>			

			window.location.href = app + "?id=" + getSelectedRow().<?php echo $this->pkViewName; ?> + "&puri=<?php echo base64_encode($_SERVER['REQUEST_URI'] . $filterurl); ?>&callee=<?php echo base64_encode(basename($_SERVER['PHP_SELF'])); ?>";
		}
		
		<?php
			if (isset($_GET['puri'])) {
		?>
		function back() {
			window.location.href = "<?php echo base64_decode($_GET['puri']); ?>";
		}
		<?php
			}
		?>
		
		function selectFilter(filterid) {
			callAjax(
					"finddata.php", 
					{ 
						sql: "SELECT * FROM <?php echo $_SESSION['DB_PREFIX'];?>filterdata WHERE filterid = " + filterid
					},
					function(data) {
						var i = 0;
						
						$("#filterpanel input").val("");
						$("#filterpanel select").val("");
						
						for (i = 0; i < data.length; i++) {
							var node = data[i];
							
							$("#filter_" + node.columnname).val(node.value);
						}
						
						/* Filter post. */						
						post("filterform", "filter");
					}
				);
		}
		
		function viewSelectedRow() {
			view(getSelectedRow().<?php echo $this->pkViewName; ?>);
		}
		
		function editSelectedRow() {
			<?php
				if ($this->allowEdit) {
					if (isUserAccessPermitted('EditItem')) {
						
					if ($this->preEditScript != null) {
						$this->preEditScript();
					}
			?> 
			edit(getSelectedRow().<?php echo $this->pkViewName; ?>);
			<?php
					}
				}
			?> 
		}
		
		function getSelectedRow() {
			var gr = $("#tempgrid").jqGrid('getGridParam','selrow');
			
			if( gr != null ) {
				return $("#tempgrid").getLocalRow(gr);
			}
			
			return null;
		}
		
		function removeSelectedRow() {
			removeCrudItem(getSelectedRow().<?php echo $this->pkViewName; ?>);
		}
		
		function application(app) {
			post("editform", app);
		}
		
		function filter() {
			$("#filterdialog").dialog("open");
		}
	
		function addCrudItem() {
			<?php
			if ($this->preAddScriptEvent != null) {
				echo $this->preAddScriptEvent . "();\n";
			}
			?>
			
			$(".ui-dialog[aria-labelledby=ui-dialog-title-editdialog] .ui-dialog-title").text("Add");
			$(".ui-dialog[aria-labelledby=ui-dialog-title-editdialog] .ui-dialog-buttonset button:first").css("display", "");
			
			$("#crudcmd").val("insert");
			
			<?php
			foreach ($this->columns as $col) {
				if ($col['editable']) {
					if ($col['readonly'] || $col['type'] == "DERIVED") {
						
						if ($col['type'] == "DATACOMBO" || $col['type'] == "MULTIDATACOMBO") {
							echo "$('#editpanel #" . $col['name'] . "').attr('disabled', true);\n";
							
						} else {
							echo "$('#editpanel #" . $col['name'] . "').attr('readonly', true);\n";
						}
					}

					if ($col['type'] == "TEXTBOX") {
						echo "$('#" . $col['name'] . "').val('');\n";
					
					} else if ($col['type'] == "GEOLOCATION") {
						echo "$('#" . $col['name'] . "').val('');\n";
					
					} else if ($col['type'] == "CHECKBOX") {
						echo "$('#" . $col['name'] . "').attr('checked', false);\n";
					
					} else if ($col['type'] == "DERIVED") {
						echo "$('#" . $col['name'] . "').val('');\n";
						
					} else if ($col['type'] == "FILE") {
						echo "$('#" . $col['name'] . "').val('');\n";
						
					} else if ($col['type'] == "PASSWORD") {
						echo "$('#" . $col['name'] . "').val('');\n";
						
					} else if ($col['type'] == "BASICTEXTAREA") {
						echo "$('#" . $col['name'] . "').val('');\n";
						
					} else if ($col['type'] == "TEXTAREA") {
						echo "tinyMCE.get('" . $col['name'] . "').setContent('');\n";
						
					} else if ($col['type'] == "IMAGE") {
						echo "$('#" . $col['name'] . "_img').attr('src', 'images/no-image.gif');\n";
						
					} else if ($col['type'] == "DATACOMBO") {
						echo "$('#" . $col['name'] . "').val('0');\n";
						
						if (isset($_GET['callee']) && isset($_GET['id'])) {
							if ($col['pk']) {
								echo "$('#" . $col['name'] . "').val('" . $_GET['id'] . "');\n";
								echo "$('#" . $col['name'] . "').attr('disabled', 'true');\n";
							}
						}
						
					} else if ($col['type'] == "LAZYDATACOMBO") {
						echo "$('#" . $col['name'] . "').val('0');\n";
						echo "$('#" . $col['name'] . "_lazy').val('');\n";
						
					} else if ($col['type'] == "MULTIDATACOMBO") {
						echo "$('#" . $col['name'] . "').multiselect('uncheckAll');\n";
						
						if (isset($_GET['callee']) && isset($_GET['id'])) {
							if ($col['pk']) {
								echo "$('#" . $col['name'] . "').val('" . $_GET['id'] . "');\n";
								echo "$('#" . $col['name'] . "').attr('disabled', 'true');\n";
							}
						}
  						
					} else if ($col['type'] == "COMBO") {
						echo "$('#" . $col['name'] . "').val('0');\n";
					}
				}
			}
			
			$this->postAddScriptEvent();
			?>
			$("#editdialog .datepicker").attr("disabled", false);
			$("#editdialog input").attr("readonly", false);
			$("#editdialog input[type=checkbox]").attr("disabled", false);
			$("#editdialog select").attr("disabled", false);
			$(".mceToolbar > div").css("visibility", "visible");
			
			$("#editdialog").dialog("open");
		}
		
		function getPK() {
			return getSelectedRow().<?php echo $this->pkViewName; ?>;
		}
		
		function edit(id) {
			currentCrudID = id;
			
			$(".ui-dialog[aria-labelledby=ui-dialog-title-editdialog] .ui-dialog-title").text("Edit");
			$(".ui-dialog[aria-labelledby=ui-dialog-title-editdialog] .ui-dialog-buttonset button:first").css("display", "");
			
			$("#crudcmd").val("update");
			
			callAjax(
					"finddatarow.php", 
					{ 
						id: id,
						pkname: "<?php echo $this->pkName; ?>",
						table: "<?php echo $this->table; ?>",
						sql: "<?php echo str_replace("\"", "\\\"", $this->sql); ?>"
					},
					function(data) {
						if (data.length > 0) {
							var node = data[0];
							$("#editdialog .datepicker").attr("disabled", false);
							$("#editdialog input").attr("readonly", false);
							$("#editdialog input[type=checkbox]").attr("disabled", false);
							$("#editdialog select").attr("disabled", false);
							$(".mceToolbar > div").css("visibility", "visible");
							
							<?php
							foreach ($this->columns as $col) {
								if ($col['editable']) {
									if ($col['readonly'] || $col['type'] == "DERIVED") {
										
										if ($col['type'] == "DATACOMBO" || $col['type'] == "MULTIDATACOMBO") {
											echo "$('#editpanel #" . $col['name'] . "').attr('disabled', true);\n";
											
										} else {
											echo "$('#editpanel #" . $col['name'] . "').attr('readonly', true);\n";
										}
									}
									
									if ($col['type'] == "TEXTBOX") {
										if ($col['datatype'] == "" || $col['datatype'] == "timestamp") {
											echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ".substring(0, 10));\n";
											echo "$('#editpanel #" . $col['name'] . "_time').val(node." . $col['name'] . ".substring(11, 16));\n";
											
										} else if ($col['datatype'] == "typist") {
											echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ").trigger('change');\n";
											
										} else if ($col['datatype'] == "user") {
											echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ").trigger('change');\n";
											
										} else if ($col['datatype'] == "contact") {
											echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ").trigger('change');\n";
											
										} else {
											echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ");\n";
										}
					
									} else if ($col['type'] == "GEOLOCATION") {
										echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ");\n";
					
									} else if ($col['type'] == "CHECKBOX") {
										echo "$('#editpanel #" . $col['name'] . "').attr('checked', node." . $col['name'] . " == 1 ? true : false);\n";
										echo "$('#editpanel #" . $col['name'] . "').trigger('change');\n";
					
									} else if ($col['type'] == "DERIVED") {
										echo "$('#editpanel #" . $col['name'] . "').val(" . $col['function'] . "(node));\n";
										
									} else if ($col['type'] == "BASICTEXTAREA") {
										echo "if (node." . $col['name'] . " != null) {\n";
										echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ");\n";
										echo "} else {\n";
										echo "$('#editpanel #" . $col['name'] . "').val('');\n";
										echo "}\n";
										
									} else if ($col['type'] == "TEXTAREA") {
										echo "if (node." . $col['name'] . " == null) {\n";
										echo "tinyMCE.get('" . $col['name'] . "').setContent('');\n";
										echo "} else {\n";
										echo "tinyMCE.get('" . $col['name'] . "').setContent(node." . $col['name'] . ");\n";
										echo "}\n";
										
										echo "tinyMCE.get('" . $col['name'] . "').getBody().setAttribute('contenteditable', true);\n";
						
									} else if ($col['type'] == "FILE") {
										echo "if (node." . $col['name'] . " == null) {\n";
										echo "$('#" . $col['name'] . "').val('');\n";
										echo "} else {\n";
										echo "$('#" . $col['name'] . "').val(node." . $col['name'] . ");\n";
										echo "}\n";
						
									} else if ($col['type'] == "PASSWORD") {
										echo "$('#" . $col['name'] . "').val('');\n";
						
									} else if ($col['type'] == "IMAGE") {
										echo "if (node." . $col['name'] . " == 0 || node." . $col['name'] . " == null) {\n";
										echo "$('#" . $col['name'] . "_img').attr('src', 'images/no-image.gif');\n";
										echo "} else {\n";
										echo "$('#" . $col['name'] . "_img').attr('src', 'system-imageviewer.php?id=' + node." . $col['name'] . ");\n";
										echo "}\n";
										echo "$('#" . $col['name'] . "').val('');\n";
										
									} else if ($col['type'] == "COMBO") {
										echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ");\n";
										
									} else if ($col['type'] == "DATACOMBO" || 
											   $col['type'] == "MULTIDATACOMBO" ||
											   $col['type'] == "LAZYDATACOMBO") {
										echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ");\n";
										echo "$('#editpanel #" . $col['name'] . "_lazy').val(node." . $col['alias'] . ");\n";
										
										if (isset($_GET['callee']) && isset($_GET['id'])) {
											if ($col['pk']) {
												echo "$('#" . $col['name'] . "').attr('disabled', 'true');\n";
											}
										}
									}
								}
							}

							foreach ($this->columns as $col) {
								if ($col['type'] == "GEOLOCATION") {
									?>
									if (node.<?php echo $col['name'] . '_lng'; ?> == "0" || node.<?php echo $col['name'] . '_lng'; ?> == null) {
										$("#editpanel #<?php echo $col['name']; ?>").trigger("change");
									}
									<?php
								}
							}
							
							$this->postEditScriptEvent();
							?>
							
						} else {
							pwAlert("No rows found for edit");
						}
					},
					false
				);
			
			$("#crudid").val(id);
			$("#editdialog").dialog("open");
		}
		
		function view(id) {
			currentCrudID = id;
			
			$("#crudcmd").val("view");
			
			$(".ui-dialog[aria-labelledby=ui-dialog-title-editdialog] .ui-dialog-title").text("View");
			$(".ui-dialog[aria-labelledby=ui-dialog-title-editdialog] .ui-dialog-buttonset button:first").css("display", "none");
			
			callAjax(
					"finddatarow.php", 
					{ 
						id: id,
						pkname: "<?php echo $this->pkName; ?>",
						table: "<?php echo $this->table; ?>",
						sql: "<?php echo str_replace("\"", "\\\"", $this->sql); ?>"
					},
					function(data) {
						if (data.length > 0) {
							var node = data[0];
							
							<?php
							foreach ($this->columns as $col) {
								if ($col['editable']) {
									if ($col['type'] == "TEXTBOX") {
										if ($col['datatype'] == "user") {
											echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ").trigger('change');\n";
											
										} else if ($col['datatype'] == "typist") {
											echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ").trigger('change');\n";
											
										} else if ($col['datatype'] == "contact") {
											echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ").trigger('change');\n";
											
										} else {
											echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ");\n";
										}
					
									} else if ($col['type'] == "GEOLOCATION") {
										echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ");\n";
					
									} else if ($col['type'] == "CHECKBOX") {
										echo "$('#" . $col['name'] . "').attr('checked', node." . $col['name'] . " == 1 ? true : false);\n";
										echo "$('#" . $col['name'] . "').trigger('change');\n";
					
									} else if ($col['type'] == "DERIVED") {
										echo "$('#" . $col['name'] . "').val(" . $col['function'] . "(node));\n";
										
									} else if ($col['type'] == "BASICTEXTAREA") {
										echo "$('#editpanel #" . $col['name'] . "').val(node." . $col['name'] . ");\n";
										
									} else if ($col['type'] == "TEXTAREA") {
										echo "tinyMCE.get('" . $col['name'] . "').setContent(node." . $col['name'] . ");\n";
										echo "tinyMCE.get('" . $col['name'] . "').getBody().setAttribute('contenteditable', false);\n";
										
									} else if ($col['type'] == "FILE") {
										echo "$('#" . $col['name'] . "').val(node." . $col['name'] . ");\n";
						
									} else if ($col['type'] == "PASSWORD") {
										echo "$('#" . $col['name'] . "').val('');\n";
						
									} else if ($col['type'] == "IMAGE") {
										echo "if (node." . $col['name'] . " == 0) {\n";
										echo "$('#" . $col['name'] . "_img').attr('src', 'images/no-image.gif');\n";
										echo "} else {\n";
										echo "$('#" . $col['name'] . "_img').attr('src', 'system-imageviewer.php?id=' + node." . $col['name'] . ");\n";
										echo "}\n";
										echo "$('#" . $col['name'] . "').val('');\n";
										
									} else if ($col['type'] == "COMBO") {
										echo "$('#" . $col['name'] . "').val(node." . $col['name'] . ");\n";
										
									} else if ($col['type'] == "DATACOMBO" || 
											   $col['type'] == "MULTIDATACOMBO" || 
											   $col['type'] == "LAZYDATACOMBO") {
										echo "$('#" . $col['name'] . "').val(node." . $col['name'] . ");\n";
										echo "$('#" . $col['name'] . "_lazy').val(node." . $col['alias'] . ");\n";
										
										if (isset($_GET['callee']) && isset($_GET['id'])) {
											if ($col['pk']) {
												echo "$('#" . $col['name'] . "').attr('disabled', 'true');\n";
											}
										}
									}
								}
							}
							
							$this->postViewScriptEvent();
							?>
							$(".mceToolbar > div").css("visibility", "hidden");
							$("#editdialog input").attr("readonly", true);
							$("#editdialog input[type=checkbox]").attr("disabled", true);
							$("#editdialog select").attr("disabled", true);
							$("#editdialog .datepicker").attr("disabled", true);
						}
					},
					false
				);
			
			$("#crudid").val(id);
			$("#editdialog").dialog("open");
		}
	
		function post(form, command, target, parameters) {
			var prevCMD = $("#" + form + " #crudcmd").val();
			
			if (target && target != null) {
				$("#" + form).attr("target", target);
				$("#" + form + " #triggerrefresh").val("true");
				
			} else {
				$("#" + form).attr("target", "");
			}
			
			if (parameters) {
				for (var param in parameters) {
					if (parameters[param] instanceof Array) {
						for (var ix = 0; ix < parameters[param].length; ix++) {
							$("<input type='hidden' id='" + param + "' name='" + param + "[]' value='" + parameters[param][ix] + "' />\n").appendTo("#editform");
						}
						
					} else {
						$("#" + form + " #" + param).val(parameters[param]);
					}
					
				}
			}
						
			$("#" + form + " #crudcmd").val(command);
			$("#" + form).submit();
			
			$("#" + form + " #crudcmd").val(prevCMD);
		}
		
		function crudDelete() {
			var correct = true;
			
			$("#confirmdialog").dialog("close");
			
			<?php
			foreach ($this->checkconstraints as $constraint) {
			?>
			callAjax(
					"finddata.php", 
					{ 
						sql: "SELECT <?php echo $constraint['column']; ?> FROM <?php echo $_SESSION['DB_PREFIX'] . $constraint['table']; ?> WHERE <?php echo $constraint['column']; ?> = " + currentCrudID
					},
					function(data) {
						if (data.length >= 1) {
							correct = false;
						}
					},
					false
				);
			<?php
			}
			
			if (! correct) {
				pwAlert("Row is currently in use. Cannot remove.");
				return;
			}
			
			if ($this->preDeleteScriptEvent != null) {
				echo "if (! ". $this->preDeleteScriptEvent . "(currentCrudID)) return;\n";
			}
			?>
			
			callAjax(
					"cruddelete.php", 
					{ 
						table: "<?php echo $this->table; ?>",
						pkname: "<?php echo $this->pkName; ?>",
						id: currentCrudID
					},
					function(data) {
					},
					false
				);
<?php 
			if ($this->postDeleteScriptEvent != null) {
				echo $this->postDeleteScriptEvent . "(currentCrudID);\n";
			}
?>
			
			refresh();
		}
		
		function removeCrudItem(crudID) {
			currentCrudID = crudID;
			
			$("#confirmdialog .confirmdialogbody").html("You are about to remove this item.<br>Are you sure ?");
			$("#confirmdialog").dialog("open");
		}
		
		function refresh() {
			document.body.style.cursor = "wait";
			
			setTimeout(refreshData, 0);
		}
		
		function showError(str) {
			pwAlert("An error has occurred: " + str);
		}
		
		var colNames = new Array();
		
		$(document).ready(
				function() {
					 var grid = $("#tempgrid");
					 var layout = new Array();
					 var info;
					 var colIndex = 0;
					 
				   	$(".multiselect").multiselect({
				   			multiple: true
					   }); 
					 
					<?php
					$visibleIndex = 1;
						
					for ($i = 0; $i < count($this->columns); $i++) {
						$width = 0;
						$hidden = false;
						$columnlabel = "";
						
						if ($this->columns[$i]['showInView']) {
							$width = $this->columns[$i]['length'];
							
							if ($this->columns[$i]['length'] < strlen($this->columns[$i]['label'])) {
								$width = strlen($this->columns[$i]['label']);
							}
							
							$width = intval($width * 6.2);
				
							$this->columns[$i]['viewlength'] = $width;
							$this->columns[$i]['hidden'] = $hidden;
							
							$visibleIndex++;
						}
					}
					
					foreach ($this->columns as $col) {
						if ($col['showInView'] || $col['pk']) {
					?>
					 info = {
							index:		"<?php echo $col['viewname']; ?>",
							name:		"<?php echo $col['viewname']; ?>",
							resizable:	false,
							width:		<?php echo $col['viewlength']; ?>,
							hidden:		<?php echo ($col['pk'] && ! $col['showInView']) || $col['hidden'] ? "true" : "false";?>,
							align:		"<?php echo $col['align']; ?>",
							sortable:   false
							<?php 
								if ($col['type'] == "CHECKBOX") {
									echo ", formatter: checkboxFormatter";
									
								} else if ($col['formatter'] != "") {
									echo ", formatter: " . $col['formatter'];
								}
								
							?>
						};
						
					 colNames[colIndex] = "<?php echo $col['label']; ?>";
					 layout[colIndex++] = info;
					<?php
						}
					}
					?>

					 grid.jqGrid({
							datatype: "local",
							height: 450,
						   	colNames: colNames,
						   	colModel: layout,
						   	sortable: false,
							shrinkToFit: false,
							autowidth: true,
							rowNum : 18,
						   	rowList: [18,20,30,50,80,100],
						   	pager: "#tempgrid_pager",
						   	
						   	viewRecords: true,
						   	multiselect: false,
						   	
							afterInsertRow: function(rowid, rowData, rowelem) {
						   		<?php
						   				$this->afterInsertRow ();
						   		?>
   						    },						   	
			   	
							ondblClickRow: function (rowid,iRow,iCol,e) {
							<?php
								if ($this->onDblClick != null) {
							?>
									<?php echo $this->onDblClick; ?>(getSelectedRow().<?php echo $this->pkViewName; ?>);
							<?php		
								} else if ($this->allowEdit) {
									if (isUserAccessPermitted('EditItem')) {
							?> 
										editSelectedRow();
							<?php
									}
								}
							?> 
					        },						    
						    onSelectRow: function(rowid) {
						    	$(".subapp").removeAttr("disabled");
								
								<?php
									if ($this->onClickCallback != "") {
										echo "$this->onClickCallback(getSelectedRow());\n";
									}
								?>
						    },
							caption: "<?php echo $this->title; ?>"
						
						});
					
					
				    $('form').bind('submit', function() { 
					        $(this).find('select').removeAttr('disabled'); 
					    }); 
					    
					$("#filterbutton").hover( 
							function () { 
								var child = $(this).find('ul');
								
								child.css("margin-top", "25px");
								child.show();
						  	},  
						  	function () { 
								var child = $(this).find('ul');
								var frame = $(this).find('iframe');
								
						  		child.hide();
								frame.hide();
						  	} 
						); 
				
					$(".submenuwrapper").hover( 
							function () { 
								if ($(this).find("a").attr("disabled") != true) {
									var child = $(this).find('ul');
	
									child.css("margin-top", "-17px");
									child.css("margin-left", (($(this).find('a').offset().left - $(this).parent().offset().left) + 9) + "px");
									child.show();
								}
						  	},  
						  	function () { 
								var child = $(this).find('ul');
								var frame = $(this).find('iframe');
								
						  		child.hide();
								frame.hide();
						  	} 
						); 
					 					
					$("#editdialog").dialog({
							modal: true,
							autoOpen: false,
							show:"fade",
							hide:"fade",
							width: <?php echo $this->dialogwidth; ?>,
							title:"Edit / Add",
							open: function(event, ui){
								
							},
							buttons: {
								Ok: function() {
									if (! verifyCrudForm("#editpanel")) {
										return;
									}
									
									tinyMCE.triggerSave();
									
									if ($("#crudcmd").val() == "insert") {
<?php
		for ($i = 0; $i < count($this->columns); $i++) {
			if ($this->columns[$i]['unique'] == true) {
?>				
				if (! verifyUniqueKey("<?php echo $this->columns[$i]['name']; ?>", $("#<?php echo $this->columns[$i]['name']; ?>").val())) {
					return;
				}
<?php				
			}
		}
?>									
									}
									
									$(this).dialog("close")
									
									post("editform", $("#editform #crudcmd").val(), "submitframe");
<?php									
									$this->postUpdateScriptEvent();
?>									
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});
 					
					$("#filterdialog").dialog({
							modal: true,
							autoOpen: false,
							show:"fade",
							hide:"fade",
							width: <?php echo $this->dialogwidth; ?>,
							title:"Search",
							open: function(event, ui){
								
							},
							buttons: {
								"Search": function() {
									$(this).dialog("close")
									
									post("filterform", "filter");
								},
								"Save": function() {
									$("#filtersavedialog").dialog("open");
								},
								"Clear": function() {
									$("#filterform input").val("");
									$("#filterform select").val("");
								},
								Cancel: function() {
									$("#filterdialog").dialog("close");
								}
							}
						});
 					
					$("#filtersavedialog").dialog({
							modal: true,
							autoOpen: false,
							show:"fade",
							hide:"fade",
							title:"Save Filter",
							open: function(event, ui){
								
							},
							buttons: {
								Ok: function() {
									$(this).dialog("close")
									$("#savefiltername").val($("#filtername").val());
									
									post("filterform", "filtersave");
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});
						
					<?php
						if ($this->autoPopulate) {
							$this->rowcount = $this->getRowCount();
							$this->pages = intval($this->rowcount / $this->pagesize);
							
							if (($this->rowcount % $this->pagesize) > 0) {
								$this->pages++;
							}
							
							if ($this->pages == 0) {
								$this->pages = 1;
							}
					?>
							pages = <?php echo $this->pages; ?>;
							refresh();
							
					<?php
						} else {
							$this->rowcount = 0;
							$this->pages = 1;
					?>
							pages = <?php echo $this->pages; ?>;
					<?php
						}
						
					?>
					
					var marker = false;
					
					$(".ui-pg-table td[dir='ltr']").each(
							function() {
								if (! marker) {
									$(this).html("Page <?php echo intval(($this->fromrow / $this->pagesize) + 1); ?> of <?php echo intval($this->pages); ?>");
									marker = true;
								}
							}
						);
					
					$(".ui-jqgrid-sortable").click(
							function() {
								var str = $(this).attr("id");
								var n=str.lastIndexOf("_") + 1; 
								var column = str.substring(n);
								
								$(".ui-jqgrid-sortable span").hide();
								$(this).find("span").show();
								
								<?php
									foreach ($this->columns as $col) {
										if ($col['sortcolumn'] != null) {
								?>
								if (column == "<?php echo $col['name']; ?>") column = "<?php echo $col['sortcolumn']; ?>";
								<?php
											
										}
									}
								?>
									
								if (sortByColumn == column) {
									/* Same column, so sort in reverse. */
									if (sortByDirection  == "ASC") {
										sortByDirection = "DESC";
										
									} else {
										sortByDirection = "ASC";
									}
									
								} else {
									sortByColumn = column;
									sortByDirection = "ASC";
								}
								
								refresh();
							}
						);
						
					$(".ui-pager-control .ui-icon-seek-first").click(
							function() {
								fromRow = 0;
								toRow = pageSize;
								
								refresh();
							}
						);
						
					$(".ui-pager-control .ui-icon-seek-end").click(
							function() {
								fromRow = parseInt((pages - 1) * pageSize);
								toRow = pageSize;
								
								refresh();
							}
						);
						
					$(".ui-pager-control .ui-icon-seek-prev").click(
							function() {
								if (fromRow > 0) {
									fromRow = parseInt(fromRow) - parseInt(pageSize);
									toRow = pageSize;
									
									refresh();
								}
							}
						);
						
					$(".ui-pg-selbox").change(
							function() {
								pageSize = parseInt($(this).val());
								fromRow = 0;
								toRow = pageSize;
								
								refresh();
							}
						);
						
					$(".ui-pager-control .ui-icon-seek-next").click(
							function() {
								if ((fromRow + pageSize) < <?php echo $this->rowcount; ?>) {
									fromRow = parseInt(fromRow) + parseInt(pageSize);
									toRow = pageSize;
									
									refresh();
								}
							}
						);
						
	<?php
					$this->postLoadScriptEvent();
	?>
				}
			);
			
		function refreshData() {
	    	$(".subapp").attr("disabled", true);

			callAjax(
					"finddata.php", 
					{ 
						sql: "<?php echo str_replace("\"", "\\\"", $this->sql); ?>",
						orderby: sortByColumn,
						direction: sortByDirection,
						from: fromRow,
						to: pageSize
					},
					function(data) {
						var marker = false;
						pages = parseInt(<?php echo $this->rowcount; ?> / pageSize);
					
						if ((<?php echo $this->rowcount; ?> % pageSize) > 0) {
							pages++;
						}
						
						if (pages == 0) {
							pages = 1;
						}
					
						$(".ui-pg-table td[dir='ltr']").each(
								function() {
									if (! marker) {
										$(this).html("Page " + ((fromRow / pageSize) + 1) + " of " + pages);
										
										marker = true;
									}
								}
							);
							
						$("#tempgrid").clearGridData(true);
						
						var i = 0;
						var indexNo = 1;
						var item;
						for (i = 0; i < data.length; i++) {
							var node = data[i];
<?php
							$first = true;
							
							echo "item = {";
										
							foreach ($this->columns as $col) {
								
								if ($col['showInView'] || $col['pk']) {
									if ($first) {
										$first = false;
												
									} else {
										echo ", ";
									}
											
									echo "'" . $col['viewname'] . "': ";
											
									if ($col['type'] == "DATACOMBO" || 
									    $col['type'] == "LAZYDATACOMBO" || 
									    $col['type'] == "MULTIDATACOMBO") {
										echo "node.";
									
										if ($col['alias'] != '') {
											echo $col['alias'];
													
										} else {
											echo $col['table_name'];
										}
											
									} else if ($col['type'] == "DERIVED") {
										echo $col['function'] . "(node)";
									
									} else if ($col['type'] == "COMBO") {
										$comboArray = array();
										$descArray = array();
										
										foreach ($col['options'] as $opt) {
											array_push($comboArray, $opt['value']); 
											array_push($descArray, $opt['text']); 
										}

										echo "getComboValue(node." . $col['name'] . ", new Array(" . ArrayToInClause($comboArray) . "), new Array(" . ArrayToInClause($descArray) . "))";
												
									} else {
										echo "node." . $col['name'];
									}
								}
							}
	
							echo "};\n";
							echo "$('#tempgrid').addRowData(indexNo++, item);\n";
?>
						}
						
						$(".ui-state-disabled").each(
								function() {
									$(this).removeClass("ui-state-disabled");
								}
							);
							
						<?php
						if ($this->postDataRefreshEvent != null) {
							echo $this->postDataRefreshEvent . "(data);\n";
						}
						?>
						
						document.body.style.cursor = "default";
					}
			);
		}
	<?php
		$this->postScriptEvent();
	?>
	
	function checkboxFormatter(el, cval, opts) {
		if (el == 0) {
			return "<img height=16  src='images/checkbox_off.png' />";
		}
		
		return "<img height=16 src='images/checkbox_on.png' />";
    } 	
    
	function getComboValue(value, comboArray, descArray) {
		for (var i = 0; i < comboArray.length; i++) {
			if (comboArray[i] == value) {
				return descArray[i];
			}
		}
		
		return "";
	}
			
	</script>
<?php		
	} 
	
	public function createView() {
		if (isset($_GET['puri'])) {
			$this->crud_cache_function("showHTMLAssets", array("pageid" => $_SESSION['pageid'], "url" => $_GET['puri']));
			
		} else {
			$this->crud_cache_function("showHTMLAssets", array("pageid" => $_SESSION['pageid']));
		}
?>

	<?php
		require_once("system-footer.php");
	}

	public function delete($id) {
		$qry = "DELETE FROM " . $this->table . " WHERE " . $this->pkName . " = $id";
		
		logError("$qry", false);
		$result = mysql_query($qry);
		
		if (! $result) {
			logError(mysql_error());
		}
		
		$this->postDeleteEvent($id);
	}

	public function update($id) {
		try {
			$this->preUpdateEvent($id);
			
			$qry = "UPDATE " . $this->table . " SET ";
			$first = true;
			
			foreach ($this->columns as $col) {
				if ($col['bind']) {
					if ($first) {
						$first = false;
						
					} else {
						$qry = $qry . ", ";
					}
					
					if ($col['type'] == "IMAGE") {
						if (is_uploaded_file($_FILES[$col['name']]['tmp_name'])) {
							$qry = $qry . $col['name'] . " = " . getImageData($col['name']) . "";
							
						} else {
							$qry = $qry . $col['name'] . " = " . $col['name'] . "";
						}
						
					} else if ($col['type'] == "FILE") {
						if (is_uploaded_file($_FILES[$col['name']]['tmp_name'])) {
							$qry = $qry . $col['name'] . " = " . getFileData($col['name']) . "";
							
						} else {
							$qry = $qry . $col['name'] . " = " . $col['name'] . "";
						}
						
					} else if ($col['type'] == "CHECKBOX") {
						$qry = $qry . $col['name'] . " = " . (isset($_POST[$col['name']]) ? ($_POST[$col['name']] == "on" ? 1 : 0) : 0);
						
					} else if ($col['type'] == "PASSWORD") {
						$qry = $qry . $col['name'] . " = '" . mysql_escape_string(md5($_POST[$col['name']])) . "'";
						
					} else {
						if (isset($_POST[$col['name']])) {
							if ($col['datatype'] == "timestamp" || $col['datatype'] == "") {
								$mysql_date = convertStringToDate($_POST[$col['name']]);
								$mysql_time = $_POST[$col['name'] . "_time"];
								
								$qry = $qry . $col['name'] . " = '" . mysql_escape_string($mysql_date) . " $mysql_time'";
								
							} else if ($col['datatype'] == "date") {
								$mysql_date = convertStringToDate($_POST[$col['name']]);
								
								$qry = $qry . $col['name'] . " = '" . mysql_escape_string($mysql_date) . "'";
								
							} else {
								$qry = $qry . $col['name'] . " = '" . mysql_escape_string($_POST[$col['name']]) . "'";
							}
							
						} else {
							$qry = $qry . $col['name'] . " = '" . mysql_escape_string($col['default']) . "'";
						}
					}
				}
			}
			
			$qry = $qry . ", metamodifieddate = NOW(), metamodifieduserid = " . getLoggedOnMemberID() . " WHERE " . $this->pkName . " = '$id'";
			
			$result = mysql_query($qry);
			
			if (! $result) {
				logError($qry . " = " . mysql_error());
			}
			
			$this->postUpdateEvent($id);
			
		} catch (Exception $e) {
			$this->errorDescriptions[] = $e->getMessage();
		}
	}

	public function insert() {
		try {
			$qry = "INSERT INTO " . $this->table . " (";
			$first = true;
			
			foreach ($this->columns as $col) {
				if ($col['bind']) {
					if ($first) {
						$first = false;
						
					} else {
						$qry = $qry . ", ";
					}
					
					$qry = $qry . $col['name'];
				}
			}
			
			$qry = $qry . ", metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES (";
			$first = true;
			
			foreach ($this->columns as $col) {
				if ($col['bind']) {
					if ($first) {
						$first = false;
						
					} else {
						$qry = $qry . ", ";
					}
					
					if ($col['type'] == "IMAGE") {
						$qry = $qry . "'" . getImageData($col['name']) . "'";
						
					} else if ($col['type'] == "FILE") {
						$qry = $qry . "'" . getFileData($col['name']) . "'";
						
					} else if ($col['type'] == "PASSWORD") {
						$qry = $qry . "'" . md5($_POST[$col['name']]) . "'";
						
					} else if ($col['type'] == "CHECKBOX") {
						$qry = $qry . (isset($_POST[$col['name']]) ? ($_POST[$col['name']] == "on" ? 1 : 0) : 0);
						
					} else {
						if (isset($_POST[$col['name']])) {
							if ($col['datatype'] == "timestamp" || $col['datatype'] == "") {
								$mysql_date = convertStringToDate($_POST[$col['name']]);
								$mysql_time = $_POST[$col['name'] . "_time"];
								
								$qry = $qry . "'" . mysql_escape_string($mysql_date) . " $mysql_time'";
								
							} else if ($col['datatype'] == "date") {
								$mysql_date = convertStringToDate($_POST[$col['name']]);
								
								$qry = $qry . "'" . mysql_escape_string($mysql_date) . "'";
								
							} else {
								$qry = $qry . "'" . mysql_escape_string($_POST[$col['name']]) . "'";
							}
							
						} else {
							if ($col['default'] == "TODAY") {
								$qry = $qry . "NOW()";
								
							} else if ($col['default'] == "USER") {
								$qry = $qry . getLoggedOnMemberID();
								
							} else {
								$qry = $qry . "'" . mysql_escape_string($col['default']) . "'";
							}
						}
					}
					
				}
			}
			
			$memberid = getLoggedOnMemberID();
			$qry = $qry . ", NOW(), $memberid, NOW(), $memberid)";
	
			$result = mysql_query($qry);
			
			if (! $result) {
				logError($qry . " = " . mysql_error());
			}
			
			$this->postInsertEvent();
			
		} catch (Exception $e) {
			$this->errorDescriptions[] = $e->getMessage();
		}
	}
	
	private function getRowCount() {
		$parser = new PHPSQLParser($this->sql);
		$amount = 0;
		
		for ($i = count($parser->parsed['SELECT']) - 1; $i >=0; $i--) {
			unset($parser->parsed['SELECT'][$i]);
		}
		
		$parser->parsed['SELECT'][] =
				array(
						"expr_type" 		=> "colref",
						"alias"				=> "a",
						"base_expr"			=> "COUNT(*)",
						"sub_tree"			=> ""
					);
					
		$creator = new PHPSQLCreator($parser->parsed);
		$result = mysql_query($creator->created);
		
		if (! $result) {
			logError($parser->parsed . " = " . mysql_error());
		}
		
		//Check whether the query was successful or not
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				if (isset($member['a'])) {
					$amount = $member['a'];
				}
			}
		}
		
		return $amount;
	}
	
	public function crud_cache_function($functionname, $arguments = array()) {
//				$stti = microtime(true);
		$encoded = md5(json_encode($arguments));
		$cachekey = 'CRUD_CACHE_' . $functionname . "_" . $encoded;
		
		if (! isset($_SESSION[$cachekey]) || $_SESSION['CACHING'] != "temp") {
			ob_start(); //Turn on output buffering 
			
			call_user_func(array($this, $functionname));
			
			$_SESSION[$cachekey] = ob_get_clean(); 
//			$fiti = number_format(microtime(true) - $stti, 6);
//			logError("<h1>CRUD - NONE CACHED $cachekey - ELAPSED $fiti:</h1>", false) ;
			
//		} else {
//			$fiti = number_format(microtime(true) - $stti, 6);
//			logError("<h1>CRUD - CACHED $cachekey - ELAPSED $fiti</h1>", false) ;
		}
		
		echo $_SESSION[$cachekey];
		
	}
}