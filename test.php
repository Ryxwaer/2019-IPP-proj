<?php

/**
 * JustForFun - helps with debugging
 */
function kws()
{
    echo "\nKillroywashere\n";
}

function err_out($input_file, $ret_val)
{
    echo "\n\nERR>";
    fwrite(STDERR, $ret_val);

    if ($input_file != 0) {
        fclose($input_file);
    }
    //kws();
    exit($ret_val);
}

/***  Start  ***/
///////// PARAMETERS /////////
$ERR_MISSING_PARAM = 10; // parameter is missing
$ERR_INPUT_FILE = 11;   // error loading input file
$ERR_OUTPUT_FILE = 12;   // error loading output file
$ERR_FATAL_ERROR = 99;   // sth went horribly wrong
$ERR_LEX_SYNTAX = 21;   // lexical or syntax error
$directory = ".";
$f_recursive = 0;
$parse_script = "parse.php";
$py_script = "interpret.py";
$fp_py_script = 0;
$fp_parse_script = 0;
$flag_int_only = 0;
$flag_parse_only = 0;

if (false){
    var_dump($argv);
}
$param_counter = count($argv);
$input_file = 0;
//echo $param_counter;
if (($param_counter == 2) && ($argv[1] == "--help")) {
    echo "IPP project #1\n" . "for more information please see the train guide.\n";
    exit;
} elseif (($argc !== 1) && ($argv[1] == "--help") && ($param_counter !== 2)) {
    err_out($input_file, $ERR_MISSING_PARAM);
    exit;
}
$shortopts = "";
$longopts = array(
    "recursive",
    "directory::",
    "parse-script::",
    "int-script::",
    "int-only",
    "parse-only",
);
$options = getopt($shortopts, $longopts);
//var_dump($options);
// FILE INPUT //

$help = array_key_exists("help", $options);
$flag_recursive = array_key_exists("recursive", $options);
$flag_directory = array_key_exists("directory", $options);
$flag_parse_script = array_key_exists("parse-script", $options);
$flag_int_script = array_key_exists("int-script", $options);
$flag_int_only = array_key_exists("int-only", $options);
$flag_parse_only = array_key_exists("parse-only", $options);

if (false){
    echo "REC", $flag_recursive, "\n";
    echo "DIR", $flag_directory, "\n" ;
    echo "ParseScript", $flag_parse_script, "\n" ;
    echo "INT", $flag_int_script, "\n";
    echo "INTONLY", $flag_int_only, "\n" ;
    echo "ParseOnly", $flag_parse_only, "\n";

}

if ($flag_parse_only && $flag_int_only){
    echo "Warning: int-only and parse-only mustn't be used together!";
    echo "It makes no sense";
    exit;
}

foreach ($options as $key => $value) {
    if ($key == "directory") {
        $directory = "$options[$key]";
    } elseif ($key == "recursive") {
        $f_recursive = 1;
    } elseif ($key == "parse-script") {
        $parse_script = "$options[$key]";
        $fp_parse_script = fopen($parse_script, "r");
        if ($fp_parse_script == 0) {
            err_out($fp_parse_script, $ERR_INPUT_FILE);
        }
    } elseif ($key == "int-script") {
        $py_script = "$options[$key]";
        $fp_py_script = fopen($parse_script, "r");
        if ($fp_py_script == 0) {
            err_out($fp_py_script, $ERR_INPUT_FILE);
        }
    } else {
        err_out($input_file, $ERR_MISSING_PARAM);
    }
}
echo "$directory\n";
echo "$parse_script\n";
echo "$py_script\n";
echo "$input_file\n";
/*
if (
	($input_file == 0) &&
	($parse_script == 0) &&
	($py_script == 0)
	) {
	err_out($input_file, $ERR_MISSING_PARAM);
    exit;
}
*/
//kws();
$shell_str = "ls " . "$directory";
$shell_out = shell_exec($shell_str);
$dir_files = explode("\n", $shell_out);
//var_dump($dir_files);
foreach ($dir_files as $key => $value) {
    if (($key == "") || ($value == "")) {
        continue;
    }
    $shell_str = "\necho $directory" . "/$value" . ">> log.txt";
    $shell_out = shell_exec($shell_str);
    $myout = $key + 1;
    $shell_str = "cat " . "$directory" . "/$value " . "| php " . "$parse_script " . "> testy/out" . "$myout" . ".xml\n";
    echo "$shell_str";
    $shell_str = "diff testy/out" . "$myout" . ".xml " . "testy/outref/output" . "$myout" . ".xml" . " >> log.txt";
    echo "$shell_str";
    $shell_out = shell_exec($shell_str);
    echo "$shell_out\n";
    echo "\n";
}


?>