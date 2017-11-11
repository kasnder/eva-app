<?php
defined('DIR') or die;

// Store input in a temporary file to be able to use the native MySQL CSV Import Statement
$temp_file = tempnam(realpath(sys_get_temp_dir()), 'eva-');
file_put_contents($temp_file, $update); // TODO: parse directly

function tocsv($array)
{
    return str_getcsv($array, '|');
}

$csv = array_map('tocsv', file($temp_file));

// Remove first line (header)
array_shift($csv);

foreach ($csv as $entry) {
    array_map('mysql_real_escape_string', $entry); // Security
    $eva->query('INSERT INTO schedule (datum, stunde, klasse, fach, lehrerid, vertretung, raum, bemerkung) 
		VALUES (STR_TO_DATE("'.$entry[0].'", "%d.%m.%Y"), "'.$entry[1].'", "'.$entry[3].'", "'.$entry[4].'", "'.$entry[5].'", "'.$entry[6].'", "'
        .$entry[7].'", "'.$entry[8].'")');
}

/* Process loaded data.. */
/* Process notes */
// Move imported notes from schedule table into notes table
$eva->query('INSERT INTO notes (note, date, type)
								SELECT bemerkung, datum, `stunde` - 100 AS type FROM `schedule` WHERE stunde >= 100;');
$eva->query('DELETE FROM schedule WHERE stunde >= 100;');

// Beautify result
$eva->query("UPDATE notes SET note = CONCAT( REPLACE( REPLACE( note,  ' bis ',  '-' ) ,  ' ',  ' (' ) ,  ')' ) WHERE note LIKE '%. bis %';");
/* An example in detail:
SELECT CONCAT(
		REPLACE(
				REPLACE(note,
						' bis ',
						'-'
				), # --> 'Br 3.-4.'
				' ',
				' ('
				),  # --> 'Br (3.-4.'
						')'
		)   # --> 'Br (3.-4.)'
		FROM (SELECT 'Br 3. bis 4.' AS note) temp
		 */

// Delete come earlier - not needed
$eva->query('DELETE FROM notes WHERE type = 1;');

/* Process additional comments */
$result = $eva->query('SELECT * FROM notes WHERE type = 0;');
$notes  = [];

while ($comment = mysql_fetch_assoc($result)) {
    // Remember date
    $date = $comment['date'];

    // Explode the note string. Delimter is
    $comments = explode("\n", $comment['note']);

    // Process all stored comments
    foreach ($comments as &$entry) {
        $notes[] = [
            'note' => $entry,
            'date' => $date,
        ];
    }
}

// Readd all notes
$eva->query('DELETE FROM notes WHERE type = 0;');
foreach ($notes as &$entry) {
    $eva->query('INSERT INTO notes (note, date, type)
									VALUES("'.mysql_real_escape_string($entry['note']).'",
											"'.mysql_real_escape_string($entry['date']).'",
											0);');
}

// Make sure we have klassePatch in the new format but with the correct numbers in klasse
$eva->query('UPDATE schedule SET klasse = "10" WHERE klasse LIKE "EF";');
$eva->query('UPDATE schedule SET klasse = "11" WHERE klasse LIKE "Q1";');
$eva->query('UPDATE schedule SET klasse = "12" WHERE klasse LIKE "Q2";');

$eva->query('UPDATE schedule SET klassePatch = klasse;');
$eva->query('UPDATE schedule SET klassePatch = "EF" WHERE klasse = "10";');
$eva->query('UPDATE schedule SET klassePatch = "Q1" WHERE klasse = "11";');
$eva->query('UPDATE schedule SET klassePatch = "Q2" WHERE klasse = "12";');

// Beautify EVA entires
$eva->query('UPDATE schedule SET raum = NULL WHERE bemerkung = "EVA";');