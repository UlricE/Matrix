<?php

/* Common code for all years and all populations. */

require(dirname(__FILE__) . '/smf/SSI.php');

function small_header($title, $id)
{
	echo "<html>\n";
	echo "<head>\n";
//	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
	echo "<title>" . $title . "</title>\n";
//	echo "<LINK href=\"main-filer/gnestasfktext.css\" type=text/css rel=STYLESHEET>\n";
	echo "<STYLE type=text/css>A {\n";
	echo "	FONT-WEIGHT: bold; FONT-SIZE: 10pt; COLOR: #000000; TEXT-DECORATION: underline\n";
	echo "}\n";
	echo "A:hover {\n";
	echo "	COLOR: #FF6633\n";
	echo "}\n";
	echo ".ulink {\n";
	echo "	FONT-SIZE: 10pt\n";
	echo "}\n";
	echo ".biglink {\n";
	echo "	FONT-SIZE: 16pt; COLOR: #336699; FONT-FAMILY: verdana, times new roman, sans serif\n";
	echo "}\n";
	echo ".mellanlink {\n";
	echo "	FONT-SIZE: 14pt; COLOR: #336699; FONT-FAMILY: verdana, times new roman, sans serif\n";
	echo "}\n";
	echo "</STYLE>\n";
	echo "</head>\n";
	echo "<BODY bgColor=#FFCC66>\n";
	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"90%\" align=\"center\">\n";
	echo "<td>\n";
	echo "<font face=\"verdana, times new roman, sans, serif\" size=2>\n";
}

function my_header($title, $id)
{
	echo "<html>\n";
	echo "<head>\n";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
	echo "<title>" . $title . "</title>\n";
//	echo "<LINK href=\"main-filer/gnestasfktext.css\" type=text/css rel=STYLESHEET>\n";
	echo "<STYLE type=text/css>A {\n";
	echo "	FONT-WEIGHT: bold; FONT-SIZE: 10pt; COLOR: #000000; TEXT-DECORATION: underline\n";
	echo "}\n";
	echo "A:hover {\n";
	echo "	COLOR: #FF6633\n";
	echo "}\n";
	echo ".ulink {\n";
	echo "	FONT-SIZE: 10pt\n";
	echo "}\n";
	echo ".biglink {\n";
	echo "	FONT-SIZE: 16pt; COLOR: #336699; FONT-FAMILY: verdana, times new roman, sans serif\n";
	echo "}\n";
	echo ".mellanlink {\n";
	echo "	FONT-SIZE: 14pt; COLOR: #336699; FONT-FAMILY: verdana, times new roman, sans serif\n";
	echo "}\n";
	echo "</STYLE>\n";

//	echo "<style type=\"text/css\">\n";
//	echo "table { background: #000000 }\n";
//	echo "td { background: #FFFFFF }\n";
//	echo "th { background: #EEEEEE }\n";
//	echo "</style>\n";
	echo "<script type=\"text/javascript\" src=\"sorttable.js\"></script>\n";
	echo "<script type=\"text/javascript\">\n<!--\n";
	echo "function my_popup(mode) {\n";
	echo "  window.open(\"?mode=\" + mode, \"djurlista\", \"width=300,height=400,top=100,left=300,toolbar=0,menubar=0,location=0,status=0,scrollbars=1\");\n";
	echo "}\n";
	echo "function popup_url(url) {\n";
	echo "  window.open(url,'name','height=200,width=150');
	echo }\n";
	echo "// -->\n</script>\n";
	echo "</head>\n";
	echo "<BODY bgColor=#FFCC66>\n";
	echo "<br><br>\n";
	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"90%\" align=\"center\">\n";
	echo "<tr>\n";
	echo "<td bgcolor=\"#FF6633\">\n";
	echo "<font face=\"verdana, times new roman, sans, serif\" size=\"2\">\n";
	echo "<b>&shy;", $title, "</b>\n";
	echo "</font>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<td><br>\n";
	echo "<font face=\"verdana, times new roman, sans, serif\" size=2>\n";
	standard_links($id);
}

function footer()
{
	echo "</font>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<p>\n";
	echo "</body>\n";
	echo "</html>\n";
}

function error_page($msg)
{
	my_header("Fel", 0);
	echo "Fel: " . $msg . "<p>\n";
	echo "<a href=\"?mode=list\">Till listan</a><p>\n";
	footer();
	exit();
}

function list_row($id, $nummer, $kon, $fodd, $farg, $namn, $offspring, $mor, $far, $ic, $mk)
{
	echo "<tr>\n";
	if (!$nummer) $nummer = "Saknas";
	echo "<td bgcolor=#FFFFFF><a name=\"", $id, "\"></a><a href=\"?mode=pedigree&id=", $id, "\">", $nummer, "</a></td>\n";
	echo "<td bgcolor=#FFFFFF>", $kon, "</td>\n";
	echo "<td bgcolor=#FFFFFF>", $fodd, "</td>\n";
	//echo "<td bgcolor=#FFFFFF>", $farg, "</td>\n";
	echo "<td bgcolor=#FFFFFF>", $namn, "</td>\n";
	if ($offspring > 0) {
		echo "<td bgcolor=#FFFFFF><a href=\"?mode=offspring&id=", $id, "\">", $offspring, "</td>\n";
	} else {
		echo "<td bgcolor=#FFFFFF>", $offspring, "</td>\n";
	}
	echo "<td bgcolor=#FFFFFF>", $mor, "</td>\n";
	echo "<td bgcolor=#FFFFFF>", $far, "</td>\n";
	echo "<td bgcolor=#FFFFFF>", round(100*($ic-1), 2), "%</td>\n";
	if ($mk) {
		echo "<td bgcolor=#FFFFFF>", round(100*$mk, 2), "%</td>\n";
	} else {
		echo "<td bgcolor=#FFFFFF>-</td>\n";
	}
	echo "</tr>\n";
}

function page_founders()
{
	global $table_prefix;

	my_header("Founderrepresentation i genbanken", 0);
	echo "<table bgcolor=#000000 class=sortable>\n";
	echo "<tr><th bgcolor=#EEEEEE>Nummer</th>";
	echo "<th bgcolor=#EEEEEE>Namn</th>";
	echo "<th bgcolor=#EEEEEE>Född</th>";
	echo "<th bgcolor=#EEEEEE>Andel</th>";
	echo "<th bgcolor=#EEEEEE>Ättlingar</th>";
	echo "</tr>";
	$result = mysql_query("select r.nummer nummer, r.namn namn, r.fodd fodd, sum(f.factor) andel, count(*) attlingar, r.id id
				from ".$table_prefix."_founders f
				join ".$table_prefix."_register r
				on f.founder = r.id
				group by f.founder
				order by r.nummer, r.namn");
	if (!$result) die(mysql_error());
	while ($r = mysql_fetch_object($result)) {
		echo "<tr>";
		echo "<td bgcolor=#FFFFFF>", $r->nummer, "</td>\n";
		echo "<td bgcolor=#FFFFFF><a href=\"?mode=list_all#", $r->id, "\">", $r->namn, "</td>\n";
		echo "<td bgcolor=#FFFFFF>", $r->fodd, "</td>\n";
		echo "<td bgcolor=#FFFFFF>", round($r->andel, 4), "</td>\n";
		// echo "<td bgcolor=#FFFFFF><a href=\"?mode=founder_representation&founder=", $r->id, "\">", round($r->andel, 4), "</td>\n";
		echo "<td bgcolor=#FFFFFF><a href=\"?mode=founder_representation&founder=", $r->id, "\">", $r->attlingar, "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n<p>\n";

	footer();
}

function page_founder_representation()
{
	global $table_prefix;
	global $last_year;
	$founder = $_GET['founder'];
	if (!$founder) die("Inget founderid");
	$row = db_get("select nummer, namn from ".$table_prefix."_register where id = " . $founder);
	my_header("Representation i dagens genbank för founder " . $row[0] . " " . $row[1], $founder);

	echo "<table bgcolor=#000000 class=sortable>\n";
	echo "<tr><th bgcolor=#EEEEEE>Nummer</th>";
	echo "<th bgcolor=#EEEEEE>Namn</th>";
	echo "<th bgcolor=#EEEEEE>Född</th>";
	echo "<th bgcolor=#EEEEEE>Genbank</th>";
	echo "<th bgcolor=#EEEEEE>Andel</th></tr>";
	$result = mysql_query("select r.nummer, r.namn, r.fodd, r.g".$last_year." genbank, f.factor andel, r.id id
				from ".$table_prefix."_founders f
				join ".$table_prefix."_register r
				on f.id = r.id
				where f.founder = " . $founder .
				" order by r.nummer, r.namn");
	if (!$result) die(mysql_error());
	while ($r = mysql_fetch_object($result)) {
		echo "<tr>";
		echo "<td bgcolor=#FFFFFF>", $r->nummer, "</td>\n";
		echo "<td bgcolor=#FFFFFF><a href=\"?mode=list#", $r->id, "\">", $r->namn, "</td>\n";
		echo "<td bgcolor=#FFFFFF>", $r->fodd, "</td>\n";
		echo "<td bgcolor=#FFFFFF>", $r->genbank, "</td>\n";
		echo "<td bgcolor=#FFFFFF><a href=\"?mode=founder_shares&id=", $r->id, "\">", round($r->andel, 4), "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n<p>\n";

	footer();
}

function page_founder_shares()
{
	global $table_prefix;
	$id = $_GET['id'];
	if (!$id) die("Inget id");
	$row = db_get("select nummer, namn from ".$table_prefix."_register where id = " . $id);
	my_header("Founderandelar i " . $row[0] . " " . $row[1], $id);

	echo "<table bgcolor=#000000 class=sortable>\n";
	echo "<tr><th bgcolor=#EEEEEE>Nummer</th>";
	echo "<th bgcolor=#EEEEEE>Namn</th>";
	echo "<th bgcolor=#EEEEEE>Född</th>";
	echo "<th bgcolor=#EEEEEE>Andel</th></tr>";
	$result = mysql_query("select r.nummer, r.namn, r.fodd, f.factor andel, f.founder founder
				from ".$table_prefix."_founders f
				join ".$table_prefix."_register r
				on f.founder = r.id
				where f.id = " . $id .
				" order by r.nummer, r.namn");
	if (!$result) die(mysql_error());
	while ($r = mysql_fetch_object($result)) {
		echo "<tr>";
		echo "<td bgcolor=#FFFFFF>", $r->nummer, "</td>\n";
		echo "<td bgcolor=#FFFFFF><a href=\"?mode=list_all#", $r->founder, "\">", $r->namn, "</td>\n";
		echo "<td bgcolor=#FFFFFF>", $r->fodd, "</td>\n";
		echo "<td bgcolor=#FFFFFF>", round($r->andel, 4), "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n<p>\n";

	footer();
}

function page_mk()
{
	global $table_prefix;
	global $this_year;
	my_header("Listan", 0);

	echo "<i>Alla individer i den senaste årsrapporten samt eventuellt tillkommande under $this_year listade efter mean kinship (genomsnittligt släktskap).\n";
	echo "Individer med lågt MK är genetiskt värdefulla och bör användas i avelsarbetet.\n";
	echo "Djuren med högst MK bör undanhållas avel.</i><p>\n";

	echo "<table bgcolor=#000000 class=sortable>\n";
	echo "<tr><th bgcolor=#EEEEEE>Plats</th>";
	echo "<th bgcolor=#EEEEEE>Regnr</th>";
	echo "<th bgcolor=#EEEEEE>Namn</th>";
	echo "<th bgcolor=#EEEEEE>Kön</th>";
	echo "<th bgcolor=#EEEEEE>MK</th>";
	echo "<th bgcolor=#EEEEEE>F</th>";
	echo "</tr>\n";
	$result = mysql_query("select id, nummer, namn, kon, ic, mk".$this_year." mk from ".$table_prefix."_register where mk".$this_year." is not null order by mk");
	if (!$result) die(mysql_error());
	$i = 1;
	while ($row = mysql_fetch_object($result)) {
		echo "<tr>\n";
		echo "<td bgcolor=#FFFFFF>", $i++, "</td>\n";
		echo "<td bgcolor=#FFFFFF><a href=\"?mode=list#", $row->id, "\">", $row->nummer, "</td>\n";
		echo "<td bgcolor=#FFFFFF>", $row->namn, "</td>\n";
		echo "<td bgcolor=#FFFFFF>", $row->kon, "</td>\n";
		echo "<td bgcolor=#FFFFFF>", round(100*$row->mk, 2), "%</td>\n";
		echo "<td bgcolor=#FFFFFF>", round(100*($row->ic-1), 2), "%</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n<p>\n";

	footer();
}

function make_list($condition, $order)
{
	global $table_prefix;
	global $this_year;
	echo "<table bgcolor=#000000 class=sortable>\n";
	echo "<tr><th bgcolor=#EEEEEE>Regnr</th>";
	echo "<th bgcolor=#EEEEEE>Kön</th>";
	echo "<th bgcolor=#EEEEEE>Född</th>";
	//echo "<th bgcolor=#EEEEEE>Färg och kännetecken</th>";
	echo "<th bgcolor=#EEEEEE>Namn</th>";
	echo "<th bgcolor=#EEEEEE>Avkomma</th>";
	echo "<th bgcolor=#EEEEEE>Mor</th>";
	echo "<th bgcolor=#EEEEEE>Far</th>";
	echo "<th bgcolor=#EEEEEE>Inavel</th>";
	echo "<th bgcolor=#EEEEEE>Släktskap</th>";
	echo "</tr>\n";

	//$q = $query;
	$q = "select id, nummer, kon, date_format(fodd, '%Y-%m-%d') fodd, farg, namn, offspring, mor, far, ic, mk".$this_year." mk from ".$table_prefix."_register";
	if ($condition) $q = $q . " where " . $condition;
	if ($order) $q = $q . " order by " . $order;
	echo "<!-- $q -->\n";
	$result = mysql_query($q);
	if (!$result) die(mysql_error());
	while ($row = mysql_fetch_object($result)) {
		list_row($row->id, $row->nummer, $row->kon,
			$row->fodd, $row->farg, $row->namn, $row->offspring, $row->mor, $row->far,
			$row->ic, $row->mk);
	}
	echo "</table>\n<p>\n";
}

function page_list()
{
	global $this_year;
	my_header("Register", 0);
	make_list("mk".$this_year." is not null", "nummer");

	footer();
}

function page_list_all()
{
	my_header("Register", 0);
	make_list(null, "nummer");

	footer();
}

function page_offspring()
{
	global $table_prefix;
	$id = $_GET['id'];
	if (!$id) die("Inget id");
	$row = db_get("select nummer, namn from ".$table_prefix."_register where id = " . $id);

	my_header("Avkomma efter " . $row[0] . " " . $row[1], $id);
	make_list("far_id = " . $id . " or mor_id = " . $id, null);
	footer();
}

function page_fathers()
{
	global $table_prefix;
	global $this_year;
	small_header("Välj genom att klicka", 0);
	echo "<table width=100%>\n";

	$result = mysql_query("select nummer, namn from ".$table_prefix."_register where kon='hane' and mk".$this_year." is not null order by nummer");
	if (!$result) die($mysql_error());
	while ($row = mysql_fetch_object($result)) {
		fathers($row->nummer, $row->namn);
	}
	echo "</table>\n";
	echo "<script type=\"text/javascript\">\n<!--\n";
	echo "-->\n</script>\n";
	footer();
}

function fathers($nummer, $namn)
{
	echo "<tr>";
	echo "<td><a href=\"#\" onClick=\"opener.document.details.far_nr.value='", $nummer, "'; window.close(); return false;\">", $nummer, "</a></td>";
	echo "<td>", $namn, "</td>";
	echo "</tr>\n";
}

function page_mothers()
{
	global $table_prefix;
	global $this_year;
	small_header("Välj genom att klicka", 0);
	echo "<table width=100%>\n";

	$result = mysql_query("select nummer, namn from ".$table_prefix."_register where kon='hona' and mk".$this_year." != '' order by nummer");
	if (!$result) die($mysql_error());
	while ($row = mysql_fetch_object($result)) {
		mothers($row->nummer, $row->namn);
	}
	echo "</table>\n";
	echo "<script type=\"text/javascript\">\n<!--\n";
	echo "-->\n</script>\n";
	footer();
}

function mothers($nummer, $namn)
{
	echo "<tr>";
	echo "<td><a href=\"#\" onClick=\"opener.document.details.mor_nr.value='", $nummer, "'; window.close(); return false;\">", $nummer, "</a></td>";
	echo "<td>", $namn, "</td>";
	echo "</tr>\n";
}

function db_get($query)
{
	// echo "<!-- ", $query, "-->\n";
	$result = mysql_query($query);
	if (!$result) die(mysql_error());
	$row = mysql_fetch_row($result);
	if (!$result) die(mysql_error());
	return $row;
}

function make_link($url, $newmode, $name)
{
	global $mode;

	if ($mode == $newmode) {
		echo " $name |";
	} else {
		echo " <a href=\"", $url, "\">", $name, "</a> |";
	}
}

function standard_links($id)
{
	echo "|";

	make_link("?mode=list#" . $id, "list", "Aktuella djur");
	make_link("?mode=list_all#" . $id, "list_all", "Alla djur");
	make_link("?mode=statistics", "statistics", "Statistik");
	make_link("?mode=founders", "founders", "Founders");
	make_link("?mode=mating_form", "mating_form", "Provparning");
	make_link("?mode=mk", "mk", "Listan");
	echo "<p>\n";
}

function page_mating_form()
{
	my_header("Testparning", 0);
	echo "<form name=\"details\">\n";
	echo "<input type=hidden name=mode value=mating_pedigree>";
	echo "<table style=\"background: #FFFFFF\">\n";
	echo "<tr><td>Far</td><td><input type=text name=far_nr value=\"" . $far_nr . "\"><input type=\"button\" onClick=\"my_popup('fathers')\" value=\"...\"></td></tr>\n";
	echo "<tr><td>Mor</td><td><input type=text name=mor_nr value=\"" . $mor_nr . "\"><input type=\"button\" onClick=\"my_popup('mothers')\" value=\"...\"></td></tr>\n";
	echo "</table>";
	echo "<input type=submit value=Fortsätt>";
	echo "</form>\n";
	footer();
}

function pedicell($id)
{
	global $table_prefix;
	echo "<center>\n";
	if (!$id) {
		echo "&nbsp;<br>&nbsp;<br>&nbsp;";
	} else {
		$row = db_get("select namn, nummer, date_format(fodd, '%Y-%m-%d') fodd from ".$table_prefix."_register where id=" . $id);
		if (!row) {
			echo "&nbsp;<br>&nbsp;<br>&nbsp;";
		} else {
			echo $row[0], "<br><a href=\"?mode=pedigree&id=", $id, "\">", $row[1], "</a><br>", $row[2];
		}
	}
	echo "</center>\n";
}

function pedigree($id, $rowspan)
{
	global $table_prefix;
	if ($rowspan == 1) {
		echo "<td bgcolor=#FFFFFF>";
		pedicell($id);
		echo "</td></tr>\n";
	} else {
		if ($id) {
			$row = db_get("select far_id, mor_id from ".$table_prefix."_register where id =" . $id);
			$far = $row[0];
			$mor = $row[1];
		}
		echo "<td bgcolor=#FFFFFF rowspan=" . $rowspan . ">";
		pedicell($id);
		echo "</td>\n";
		pedigree($far, $rowspan/2);
		echo "<tr>";
		pedigree($mor, $rowspan/2);
	}
}

function lookup_kinship($mor_id, $far_id)
{
	global $table_prefix;
	if ($mor_id > $far_id) {
		$id1 = $far_id;
		$id2 = $mor_id;
	} else {
		$id1 = $mor_id;
		$id2 = $far_id;
	}
	$row = db_get("select kinship/2 from ".$table_prefix."_kinship where id1 = " . $id1 . " and id2 = " . $id2);
	if (!$row) return null;
	return $row[0];
}

function pdf_parents($pdf, $far_id, $mor_id)
{
	global $table_prefix;
	$pdf->SetFont('DejaVuB', '', 12);
	$pdf->Cell(pdf_cell_width, 8, "Föräldrar", 1, 0, "C", 1);
	$pdf->Cell(pdf_cell_width, 8, "G2", 1, 0, "C", 1);
	$pdf->Cell(pdf_cell_width, 8, "G3", 1, 0, "C", 1);
	$pdf->Cell(pdf_cell_width, 8, "G4", 1, 0, "C", 1);
	$pdf->Ln();
	$pdf->SetFont('DejaVu', '', 10);
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$height = 96;
	pdf_pedigree($pdf, $far_id, 8, $x, $y, $height);
	pdf_pedigree($pdf, $mor_id, 8, $x, $y+$height, $height);
}

function html_parents($far_id, $mor_id)
{
	echo "<table width=\"100%\">";
	echo "<tr>\n";
	echo "<th bgcolor=#EEEEEE>Föräldrar</th>\n";
	echo "<th bgcolor=#EEEEEE>G2</th>\n";
	echo "<th bgcolor=#EEEEEE>G3</th>\n";
	echo "<th bgcolor=#EEEEEE>G4</th>\n";
	echo "<th bgcolor=#EEEEEE>G5</th>\n";
	echo "</tr>\n";
	echo "<tr>";
	pedigree($far_id, 16);
	echo "<tr>";
	pedigree($mor_id, 16);
	echo "</table>";
}

function mating_parents()
{
	global $table_prefix;
	$mor_nr = $_GET['mor_nr'];
	$far_nr = $_GET['far_nr'];
	if (!$mor_nr) error_page("Inget mor_nr");
	if (!$far_nr) error_page("Inget far_nr");
	$row = db_get("select id, namn from ".$table_prefix."_register where nummer = '" . $far_nr . "' limit 1");
	if (!$row) error_page("Inget far_id");
	$far_id = $row[0];
	$far = $row[1];
	$row = db_get("select id, namn from ".$table_prefix."_register where nummer = '" . $mor_nr . "' limit 1");
	if (!$row) error_page("Inget mor_id");
	$mor_id = $row[0];
	$mor = $row[1];
	return array($mor_id, $mor_nr, $mor, $far_id, $far_nr, $far);
}

function page_mating_pdf()
{
	list($mor_id, $mor_nr, $mor, $far_id, $far_nr, $far) = mating_parents();

	$kinship = lookup_kinship($mor_id, $far_id);

	require('tfpdf/tfpdf.php');
	$pdf = new tFPDF();
	$pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
	$pdf->AddFont('DejaVuB','','DejaVuSans-Bold.ttf',true);
	$pdf->AddPage();
	$pdf->SetFillColor(238);
	$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	$pdf->SetFont('DejaVuB', '', 12);
	$pdf->Cell(4*pdf_cell_width, 8, "Provparning " . $far_nr . " " . $far . " x " . $mor_nr . " " . $mor, 1, 1, "C", 1);
	if ($kinship) {
		pdf_row($pdf, "Inavelskoefficient", round(100*$kinship, 2) . "%");
	}
	pdf_parents($pdf, $far_id, $mor_id);
	$pdf->Output();
}

function page_mating_pedigree()
{
	list($mor_id, $mor_nr, $mor, $far_id, $far_nr, $far) = mating_parents();

	$kinship = lookup_kinship($mor_id, $far_id);

	my_header("Provparning " . $far_nr . " " . $far . " x " . $mor_nr . " " . $mor, $id);
	echo "<table>\n";
	if ($kinship) {
		echo "<tr><td>Inavelskoefficient</td><td>", round(100*$kinship, 2), "%</td></tr>\n";
	}
	echo "<tr><td><a target=_blank href=\"?mode=mating_pdf&mor_nr=", $mor_nr, "&far_nr=", $far_nr, "\">PDF</a></td></tr>\n";
	echo "</table>\n";
	echo "<p>\n";
	html_parents($far_id, $mor_id);
	footer();
}

function page_pedigree()
{
	global $table_prefix;
	global $this_year;
	$id = $_GET['id'];
	if (!$id) error_page("Inget id");

	$row = db_get("select kon, far_id, mor_id, farg, date_format(fodd, '%Y-%m-%d') fodd, nummer, ic, mk".$this_year." mk, namn from ".$table_prefix."_register where id=" . $id);
	if (!$row) error_page("Information saknas om djuret");

	my_header("Stamtavla för " . $row[5] . " " . $row[8], $id);
	echo "<table>\n";
	echo "<tr><td>Namn</td><td>" . $row[8] . "</td></tr>\n";
	echo "<tr><td>Kön</td><td>" . $row[0] . "</td></tr>\n";
	echo "<tr><td>Färg</td><td>", $row[3], "</td></tr>\n";
	echo "<tr><td>Född</td><td>", $row[4], "</td></tr>\n";
	echo "<tr><td>Registreringsnummer</td><td>", $row[5], "</td></tr>\n";
	echo "<tr><td>Inavelskoefficient</td><td>", round(100*($row[6]-1), 2), "%</td></tr>\n";
	if ($row[7]) {
		echo "<tr><td>Genomsnittligt släktskap</td><td>", round(100*$row[7], 2), "%</td></tr>\n";
	}
	echo "<tr><td><a target=_blank href=\"?mode=pedigree_pdf&id=", $id, "\">PDF</a></td></tr>\n";
	echo "</table>\n";
	echo "<p>\n";
	html_parents($row[1], $row[2]);
	footer();
}

define('pdf_cell_width', 45);

function pdf_pedicell($pdf, $id, $x, $y, $height)
{
	global $table_prefix;
	$pdf->SetXY($x, $y);
	$pdf->Rect($x, $y, pdf_cell_width, $height);
	if (!$id) {
		// $pdf->Cell(pdf_cell_width, $height, " ", 1, 0, "C");
	} else {
		$row = db_get("select namn, nummer, date_format(fodd, '%Y-%m-%d') fodd from ".$table_prefix."_register where id=" . $id);
		if (!$row) {
			// $pdf->Cell(pdf_cell_width, $height, " ", 1, 0, "C");
		} else {
			$pdf->SetXY($x, $y+$height/2-6);
			$pdf->Cell(pdf_cell_width, 4, $row[0], 0, 2, "C");
			$pdf->Cell(pdf_cell_width, 4, $row[1], 0, 2, "C");
			$pdf->Cell(pdf_cell_width, 4, $row[2], 0, 2, "C");
		}
	}
}

function pdf_pedigree($pdf, $id, $rowspan, $x, $y, $height)
{
	global $table_prefix;
	if ($rowspan == 1) {
		pdf_pedicell($pdf, $id, $x, $y, $height);
	} else {
		if ($id) {
			$row = db_get("select far_id, mor_id from ".$table_prefix."_register where id =" . $id);
			$far = $row[0];
			$mor = $row[1];
		} else {
			$far = '';
			$mor = '';
		}
		pdf_pedicell($pdf, $id, $x, $y, $height);
		pdf_pedigree($pdf, $far, $rowspan/2, $x+pdf_cell_width, $y, $height/2);
		pdf_pedigree($pdf, $mor, $rowspan/2, $x+pdf_cell_width, $y+$height/2, $height/2);
	}
}

function pdf_row($pdf, $d1, $d2)
{
	$pdf->SetFont('DejaVuB', '', 10);
	$pdf->Cell(60, 6, $d1, 0, 0);
	$pdf->SetFont('DejaVu', '', 10);
	$pdf->Cell(60, 6, $d2, 0, 1);
}

function page_pedigree_pdf()
{
	global $table_prefix;
	global $this_year;
	$id = $_GET['id'];
	if (!$id) error_page("Inget id");

	$row = db_get("select kon, far_id, mor_id, farg, date_format(fodd, '%Y-%m-%d') fodd, nummer, ic, mk".$this_year." mk, namn from ".$table_prefix."_register where id=" . $id);
	if (!$row) error_page("Information saknas om djuret");

	require('tfpdf/tfpdf.php');
	$pdf = new tFPDF();
	$pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
	$pdf->AddFont('DejaVuB','','DejaVuSans-Bold.ttf',true);
	$pdf->AddPage();
	$pdf->SetFillColor(238);
	$pdf->SetFont('DejaVuB', '', 12);
	$pdf->Cell(4*pdf_cell_width, 8, "Stamtavla för " . $row[5] . " " . $row[8], 1, 1, "C", 1);
	pdf_row($pdf, "Namn", $row[8]);
	pdf_row($pdf, "Kön", $row[0]);
	pdf_row($pdf, "Färg", $row[3]);
	pdf_row($pdf, "Registreringsnummer", $row[5]);
	pdf_row($pdf, "Inavelskoefficient", round(100*($row[6]-1), 2) . "%");
	if ($row[7]) {
		pdf_row($pdf, "Genomsnittligt släktskap", round(100*$row[7], 2) . "%");
	}
	pdf_parents($pdf, $row[1], $row[2]);
	$pdf->Output();
}

$union = "";
$djur_i_genbank = "";
$hannar_i_genbank = "";
$honor_i_genbank = "";
$antal_genbanker = "";
$min_storlek_genbank = "";
$avg_storlek_genbank = "";
$max_storlek_genbank = "";

for ($y = $first_year; $y <= $last_year; $y++) {
	$djur_i_genbank = $djur_i_genbank.
		$union.
		"select ".$y." År, count(*) Antal
		from ".$table_prefix."_register
		where g".$y." != ''";
	$hannar_i_genbank = $hannar_i_genbank.
		$union.
		"select ".$y." År, count(*) Antal
		from ".$table_prefix."_register
		where kon = 'hane'
		and g".$y." != ''";
	$honor_i_genbank = $honor_i_genbank.
		$union.
		"select ".$y." År, count(*) Antal
		from ".$table_prefix."_register
		where kon = 'hona'
		and g".$y." != ''";
	$antal_genbanker = $antal_genbanker.
		$union.
		"select ".$y." År, count(distinct g".$y.") Antal
		from ".$table_prefix."_register
		where g".$y." != ''";
	$min_storlek_genbank = $min_storlek_genbank.
		$union.
		"select ".$y." År, min(n) Antal
		from (
			select count(*) n
			from ".$table_prefix."_register
			where g".$y." != ''
			group by g".$y.") x";
	$avg_storlek_genbank = $avg_storlek_genbank.
		$union.
		"select ".$y." År, avg(n) Antal
		from (
			select count(*) n
			from ".$table_prefix."_register
			where g".$y." != ''
			group by g".$y.") x";
	$max_storlek_genbank = $max_storlek_genbank.
		$union.
		"select ".$y." År, max(n) Antal
		from (
			select count(*) n
			from ".$table_prefix."_register
			where g".$y." != ''
			group by g".$y.") x";

	$union = "
		union
";
}

$hannar_per_arskull = "select year(fodd) År, count(*) Antal
		from ".$table_prefix."_register
		where kon = 'hane'
		group by År
		order by År";
$honor_per_arskull = "select year(fodd) År, count(*) Antal
		from ".$table_prefix."_register
		where kon = 'hona'
		group by År
		order by År";
$djur_per_arskull = "select year(fodd) År, count(*) Antal
		from ".$table_prefix."_register
		group by År
		order by År";
$avg_ic_per_ar = "select year(fodd) År, round(100*(avg(ic)-1),2) Inavelskoefficient
		from ".$table_prefix."_register
		group by År
		order by År";
$max_ic_per_ar = "select year(fodd) År, round(100*(max(ic)-1),2) Inavelskoefficient
		from ".$table_prefix."_register
		group by År
		order by År";
$avg_avkomma_per_ar = "select year(fodd) År, avg(offspring) Avkomma
		from ".$table_prefix."_register
		group by År
		order by År";
$max_avkomma_per_ar = "select year(fodd) År, max(offspring) Avkomma
		from ".$table_prefix."_register
		group by År
		order by År";
$avg_mk_per_ar = "select year År, round(100*amk, 2) Släktskap
		from ".$table_prefix."_average_mk
		order by År";

$stats = array(1 => array("Antal hannar per årskull", $hannar_per_arskull),
		array("Antal honor per årskull", $honor_per_arskull),
		array("Antal djur per årskull", $djur_per_arskull),
		array("Genomsnittlig inavelskoefficient år för år", $avg_ic_per_ar),
		array("Max inavelskoefficient år för år", $max_ic_per_ar),
		array("Genomsnittligt antal avkommor år för år", $avg_avkomma_per_ar),
		array("Max antal avkommor år för år", $max_avkomma_per_ar),
		array("Genomsnittligt medelsläktskap år för år", $avg_mk_per_ar),
		array("Antal djur i genbank", $djur_i_genbank),
		array("Antal hannar i genbank", $hannar_i_genbank),
		array("Antal honor i genbank", $honor_i_genbank),
		array("Antal genbanker", $antal_genbanker),
		array("Minsta besättningsstorlek", $min_storlek_genbank),
		array("Genomsnittlig besättningsstorlek", $avg_storlek_genbank),
		array("Största besättningsstorlek", $max_storlek_genbank)
);

// Make chart for statistics page
// Column 1 will be used for the chart
function page_chart()
{
	include "libchart/classes/libchart.php";
	global $stats;
	$stat = $_GET['stat'];
	if (!$stat) die("No chart");
	$result = mysql_query($stats[$stat][1]);
	if (!$result) die(mysql_error());
	$chart = new LineChart(500, 250);
	$data = new XYDataSet();
	while ($row = mysql_fetch_row($result)) {
		$data->addPoint(new Point($row[0], $row[1]));
	}
	$chart->setDataSet($data);
	$chart->setTitle($stats[$stat][0]);
	header("Content-type: image/png");
	$chart->render();
}

function page_statistics()
{
	my_header("Statistik", 0);
	global $stats;

	$stat=$_GET['stat'];

	echo "<form action=\"../\">\n";
//	echo "<select name=stat onChange=\"window.open(this.options[this.selectedIndex].value, 'main')\">\n";
	echo "<select name=stat onChange=\"window.open(this.options[this.selectedIndex].value, '_self')\">\n";
	echo "<option value=\"?mode=statistics\">Välj statistik</option>\n";
	foreach ($stats as $key => $value) {
		if ($stat && ($stat == $key)) {
			echo "<option selected value=\"?mode=statistics&stat=", $key, "\">", $value[0], "</option>\n";
		} else {
			echo "<option value=\"?mode=statistics&stat=", $key, "\">", $value[0], "</option>\n";
		}
	}
	echo "</select>\n";
	echo "</form>\n";
	if ($stat) {
		echo "<table bgcolor=#000000 class=sortable>\n";
/*
		echo "<!--\n";
		echo $stats[$stat][1];
		echo " -->\n";
*/
		$result = mysql_query($stats[$stat][1]);
		if (!$result) die(mysql_error());
		$fields = mysql_num_fields($result);
		echo "<tr>";
		for ($i = 0; $i < $fields; $i++) {
			echo "<th bgcolor=#EEEEEE>", mysql_field_name($result, $i), "</th>";
		}
		echo "</tr>\n";
		while ($row = mysql_fetch_row($result)) {
			echo "<tr>\n";
			for ($i = 0; $i < $fields; $i++) {
				echo "<td bgcolor=#FFFFFF>", $row[$i], "</td>\n";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "<p>\n";
		echo "<img src=\"?mode=chart&stat=", $stat, "\">\n";
	}
	echo "<p>\n";
	footer();
}

?>
