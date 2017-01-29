#!/usr/bin/php
<?php
/*
 * Outline Simple Shell - ECS518U Lab 3
 *
 *    COMMANDS          ERRORS CHECKED
 *    1. info XX         - check file/dir exists
 *    2. files
 *    3. delete  XX      - check file exists and delete succeeds
 *    4. copy XX YY      - XX exists, YY does not exist; copy succeeds
 *    5. where
 *    6. down DD         - check directory exists and change succeeds
 *    7. up              - check not at top
 */


date_default_timezone_set('Europe/London') ;

$prompt = "PShell>" ;
fwrite(STDOUT, "$prompt ") ;
while (1) {
    $line = trim(fgets(STDIN)); // reads one line from STDIN
    $fields = preg_split("/\s+/", $line) ; 

    switch ($fields[0]) {
        case "files": 
           filesCmd($fields);
           break ;
        case "info": 
           infoCmd($fields);
           break ;
        default:
	  echo("Unknown command $fields[0]\n") ;
    }

    fwrite(STDOUT, "$prompt ");
}

// ========================
//   files command
//      List file and directory names
//      No arguments
// ========================
function filesCmd($fields) {
  foreach (glob("*") as $filename) {
    echo("$filename\n") ;
  }
}

// ========================
//   info command
//      List file information
//      1 argument: file name
// ========================
function infoCmd($fields) { 
  echo("No info yet") ;
}



//---------------------- 
// Other functions
//---------------------- 


?>