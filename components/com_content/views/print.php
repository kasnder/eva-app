<?php
/**
 * Print view.
 *
 * This renders what appears on the computer terminals in the entrance, etc..
 * and the print version.
 *
 * By now it is based on the print view to provide basic funtionality.
 *
 * There are some styles and some scripts to make it look and feel better.
 * - Design by Clement Mensah (not yet)
 * - Autoscroll Script by Konrad Kollnig
 *
 * @package    Ops
 * @subpackage Ops_com_content
 * @author     Konrad Kollnig <team@otto-pankok-schule.de>
 * @author     Clement Mensah
 * @copyright  Copyright (C) 2013 Konrad Kollnig
 * @todo       Add display mode
 */

defined('DIR') or die;

/* TODO save token in a session // encrypt URL */
?>
    <!DOCTYPE HTML>
    <html>
    <head>
        <title><?php Common::secureEcho(Config::TITLE_SHORT); ?>-Vertretungsplan (Druckversion)</title>
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
        <link href="./resources/icons/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon"/>
        <link rel="stylesheet" type="text/css" href="./components/com_content/views/resources/style.css">
        <?php if (Common::getRequest('display') == true): ?>
            <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
            <script src="./components/com_content/views/resources/autoscroll.js" type="text/javascript"></script>
        <?php endif; ?>
    </head>
    <body> <!--onload="window.print()"-->
    <?php
    if ($data) {
        $eva = Eva::getInstance(); // to generate notes..

        $user     = $data['user'];
        $schedule = $data['schedule'];

        // Check $schedule
        if (sizeof($schedule) == 0) {
            echo '<p>Kein Einträge..</p></body></html>';
            exit;
        }

        // Debugging flag
        $debug = false;

        // Settings initially the
        $date   = null;
        $stunde = null;

        // Show only four dates
        $datesCurrent = 0;

        if ($user['accessLevel'] > 1) {
            $datesMax = 4;
        } else {
            $datesMax = 2;
        }

        // Store parsed results of $schedule
        $ausfall  = [];
        $aufsicht = [];

        // Collection of all dates
        $dates = [];

        // Merge Oberstufe(Os) and Unterstufe(Us) --> kind of numeric array
        // Two entries of Us and one entry of Os each line of the output
        $eventsCountOs = 0;
        $eventsCountUs = 0;

        // Preprocess schedule data to print it out
        foreach ($schedule as $row) {
            // Check + collect dates (assuming they are ordered by date)
            if ($row["datum"] != $date) {
                // Only show a certain number of dates..
                if ($datesCurrent == $datesMax) {
                    break;
                }
                $datesCurrent++;

                // Save current date for date check
                $date = $row["datum"];

                // Add date to array
                $dates[] = $date;
            }

            // Check of stunde to merge o
            if ($row["stunde"] != $stunde) {
                // Save current stunde for stunden check
                $stunde = $row["stunde"];

                // Reset of the
                $eventsCountOs = 0;
                $eventsCountUs = 0;
            }

            // Determine type of entry
            if ($row["aufsicht"]) { // aufsicht --> just add $row to $aufsicht
                $aufsicht[$date][$stunde][] = $row;
            } else { // Change --> time to merge!
                // TODO Klassenübergreifende Ankündigung --> Regex

                // Check of class
                if (isOberstufeOnly($row["klasse"])) {         // Oberstufe?
                    $ausfall[$date][$stunde][$eventsCountOs][] = $row;
                    $eventsCountOs++;
                } elseif (isUnterstufeOnly($row["klasse"])) { // Unterstufe?
                    // Floor --> 2 changes per entry
                    $ausfall[$date][$stunde][floor($eventsCountUs)][] = $row;
                    $eventsCountUs                                    = $eventsCountUs + 0.5;
                } else {                                             // Else: Add to both
                    $row['wichtig'] = true;

                    // TODO to both!!
                    //$ausfall[$date][$row["stunde"]][$eventsCountOs][] = $row;
                    //$eventsCountOs ++;

                    $ausfall[$date][$stunde][floor($eventsCountUs)][] = $row;
                    $eventsCountUs                                    = $eventsCountUs + 0.5;
                }
            }
        }

        // Generate output
        // Process each date
        $headerPrinted = false;

        foreach ($dates as $date) {
            /*
             * [1] Page header - avoid pagebreak at first entry
             */
            if ($headerPrinted === false) {
                printHeader($date, false);
                $headerPrinted = true;
            } else {
                printHeader($date, Common::getRequest('forcePagebreak'));
            }

            // Table header
            if ( ! $debug) {
                echo '<table border="1">'."\n";
            }

            /*
             * [2] Notes
             */
            if ( ! $debug) {
                echo '<tr><td style="width: 100%;" colspan="4"><p style="display:inline;margin-right: 0;">Verhinderte Lehrkräfte: </p>'
                    .$eva->generateNote($user, true, $date).'</td></tr>'."\n";
            }

            /*
             * [4] Schedule
             */

            // Check: are there any entries?
            if (count($ausfall[$date]) > 0) {
                // Header for the schedule
                if ( ! $debug) {
                    echo '<tr><td class="emptyRow" style="width: 100%;" colspan="4"></td></tr>'."\n";
                }
                if ( ! $debug) {
                    echo '<tr><th style="width: 10%;">Stunde</th><th colspan="2" style="width: 60%;">Unter- und Mittelstufe</th><th>Oberstufe</th></tr>'
                        ."\n";
                }

                foreach ($ausfall[$date] as $event) {
                    // Count the rows a stunde
                    $rowCount      = 0;
                    $currentStunde = -1;
                    foreach ($event as $row) {
                        $rowCount++;

                        // Get values for Unterstufe and Oberstufe
                        $os       = '';
                        $us       = [];
                        $stundeNo = 0;

                        for ($i = 0; $i <= 2; $i++) {
                            if ( ! isset($row[$i])) {
                                continue;
                            }

                            $entry = $row[$i];
                            if ($entry) {
                                $stundeNo      = $entry['stunde'];
                                $currentStunde = $stundeNo;

                                if (isOberstufeOnly($entry['klasse'])) {
                                    $os = $entry;
                                } elseif (isUnterstufeOnly($entry["klasse"])) {
                                    $us[] = $entry;
                                } else { // Add to both TODO
                                    //$os = $entry;
                                    $us[] = $entry;
                                }
                            }
                        }

                        /*
                         * Avoid NOTICE errors because the variable is undefined..
                         */
                        if ( ! isset($us[0])) {
                            $us[0] = null;
                        }

                        if ( ! isset($us[1])) {
                            $us[1] = null;
                        }

                        if ( ! isset($os)) {
                            $os = null;
                        }

                        // Print ROWS
                        // FIRST row of needs rowspan to merge with the others
                        //
                        // Process all entries again to count them..
                        // TODO Rework this system (maybe count at another time)
                        if ($rowCount == 1) {
                            $rowspan    = 0;
                            $thisStunde = (! empty($us[0]['stunde']) ? $us[0]['stunde'] : $os['stunde']);

                            foreach ($event as $stundeRow) {
                                $stundeStundeRow = (! empty($stundeRow[0]['stunde']) ? $stundeRow[0]['stunde'] : $stundeRow[1]['stunde']);

                                if ($thisStunde == $stundeStundeRow) {
                                    $rowspan++;
                                }
                            }

                            // Add empty row every second time
                            if ( ! $debug && $stundeNo % 2 != 0) {
                                echo '<tr><td class="emptyRow" colspan="4"></td></tr>'."\n";
                            }

                            /* Display Pausenaufsicht */
                            if (isset($aufsicht[$date][$currentStunde]) && count($aufsicht[$date][$currentStunde]) > 0) {
                                foreach ($aufsicht[$date][$currentStunde] as $row) {
                                    if ( ! $debug) {
                                        echo '<tr><td style="width: 100%;" colspan="4"><b>Aufsicht:</b> '.Common::escapeStrings($row["lehrerid"])
                                            .'</td></tr>'."\n";
                                    }
                                }
                            }

                            // Print row (with rowspan..)
                            if ( ! $debug) {
                                echo '<tr><td class="firstCell" rowspan="'.(($rowspan == 0) ? 1 : $rowspan).'">'.Common::escapeStrings($stundeNo)
                                    .'.</td><td class="grey">'.getAusfall($us[0]).'</td><td class="grey">'.getAusfall($us[1]).'</td><td>'
                                    .getAusfall($os).'</td></tr>'."\n";
                            }
                        } else {
                            $stundeNo = null;
                            if ( ! $debug) {
                                echo '<tr><td class="grey">'.getAusfall($us[0]).'</td><td class="grey">'.getAusfall($us[1]).'</td><td>'
                                    .getAusfall($os).'</td></tr>'."\n";
                            }
                        }
                    }
                }
            }
            if ( ! $debug) {
                echo '</table>';
            }
        }
    } else {
        echo '<p>Keine Einträge!</p>';
    }
    ?>
    </body>
    </html>
<?php
function printHeader($date, $pagebreak)
{
    //$date = date_create_from_format('Y-m-d', $date);
    //echo '<h2>'.date_format($date, "d.m.Y").'</h2>';
    $date = strtotime($date);

    if ($pagebreak) {
        echo '<div class="container"><h1>'.Common::escapeStrings(Config::TITLE)."</h1>\n";
    } else {
        echo '<div><h1>'.Common::escapeStrings(Config::TITLE)."</h1>\n";
    }

    // get weekday
    echo '<h2>'.Common::escapeStrings(strftime("%A, %d.%m.%Y", $date))."</h2>\n";
}

function getAusfall($row)
{
    if ( ! $row) {
        return;
    }

    // Define output
    $output = '';

    // Who?
    if (isset($row['klassePatch']) && ! empty($row['klassePatch'])) {
        $output .= $row['klassePatch'].' ';
    } elseif (isset($row['klasse']) && ! empty($row['klasse'])) {
        $output .= $row['klasse'].' ';
    }

    // What?
    if (isset($row['lehrerid']) && ! empty($row['lehrerid'])) {
        if (isset($row['fach']) && ! empty($row['fach'])) {
            $output .= '('.$row['lehrerid'].'/'.$row['fach'].') ';
        } else {
            $output .= '('.$row['lehrerid'].') ';
        }
    }

    // And now?

    // And now?
    if (isset($row['vertretung']) && ! empty($row['vertretung'])) {
        $output .= $row['vertretung'].' ';
    }

    if (isset($row['bemerkung']) && ! empty($row['bemerkung'])) {
        $output .= $row['bemerkung'].' ';
    }

    // Where?
    if (isset($row['raum']) && ! empty($row['raum'])) {
        $output .= '- '.$row['raum'].' ';
    }

    // Hightlight!
    if (isset($row['wichtig']) && ! empty($row['wichtig'])) {
        $output = '<i>'.Common::escapeStrings($output).'</i>';
    }

    // ? /*'<p style="'.(($row['hightlight']) ? 'font-weight: bold;': '').'">'.*/$row["klasse"]." (".$row["lehrerid"]."): ".$row["bemerkung"]/*."</p>"*/: ''

    return ($output);
}

function isOberstufeOnly($form)
{
    //return preg_match('#^(1[0-2])$|^(1[0-1]-1[1-2])$#i', $form);
    return preg_match('#^(1[0-3])#i', $form);
}

function isUnterstufeOnly($form)
{
    return preg_match('#^([5-9][a-z])$|^([5-8]-[6-9])$#i', $form);
}

?>