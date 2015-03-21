<?php

$table_prefix = '@TABLE_PREFIX@';
$first_year = @FIRST_YEAR@;
$last_year = @LAST_YEAR@;
$this_year = @THIS_YEAR@;

require(dirname(__FILE__) . '/register_lib.php');

// Use smf's login to grant access to the register
if ($context['user']['is_logged']) {
	$mode = $_GET['mode'];
	if (!$mode) $mode = "list";

	//$link = mysql_connect("localhost", "titta", "flukt4re");
	$link = mysql_connect("localhost", "gotlandskaninen", "kanjaglaga");
	if (!$link) die(mysql_error());
	//if (!mysql_select_db("kaniner")) die(mysql_error());
	if (!mysql_select_db("gotlandskaninen")) die(mysql_error());
	mysql_set_charset('utf8', $link);

	if ($mode == "mating_form") {
		page_mating_form();
	} else if ($mode == "mating_pedigree") {
		page_mating_pedigree();
	} else if ($mode == "mating_pdf") {
		page_mating_pdf();
	} else if ($mode == "pedigree") {
		page_pedigree();
	} else if ($mode == "pedigree_pdf") {
		page_pedigree_pdf();
	} else if ($mode == "inco") {
		page_inco();
	} else if ($mode == "mothers") {
		page_mothers();
	} else if ($mode == "fathers") {
		page_fathers();
	} else if ($mode == "offspring") {
		page_offspring();
	} else if ($mode == "statistics") {
		page_statistics();
	} else if ($mode == "chart") {
		page_chart();
	} else if ($mode == "list_all") {
		page_list_all();
	} else if ($mode == "founders") {
		page_founders();
	} else if ($mode == "founder_representation") {
		page_founder_representation();
	} else if ($mode == "founder_shares") {
		page_founder_shares();
	} else if ($mode == "mk") {
		page_mk();
	} else {
		page_list();
	}

	mysql_close($link);
	
} else {
	my_header("Logga in i forumet för att få tillgång till registret.", 0);
//	ssi_welcome();
	echo "Logga in för att få tillgång till registret.";
	ssi_login();
	footer();
}

?>
