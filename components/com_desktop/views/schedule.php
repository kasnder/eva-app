<?php
defined('DIR') or die;

// Fetch notes
$eva   = Eva::getInstance();
$notes = $eva->generateNote($_SESSION['accessLevel']);
?>
    <!DOCTYPE HTML>
    <html>
    <head>
        <meta charset="UTF-8">
        <title><?php Common::secureEcho(Config::TITLE_SHORT); ?>: Vertretunsplan</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">

        <!-- Favicon -->
        <link rel="icon" href="resources/icons/favicon.png" type="image/png"/>
    </head>
    <body style="padding:1em;">
    <div class="page-header">
        <h1>
            <a href="#"><?php Common::secureEcho(Config::TITLE_SHORT); ?>: Vertretunsplan</a>
            <small>
                Guten Tag, <?php Common::secureEcho($_SESSION['firstname'].' '.$_SESSION['lastname']); ?>!
            </small>
            <small>
                <a class="pull-right" href="index.php?component=desktop&amp;view=logout"><i>Ausloggen</i></a>
            </small>
        </h1>
    </div>
    <div class="center-block" style="max-width:700px;">
        <?php if ($notes): ?>
            <div>
                <?php echo $notes; ?>
            </div>
        <?php endif; ?>

        <p><i>Letztes Update: <?php Common::secureEcho($eva->getSetting('last_update')); ?></i></p>

        <?php if ($data): ?>
            <table class="center-block" class="table table-striped">
                <thead>
                <tr>
                    <th style="text-align: center;max-width:100px;width:30%;">Datum</th>
                    <th style="text-align: center;max-width:100px;width:10%;">#</th>
                    <th style="text-align: center;width:70%;">Planänderung</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // To only output the following values once
                $currentDate   = null;
                $currentStunde = null;

                foreach ($data as $row):?>
                    <tr>
                        <td style="text-align: center;max-width:100px;width:30%;">
                            <?php
                            if ($currentDate != $row['datum']) {
                                $currentDate = $row['datum'];

                                // Format date
                                $date = strtotime($currentDate);
                                $date = strftime("%A, %d.%m.%Y", $date);
                                Common::secureEcho($date);
                            }
                            ?>
                        </td>
                        <td style="text-align: center;max-width:100px;width:10%;">
								<span class="badge"><?php
                                    if ($currentStunde != $row['stunde']) {
                                        Common::secureEcho($row['stunde']);
                                        $currentStunde = $row['stunde'];
                                    }
                                    ?></span>
                        </td>
                        <td style="width:70%;">
                            <?php Common::secureEcho(formatAusfall($row)) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <hr/>
            <p class="align-center">Keine Einträge!</p>
            <hr/>
        <?php endif; ?>


        <!-- Footer -->
        <ul class="pager">
            <?php if ($_SESSION['accessLevel'] == 4): ?>
                <li><a href="<?php Common::secureEcho(Common::getUrl('/index.php?component=desktop&view=manage', true)); ?>">Administration</a></li>
            <?php endif; ?>
            <li><a href="http://<?php Common::secureEcho(Config::HTTP_HOST); ?>/index.php?view=touch">Touch-Version</a></li>
            <li><a href="<?php Common::secureEcho(Config::IMPRINT_URL); ?>">Impressum</a></li>
        </ul>
    </div>
    </body>
    </html>
<?php
// Stolen from the print component..
function formatAusfall($row)
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

    return ($output);
}

?>