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


date_default_timezone_set('Europe/London');

$prompt = "PShell>";
fwrite(STDOUT, "$prompt ");
while (1) {
    $line = trim(fgets(STDIN));
    $fields = preg_split("/\s+/", $line);

    switch ($fields[0]) {
        case "files":
            filesCmd($fields);
            break;
        case "info":
            infoCmd($fields);
            break;
        case "delete":
            deleteCmd($fields);
            break;
        case "copy":
            copyCmd($fields);
            break;
        case "where":
            whereCmd($fields);
            break;
        case "down":
            downCmd($fields);
            break;
        case "up":
            upCmd($fields);
            break;
        case "exit":
            return;
        default:
            echo("Unknown command $fields[0]");
    }

    echo("\n");
    fwrite(STDOUT, "$prompt ");
}

/**
 * Lists directories and files in current directory. Lists directories first, in red text, followed
 * by files in white text.
 *
 * @param $fields
 */
function filesCmd($fields) {
    if (sizeof($fields) != 1) {
        echo("Incorrect usage of files command. Command does not support optional arguments.");
        return;
    }

    $directories = "\033[31m";
    $files = "\033[0m";

    foreach (glob("*") as $filename) {
        if (is_dir($filename)) $directories = $directories . $filename . "/\t";
        else $files = $files . $filename . "\t";
    }

    echo($directories . "" . $files);
}

/**
 * Displays information for indicated file. Checks if file exists.
 * @param $fields
 */
function infoCmd($fields) {
    if (sizeof($fields) != 2) {
        echo("Incorrect usage of info command. Correct usage: info [file or directory name]");
    }
    else if (!file_exists($fields[1])) {
        echo($fields[1] . ": no such file or directory.");
    }
    else {
        $fileOwner = posix_getpwuid(fileowner($fields[1]));
        $result = "" . $fileOwner['name'] . " " . date("d/m/Y", filemtime($fields[1]));

        if (is_dir($fields[1])) {
            $result = "\033[31m" . "directory\033[0m: " . $result;
        }
        else {
            $result = "file: " . $result . " " . filesize($fields[1]) . "B ";
            if (is_executable($fields[1])) $result = $result . "executable";
            else $result = $result . "not executable";
        }
        echo($result);
    }
}

/**
 * Deletes a folder or file indicated by user.
 * @param $fields
 */
function deleteCmd($fields) {
    // incorrect number of arguments.
    if (sizeof($fields) != 2) {
        echo("Incorrect usage of delete command. Correct usage: delete [file or directory name]");
    }
    else if (!file_exists($fields[1])) {
        echo($fields[1] . ": no such file or directory.");
    }
    else if (is_dir($fields[1])) {
        if (!rmdir($fields[1])) echo("Could not delete directory.");
    }
    else {
        if (!unlink($fields[1])) echo("Could not delete file.");
    }
}

/**
 * Copies the contents of an existing file into a new file. Cannot use directories as sources or destinations.
 * Cannot overwrite existing files.
 *
 * @param $fields
 */
function copyCmd($fields) {
    if (sizeof($fields) != 3) {
        echo("Incorrect usage of copy command. Correct usage: copy [source file path] [destination file path]");
    }
    else if (!file_exists($fields[1]) || is_dir($fields[1])) {
        echo($fields[1] . ": no such source file.");
    }
    else if (file_exists($fields[2]) && is_dir($fields[2])) {
        echo($fields[2] . ": destination must be a file, not a directory.");
    }
    else if (file_exists($fields[2])) {
        echo($fields[2] . ": destination file already exists, cannot overwrite.");
    }
    else if (!copy($fields[1], $fields[2])) {
        echo("Could not copy file.");
    }
}

/**
 * Prints the path of the current directory.
 * @param $fields
 */
function whereCmd($fields) {
    if (sizeof($fields) != 1) echo("Incorrect usage of where command. Command does not support optional arguments.");
    else echo(getcwd() . "\n");
}

/**
 * @param $fields
 */
function downCmd($fields) {
    if (sizeof($fields) != 2) {
        echo("Incorrect usage of down command. Correct usage: down [destination folder path]");
    }
    else if (!file_exists($fields[1]) || !is_dir($fields[1])) {
        echo($fields[1] . ": no such directory.");
    }
    else {
        chdir($fields[1]);
    }
}

/**
 * Changes directory to the parent of the current directory.
 * @param $fields
 */
function upCmd($fields) {
    if (sizeof($fields) != 1) {
        echo("Incorrect usage of up command. Command does not support optional arguments.");
    }
    else {
        chdir("..");
        // todo: error message when using up at root?
    }
}
?>