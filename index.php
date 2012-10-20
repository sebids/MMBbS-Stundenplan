<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>FIAE12H Stundenplan</title>
	<link href="stundenplan.css" rel="stylesheet" type="text/css">
	<link rel="apple-touch-icon-precomposed" href="apple-touch-icon.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="apple-touch-icon-114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-144x144.png" />
	<meta name="viewport" content="width=device-width">
</head>
<body>
<?php
include_once('simple_html_dom.php');

function getFirstDayOfWeek($year, $weeknr)
{
	$offset = date('w', mktime(0,0,0,1,1,$year));
	$offset = ($offset < 5) ? 1 - $offset : 8 - $offset;
	$monday = mktime(0,0,0,1,1 + $offset, $year);

	return date('d.m.', strtotime('+'. ($weeknr - 1) . ' weeks', $monday));
}

date_default_timezone_set('Europe/Berlin');
$week = date('W');

if(date('w') == 0 || date('w') == 6) {
	$week++;
}


// get HTML-Page
$vertretungsplan = @file_get_html('http://stundenplan.mmbbs.de/plan1011/ver_kla/' . $week . '/c/c00048.htm');
if($vertretungsplan === false) {
	$week++;
	$vertretungsplan = @file_get_html('http://stundenplan.mmbbs.de/plan1011/ver_kla/' . $week . '/c/c00048.htm');
}

if($vertretungsplan === false) {
	echo '<div><h1>Wir haben keine Schule! :)</h1></div>';	
}
else {
$stundenplan = @file_get_html('http://stundenplan.mmbbs.de/plan1011/klassen/' . $week . '/c/c00044.htm');

$weekStart = getFirstDayOfWeek(date('o'), $week);



$stundenplanTable = $stundenplan->find('table', 0);
$vertretungsplanTable = $vertretungsplan->find('table', 0);

echo '<div id="vertretungsplan">';
echo '<h1>Vertretungsplan für die Woche vom ' . $weekStart . '</h1><hr>';
echo '<div id="vertretungsplan_div">';
echo $vertretungsplanTable;
echo '</div><a href="http://stundenplan.mmbbs.de/plan1011/ver_kla/' . $week . '/c/c00048.htm">Komplett anzeigen</a>';
echo "</div>\n\n";

echo '<div id="stundenplan">';
echo '<h1>Stundenplan für die Woche vom ' . $weekStart . '</h1><hr>';
echo '<div id="stundenplan_div">';
echo $stundenplanTable;
echo '</div><a href="http://stundenplan.mmbbs.de/plan1011/klassen/' . $week . '/c/c00044.htm">Komplett anzeigen</a></div>';
}


?>

</body>
</html>