<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FIAE12H Stundenplan</title>
    <link href="stundenplan.css" rel="stylesheet" type="text/css">
    <link rel="apple-touch-icon-precomposed" href="apple-touch-icon.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="apple-touch-icon-72x72.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="apple-touch-icon-114x114.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-144x144.png"/>
    <meta name="viewport" content="width=device-width">
</head>
<body>
<?php
include_once('simple_html_dom.php');

function getFirstDayOfWeek($year, $weeknr)
{
    $offset = date('w', mktime(0, 0, 0, 1, 1, $year));
    $offset = ($offset < 5) ? 1 - $offset : 8 - $offset;
    $monday = mktime(0, 0, 0, 1, 1 + $offset, $year);

    return date('d.m.', strtotime('+' . ($weeknr - 1) . ' weeks', $monday));
}

function getRows($table)
{
    $rows = array();
    foreach ($table->find('tr') as $tr) {
        $colums = array();
        foreach ($tr->find('td') as $td) {
            $colums[] = utf8_encode($td->innertext());
        }
        if (count($colums) != 0) {
            $rows[] = $colums;
        }
    }

    return $rows;
}

function printVertetDetailsTable($dayRows)
{
    foreach ($dayRows as $row) {
        if (!empty($row[3])) {
            echo "<h3>$row[3] Stunde: <strong>$row[1]</strong></h3>";
        }

        $fach = $row[5] != '&nbsp;' && $row[4] != '&nbsp;' ? '<th>Fach</th>' : '';
        $lehrer = $row[8] != '&nbsp;' && $row[6] != '&nbsp;' ? '<th>Lehrer</th>' : '';
        $raum = $row[9] != '&nbsp;' && $row[7] != '&nbsp;' ? '<th>Raum</th>' : '';
        $text = $row[10] != '&nbsp;' ? '<th>Text</th>' : '';
        echo '<table class="vertDetails"><thead><tr>' . $fach . '' . $lehrer . '' . $raum . '' . $text . '</tr></thead><tbody><tr>';
        if ($row[5] != '&nbsp;' && $row[4] != '&nbsp;') {
            echo "<td>$row[5] ➙ $row[4]</td>";
        }
        if ($row[8] != '&nbsp;' && $row[6] != '&nbsp;') {
            echo "<td>$row[8] ➙ $row[6]</td>";
        }
        if ($row[9] != '&nbsp;' && $row[7] != '&nbsp;') {
            echo "<td>$row[9] ➙ $row[7]</td>";
        }
        if ($row[10] != '&nbsp;') {
            echo "<td>$row[10]</td>";
        }
        echo '</tr></tbody></table>';
    }
}

date_default_timezone_set('Europe/Berlin');
$week = date('W');

if (date('w') == 0 || date('w') == 6) {
    $week++;
}

// add leading zero
if (substr($week, 0, 1) != '0' && $week < 10) {
    $week = '0' . $week;
}

// get HTML-Page
$vertretungsplan = @file_get_html('http://stundenplan.mmbbs.de/plan1011/ver_kla/' . $week . '/c/c00048.htm');
$vertretungsplanDetails = @file_get_html('http://stundenplan.mmbbs.de/plan1011/ver_kla/' . $week . '/w/w00048.htm');
if ($vertretungsplan === false) {
    $week++;
    $vertretungsplan = @file_get_html('http://stundenplan.mmbbs.de/plan1011/ver_kla/' . $week . '/c/c00048.htm');
    $vertretungsplanDetails = @file_get_html('http://stundenplan.mmbbs.de/plan1011/ver_kla/' . $week . '/w/w00048.htm');
}

if ($vertretungsplan === false) {
    echo '<div><h1>Wir haben keine Schule! :)</h1></div>';
} else {
    $stundenplan = @file_get_html('http://stundenplan.mmbbs.de/plan1011/klassen/' . $week . '/c/c00044.htm');

    $weekStart = getFirstDayOfWeek(date('o'), $week);


    $stundenplanTable = $stundenplan->find('table', 0);
    $vertretungsplanTable = $vertretungsplan->find('table', 0);

    $vertretungsplanDetailsMonday = $vertretungsplanDetails->find('table.subst', 0);
    $vertretMondayRows = getRows($vertretungsplanDetailsMonday);

    $vertretungsplanDetailsTuesday = $vertretungsplanDetails->find('table.subst', 1);
    $vertretTuesdayRows = getRows($vertretungsplanDetailsTuesday);

    $vertretungsplanDetailsWednesday = $vertretungsplanDetails->find('table.subst', 2);
    $vertretWednesdayRows = getRows($vertretungsplanDetailsWednesday);

    $vertretungsplanDetailsThursday = $vertretungsplanDetails->find('table.subst', 3);
    $vertretThursdayRows = getRows($vertretungsplanDetailsThursday);

    $vertretungsplanDetailsFriday = $vertretungsplanDetails->find('table.subst', 4);
    $vertretFridayRows = getRows($vertretungsplanDetailsFriday);

    $vertretungsplanDetailsSaturday = $vertretungsplanDetails->find('table.subst', 5);
    $vertretSaturdayRows = getRows($vertretungsplanDetailsSaturday);

    echo '<div id="vertretungsplan">';
    echo '<h1>Vertretungsplan für die Woche vom ' . $weekStart . '</h1><hr>';
    echo '<div id="vertretungsplan_div">';
    echo $vertretungsplanTable;
    echo '</div><a href="http://stundenplan.mmbbs.de/plan1011/ver_kla/' . $week . '/c/c00048.htm">Komplett anzeigen</a>';
    echo "</div>\n<br>\n";

    echo '<div id="vertretungsplanDetails">';
    echo '<h1>Vertretungsplan Details für die Woche vom ' . $weekStart . '</h1><hr>';
    echo '<div id="vertretungsplanDetails_div">';

    if (count($vertretMondayRows[0]) > 1) {
        echo "<h2>Montag</h2>";
        printVertetDetailsTable($vertretMondayRows);
    }
    echo "\n\n";

    if (count($vertretTuesdayRows[0]) > 1) {
        echo '<h2>Dienstag</h2>';
        printVertetDetailsTable($vertretTuesdayRows);
    }
    echo "\n\n";

    if (count($vertretWednesdayRows[0]) > 1) {
        echo '<h2>Mittwoch</h2>';
        printVertetDetailsTable($vertretWednesdayRows);
    }
    echo "\n\n";

    if (count($vertretThursdayRows[0]) > 1) {
        echo '<h2>Donnerstag</h2>';
        printVertetDetailsTable($vertretThursdayRows);
    }
    echo "\n\n";

    if (count($vertretFridayRows[0]) > 1) {
        echo '<h2>Freitag</h2>';
        printVertetDetailsTable($vertretFridayRows);
    }
    echo "\n\n";

    if (count($vertretSaturdayFields) > 1) {
        echo '<h2>Samstag</h2>';
        printVertetDetailsTable($vertretSaturdayRows);
    }
    echo "\n\n";

    echo "</div></div><br>\n\n";

    echo '<details id="stundenplanCollapsable"><summary>Stundenplan einblenden</summary>';
    echo '<div id="stundenplan">';
    echo '<h1>Stundenplan für die Woche vom ' . $weekStart . '</h1><hr>';
    echo '<div id="stundenplan_div">';
    echo $stundenplanTable;
    echo '</div><a href="http://stundenplan.mmbbs.de/plan1011/klassen/' . $week . '/c/c00044.htm">Komplett anzeigen</a></div></details>';

}


?>

</body>
</html>