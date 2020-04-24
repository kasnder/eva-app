<?php
defined('DIR') or die;

// Get $eva
$eva = Eva::getInstance();

// Store input in a temporary file to be able to use the native MySQL CSV Import Statement
$temp_file = tempnam(realpath(sys_get_temp_dir()), 'ops-');
file_put_contents($temp_file, $update);

// Import query
$sql =  "LOAD DATA LOCAL INFILE '{$temp_file}'".
		"INTO TABLE `schedule`
        FIELDS TERMINATED BY '|'".
        //OPTIONALLY ENCLOSED BY '\"'
"LINES TERMINATED BY '\r\n'
        IGNORE 1 LINES
        (@datum, `stunde`, @dummy, `klasse`, `fach`, `lehrerid`, `vertretung`, `raum`, `bemerkung`)
        set datum = STR_TO_DATE(@datum, '%d.%m.%Y')";

// Run query
$eva->query($sql);

/* Process loaded data.. */
/* Process notes */
// Move imported notes from schedule table into notes table
$eva->query('INSERT INTO notes (note, date, type)
								SELECT bemerkung, datum, `stunde` - 100 as type FROM `schedule` WHERE stunde >= 100;');
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

// Delete früh kommend. Not needed.
$eva->query('DELETE FROM notes WHERE type = 1;');

/* Process comments */
$result = $eva->query('SELECT * FROM notes WHERE type = 0;');

/*
-- Beschreibung der erweiterten Kommentarfunktionen --

  Unter der Funktion "Planzusatz" des Turbo Vertretungsplans können im Zusammenspiel mit dem neuen elektronischen   Vertretungsplan die Einträge des Planers überschrieben und ergänzt werden.

Schema:
    - Löschen:    d[Stunde]/[Lehrerkürzel]
    - Hinzufügen: a(!/A)/[Stunde]/[Klasse]/[Fach]/[Lehrerkürzel]/[Bemerkung]

  Alles andere wird als einfacher Kommentar interpretiert und in der Kopfzeile für alle Benutzer ausgegeben.

Beispiel (Eintrag  unter Planzusatz):

  Nach der 6. Stunde Lehrerkonferenz
  a/3/7c/Sb/M/Bg statt 6., 124
  a/6/7b/Bg//in 405 statt 418
  a/6/7b/Bg/Ch/in 405 statt 418
  aA/5//IV Ka, 3. Etage//
  a!/6////Wegen einer Lehrerkonferenz Ende nach der 6. Stunde

  Ausgabe (zeilenweise interpretiert):
    1. Zeile: Nach der 6. Stunde Lehrerkonferenz (in der Kopfzeile)
    2. Zeile: 3. 7c (Sb/M) Bg statt 6., 124
    3. Zeile: 6. 7b (Bg) in 405 statt 418
    4. Zeile: 6. 7b (Bg/Ch) in 405 statt 418
    5. Zeile: 5. Pause: IV Ka, 3. Etage - Aufsicht (abgehoben)
    6. Zeile: 6. Wegen einer Lehrerkonferenz Ende nach der 6. Stunde (hervorgehoben)
*/

// Patches to the TURBO schedule?
// Arrays for changes
$delete = array();
$add    = array();
$keep   = array();

while($comment = mysqli_fetch_assoc($result)) {
	// Save date
	$date     = $comment['date'];

	// Explode the note string. Delimter is
	$comments = explode("\n", $comment['note']);

	foreach($comments as &$entry){
		// Sort comments
		/*
		* Style guide:
		*
		* - DELETE comment: d[Stunde]/[Lehrerkürzel]
		* - ADD    comment: a[!/A]/[Stunde]/[Klasse]/[Fach]/[Lehrerkürzel]/[Bemerkung]
		*/

		if (strpos($entry, 'd') === 0) {       // Delete
			// Try to split the entry
			$parts    = explode('/', $entry);

			if (sizeof($parts) == 3) {
				$delete[] = array('stunde'  => $parts[1],
						'lehrerid'=> $parts[2],
						'datum'   => $date );
				continue;
			}
		} elseif (strpos($entry, 'a') === 0) { // Add
			// Try to split the entry
			$parts = explode('/', $entry);
			$wichtig  = (strpos($parts[0], '!') !== false) ? 1 : 0;
			$aufsicht = (strpos($parts[0], 'A') !== false) ? 1 : 0;

			if (sizeof($parts) == 6) {
				$add[] = array('stunde'    => $parts[1],
						'klasse'    => $parts[2],
						'lehrerid'  => $parts[3],
						'fach'      => $parts[4],
						'bemerkung' => $parts[5],
						'wichtig'   => $wichtig ,
						'aufsicht'  => $aufsicht,
						'datum'     => $date );
				continue;
			}
		}

		// ELSE: Keep the entry as note
		$keep[] = array('note' => $entry,
				'date'    => $date );
	}
}

// Clean up..
$eva->query('DELETE FROM notes WHERE type = 0;');

// Commit changes to DB
foreach($delete as &$entry){
	$eva->query('DELETE FROM schedule WHERE stunde   = "'.mysql_real_escape_string($entry['stunde']  ).'" AND
														   lehrerid = "'.mysql_real_escape_string($entry['lehrerid']).'" AND
														   datum    = "'.mysql_real_escape_string($entry['datum']   ).'"');
}

foreach($add as &$entry){
	$eva->query('INSERT INTO schedule (datum, klasse, stunde, lehrerid, fach, bemerkung, wichtig, aufsicht)
									VALUES("'.mysql_real_escape_string($entry['datum']    ).'",
										   "'.mysql_real_escape_string($entry['klasse']   ).'",
										   "'.mysql_real_escape_string($entry['stunde']   ).'",
										   "'.mysql_real_escape_string($entry['lehrerid'] ).'",
										   "'.mysql_real_escape_string($entry['fach']     ).'",
										   "'.mysql_real_escape_string($entry['bemerkung']).'",
										   "'.mysql_real_escape_string($entry['wichtig']  ).'",
										   "'.mysql_real_escape_string($entry['aufsicht'] ).'");');
}

foreach($keep as &$entry){
	$eva->query('INSERT INTO notes (note, date, type)
									VALUES("'.mysql_real_escape_string($entry['note']).'",
										   "'.mysql_real_escape_string($entry['date']).'",
										   0);');
}

// Patch EF, Q1, Q2 to be processable by the system
$eva->query('UPDATE schedule SET klassePatch = klasse;');
$eva->query('UPDATE schedule SET klasse = "10" WHERE klasse = "EF";');
$eva->query('UPDATE schedule SET klasse = "11" WHERE klasse = "Q1";');
$eva->query('UPDATE schedule SET klasse = "12" WHERE klasse = "Q2";');

// Beautify EVA entires
$eva->query('UPDATE schedule SET raum = null WHERE bemerkung = "EVA";');