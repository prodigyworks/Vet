<?php
require ('fpdf.php');
require_once("simple_html_dom.php");

// function hex2dec
// returns an associative array (keys: R,G,B) from
// a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000") {
	$R = substr ( $couleur, 1, 2 );
	$rouge = hexdec ( $R );
	$V = substr ( $couleur, 3, 2 );
	$vert = hexdec ( $V );
	$B = substr ( $couleur, 5, 2 );
	$bleu = hexdec ( $B );
	$tbl_couleur = array ();
	$tbl_couleur ['R'] = $rouge;
	$tbl_couleur ['V'] = $vert;
	$tbl_couleur ['B'] = $bleu;
	return $tbl_couleur;
}

// conversion pixel -> millimeter at 72 dpi
function px2mm($px) {
	return $px * 25.4 / 72;
}
function txtentities($html) {
	$trans = get_html_translation_table ( HTML_ENTITIES );
	$trans = array_flip ( $trans );
	return strtr ( $html, $trans );
}
class PDFReport extends FPDF {
	// private variables
	var $colonnes;
	var $format;
	var $angle = 0;
	var $B;
	var $I;
	var $U;
	var $HREF;
	var $fontList;
	var $issetfont;
	var $issetcolor;

	function WriteHTML($indent = 0, $indentY = 0, $html) {
		$oldmargin = $this->lMargin;

		$this->SetX($indent);
		$this->SetY($indentY);

		$this->SetLeftMargin($indent);
		$this->SetRightMargin(15);

		$html = str_replace ( "&rsquo;", "'", str_replace ( "&lsquo;", "'", str_replace ( "&rdquo;", "'", str_replace ( "&ldquo;", "'", str_replace ( "&ndash;", "-", str_replace ( "&amp;", "&", str_replace ( "&lt;", "<", str_replace ( "&gt;", "<", str_replace ( "&apos;", "'", str_replace ( "&quot;", "\"", str_replace ( "&ndash;", "-", str_replace ( "&nbsp;", " ", $html ) ) ) ) ) ) ) ) ) ) )  );

		// HTML parser
		$html = strip_tags ( $html, "<b><u><i><a><ul><li><img><p><br><h1><h2><h3><h4><h5><h6><h7><strong><em><font><tr><blockquote>" ); // supprime tous les tags sauf ceux reconnus
		$html = str_replace ( "\n", ' ', $html ); // remplace retour à la ligne par un espace
		$a = preg_split ( '/<(.*)>/U', $html, - 1, PREG_SPLIT_DELIM_CAPTURE ); // éclate la chaîne avec les balises

		foreach ( $a as $i => $e ) {
			if ($i % 2 == 0) {
				// Text
				if ($this->HREF)
					$this->PutLink ( $this->HREF, $e );
				else
					$this->Write ( 4, stripslashes ( txtentities ( $e ) ) );
			} else {
				// Tag
				if ($e [0] == '/')
					$this->CloseTag ( strtoupper ( substr ( $e, 1 ) ) );
				else {
					// Extract attributes
					$a2 = explode ( ' ', $e );
					$tag = strtoupper ( array_shift ( $a2 ) );
					$attr = array ();
					foreach ( $a2 as $v ) {
						if (preg_match ( '/([^=]*)=["\']?([^"\']*)/', $v, $a3 ))
							$attr [strtoupper ( $a3 [1] )] = $a3 [2];
					}
					$this->OpenTag ( $tag, $attr );
				}
			}
		}

		$this->SetLeftMargin($oldmargin);

		return $this->GetY() + 8;
	}
	
function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        if (strpos($corners, '2')===false)
            $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($corners, '3')===false)
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false)
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        if (strpos($corners, '1')===false)
        {
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

	function OpenTag($tag, $attr) {
		// Opening tag
		switch ($tag) {
			case 'STRONG' :
				$this->SetStyle ( 'B', true );
				break;
			case 'EM' :
				$this->SetStyle ( 'I', true );
				break;
			case 'UL' :
				break;
			case 'LI' :
				$this->SetFont ( 'Arial', '', 9 );
				$this->Ln ( 1 );
				$this->Write ( 4, "o   " );
				$this->SetLeftMargin($this->lMargin + 5);
				break;
			case 'B' :
			case 'I' :
			case 'U' :
				$this->SetStyle ( $tag, true );
				break;
			case 'A' :
				$this->HREF = $attr ['HREF'];
				break;
			case 'IMG' :
				if (isset ( $attr ['SRC'] ) && (isset ( $attr ['WIDTH'] ) || isset ( $attr ['HEIGHT'] ))) {
					if (! isset ( $attr ['WIDTH'] ))
						$attr ['WIDTH'] = 0;
					if (! isset ( $attr ['HEIGHT'] ))
						$attr ['HEIGHT'] = 0;
					$this->Image ( $attr ['SRC'], $this->GetX (), $this->GetY (), px2mm ( $attr ['WIDTH'] ), px2mm ( $attr ['HEIGHT'] ) );
				}
				break;
			case 'TR' :
			case 'BLOCKQUOTE' :
			case 'BR' :
				$this->Ln ( 5 );
				break;
			case 'H1':
				$this->Ln ( 5 );
				$this->SetFont ( 'Arial', 'B', 20 );
				break;
			case 'H2':
				$this->Ln ( 5 );
				$this->SetFont ( 'Arial', 'B', 18 );
				break;
			case 'H3':
				$this->Ln ( 5 );
				$this->SetFont ( 'Arial', 'B', 16 );
				break;
			case 'H4':
				$this->Ln ( 5 );
				$this->SetFont ( 'Arial', 'B', 14 );
				break;
			case 'H5':
				$this->Ln ( 5 );
				$this->SetFont ( 'Arial', 'B', 12 );
				break;
			case 'H6':
				$this->Ln ( 5 );
				$this->SetFont ( 'Arial', 'B', 10 );
				break;
			case 'H7':
				$this->Ln ( 5 );
				$this->SetFont ( 'Arial', 'B', 9 );
				break;
			case 'P' :
				$this->SetFont ( 'Arial', '', 9 );
				$this->Ln ( 3 );
				break;
			case 'FONT' :
				if (isset ( $attr ['COLOR'] ) && $attr ['COLOR'] != '') {
					$coul = hex2dec ( $attr ['COLOR'] );
					$this->SetTextColor ( $coul ['R'], $coul ['V'], $coul ['B'] );
					$this->issetcolor = true;
				}
				if (isset ( $attr ['FACE'] ) && in_array ( strtolower ( $attr ['FACE'] ), $this->fontlist )) {
					$this->SetFont ( strtolower ( $attr ['FACE'] ) );
					$this->issetfont = true;
				}
				break;
		}
	}

	function CloseTag($tag) {
		// Closing tag
		if ($tag == 'STRONG')
			$tag = 'B';
		if ($tag == 'EM')
			$tag = 'I';

		if ($tag == 'B' || $tag == 'I' || $tag == 'U')
			$this->SetStyle ( $tag, false );

		if ($tag == 'A')
			$this->HREF = '';

		if ($tag == 'FONT') {
			if ($this->issetcolor == true) {
				$this->SetTextColor ( 0 );
			}

			if ($this->issetfont) {
				$this->SetFont ( 'arial' );
				$this->issetfont = false;
			}
		}

		if ($tag == 'LI') {
			$this->SetLeftMargin($this->lMargin - 5);
			$this->Ln(3);

		} else if ($tag == 'P') {
			$this->Ln(3);

		} else if ($tag == 'H1' || $tag == 'H2' || $tag == 'H3' || $tag == 'H4' || $tag == 'H5' || $tag == 'H6' || $tag == 'H7') {
			$this->Ln(5);
		}
	}

	function SetStyle($tag, $enable) {
		// Modify style and select corresponding font
		$this->$tag += ($enable ? 1 : - 1);
		$style = '';
		foreach ( array (
				'B',
				'I',
				'U'
		) as $s ) {
			if ($this->$s > 0)
				$style .= $s;
		}
		$this->SetFont ( '', $style );
	}
	function PutLink($URL, $txt) {
		// Put a hyperlink
		$this->SetTextColor ( 0, 0, 255 );
		$this->SetStyle ( 'U', true );
		$this->Write ( 4, $txt, $URL );
		$this->SetStyle ( 'U', false );
		$this->SetTextColor ( 0 );
	}

	function stripHTML($html) {
		$txt = str_get_html ( "<html>" . $html . "</html>" )->plaintext;
		$txt = str_replace ( "&ndash;", "-", str_replace ( "&amp;", "&", str_replace ( "&lt;", "<", str_replace ( "&gt;", "<", str_replace ( "&ndash;", "-", str_replace ( "&nbsp;", " ", $txt ) ) ) ) ) );

		$buildingarr = array ();
		$buildingstr = "";

		$strings = explode ( " ", $txt );

		foreach ( $strings as $str ) {
			if ($buildingstr != "") {
				$buildingstr .= " " . $str;
			} else {
				$buildingstr = $str;
			}
		}

		$buildingarr [] = $buildingstr;
		$buildingstr = "";

		foreach ( $buildingarr as $str ) {
			if ($buildingstr != "") {
				$buildingstr .= "\n" . $str;
			} else {
				$buildingstr = $str;
			}
		}

		return $buildingstr;
	}
	function DynamicImageHeight($id, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '') {
		$query = mysql_query ( "SELECT mimetype, image FROM {$_SESSION['DB_PREFIX']}images WHERE id=$id" );
		$row = mysql_fetch_array ( $query );
		$content = $row ['image'];
		$mimetype = $row ['mimetype'];

		$name = "uploads/image_" . session_id () . "-$id." . substr ( $mimetype, strpos ( $mimetype, "/" ) + 1 );

		$f = fopen ( $name, 'wb' );

		if (! $f) {
			$this->Error ( 'Unable to create output file: ' . $name );
		}

		fwrite ( $f, $content );
		fclose ( $f );

		$newY = $this->ImageHeight ( $name, $x, $y, $w, $h, $type, $link );

		unlink ( $name );

		return $newY;
	}
	function DynamicImage($id, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '') {
		$query = mysql_query ( "SELECT mimetype, image FROM {$_SESSION['DB_PREFIX']}images WHERE id=$id" );
		$row = mysql_fetch_array ( $query );
		$content = $row ['image'];
		$mimetype = $row ['mimetype'];

		$name = "uploads/image_" . session_id () . "-$id." . substr ( $mimetype, strpos ( $mimetype, "/" ) + 1 );

		$f = fopen ( $name, 'wb' );

		if (! $f) {
			$this->Error ( 'Unable to create image output file: ' . $name );
		}

		fwrite ( $f, $content );
		fclose ( $f );

		$newY = $this->Image ( $name, $x, $y, $w, $h, $type, $link );

		unlink ( $name );

		return $newY;
	}

	// public functions
	function sizeOfText($texte, $largeur) {
		$index = 0;
		$nb_lines = 0;
		$loop = TRUE;
		while ( $loop ) {
			$pos = strpos ( $texte, "\n" );
			if (! $pos) {
				$loop = FALSE;
				$ligne = $texte;
			} else {
				$ligne = substr ( $texte, $index, $pos );
				$texte = substr ( $texte, $pos + 1 );
			}
			$length = floor ( $this->GetStringWidth ( $ligne ) );
			$res = 1 + floor ( $length / $largeur );
			$nb_lines += $res;
		}
		return $nb_lines;
	}

	// Company
	function addHeading($x1, $y1, $heading, $value, $margin = 36, $fontSize = 7, $lineheight = 3) {
		// Positionnement en bas
		$this->SetTextColor ( 0, 0, 100 );
		;
		$this->SetXY ( $x1, $y1 );
		$this->SetFont ( 'Arial', 'B', $fontSize + 1 );
		$length = $this->GetStringWidth ( $heading ) * 2;
		$tailleTexte = $this->sizeOfText ( $heading, $length );
		$this->MultiCell ( $length, $lineheight, $heading );

		$maxY = $this->GetY ();

		$this->SetTextColor ( 0, 0, 0 );
		;
		$this->SetXY ( $x1 + $margin, $y1 );
		$this->SetFont ( 'Arial', '', $fontSize );
		$length = $this->GetStringWidth ( $value . " " ) * 2;
		$tailleTexte = $this->sizeOfText ( $value, $length );
		$this->MultiCell ( $length, $lineheight, $value );

		if ($this->GetY () > $maxY) {
			$maxY = $this->GetY ();
		}

		return $maxY;
	}
	function addText($x1, $y1, $heading, $fontSize = 7, $lineheight = 3, $style = '', $width = 0) {
		if ($heading == null || trim ( $heading ) == "") {
			$heading = " ";
		}

		// Positionnement en bas
		$this->SetXY ( $x1, $y1 );
		$this->SetFont ( 'Arial', $style, $fontSize );

		if ($width != 0) {
			$length = $width;
		} else {
			$length = $this->GetStringWidth ( $heading ) * 2;
		}

		$before = $this->GetY ();

		$this->MultiCell ( $length, $lineheight, $heading );

		return $this->GetY ();
	}
	function newPage() {
	}
	function addCols($y1, $tab) {
		$r1 = 10;
		$r2 = $this->w - ($r1 * 2);
		$y2 = $this->h - 28 - $y1;
		$this->SetXY ( $r1, $y1 );
		$this->Rect ( $r1, $y1, $r2, $y2, "D" );
		$this->Line ( $r1, $y1 + 6, $r1 + $r2, $y1 + 6 );
		$colX = $r1;
		$this->colonnes = $tab;
		while ( list ( $lib, $pos ) = each ( $tab ) ) {
			$this->SetXY ( $colX, $y1 + 2 );
			$this->Cell ( $pos, 1, $lib, 0, 0, "C" );
			$colX += $pos;
			$this->Line ( $colX, $y1, $colX, $y1 + $y2 );
		}
	}
	function addLineFormat($tab) {
		global $format;

		while ( list ( $lib, $pos ) = each ( $this->colonnes ) ) {
			if (isset ( $tab ["$lib"] ))
				$format [$lib] = $tab ["$lib"];
		}
	}
	function addLine($ligne, $tab) {
		global $format;

		$ordonnee = 10;
		$maxSize = $ligne;

		reset ( $this->colonnes );
		while ( list ( $lib, $pos ) = each ( $this->colonnes ) ) {
			$longCell = $pos - 2;
			$texte = $tab [$lib];

			if ($texte == null || $texte == "") {
				$texte = " ";
			}

			$length = $this->GetStringWidth ( $texte );
			$tailleTexte = $this->sizeOfText ( $texte, $length );
			$formText = $format [$lib];
			$this->SetXY ( $ordonnee, $ligne - 1 );
			$this->MultiCell ( $longCell, 4, $texte, 0, $formText );
			if ($maxSize < ($this->GetY ()))
				$maxSize = $this->GetY ();
			$ordonnee += $pos;
		}
		return ($maxSize - $ligne);
	}
	function __construct($orientation, $metric, $size) {
		parent::__construct ( $orientation, $metric, $size );

		require_once ('system-db.php');

		start_db ();
		initialise_db ();

		$this->B = 0;
		$this->I = 0;
		$this->U = 0;
		$this->HREF = '';
		$this->fontlist = array (
				'arial',
				'times',
				'courier',
				'helvetica',
				'symbol'
		);
		$this->issetfont = false;
		$this->issetcolor = false;
	}
}

?>