

<?php
include 'functions.php';
include 'Errors.php';
include 'stat.php';
include "debug.php";

$err = new Errors();

$f = 0; // first header flag

$line_counter = 1;
$beg_header = 0; // used to determine the number of header strings ".IPPcode19"
$param_counter = count($argv);

/***  Error numbers  ***/
$ERR_MISSING_PARAM = 10; // parameter is missing $ERR_INPUT_FILE  = 11;   // error loading input file
$ERR_OUTPUT_FILE = 12;   // error loading output file
$ERR_FATAL_ERROR = 99;   // sth went horribly wrong
$ERR_LEX_SYNTAX  = 23;   // lexical or syntax error

/*** Used Filenames ***/
$file_name = 'php://stdin';
$stats_file_name = '';

/*** Parameters processing ***/

$longopts  = array(
    "source::",    // Optional value
    "stats::",     // Optional value
    "help",        // No value
    "loc",
    "comments",
    "labels",
    "jumps"
);
$options = getopt("", $longopts);

$help = array_key_exists("help", $options);
$stats = array_key_exists("stats", $options);
$stats_en = false;
$source = array_key_exists("source", $options);
$not_processed_instructions = 0; // helps determine Instructioons that were not processed

$stats_obj = new Statistics();

$stats_obj->setEnable($options);

if (count($options) == 0) {
    $file_name = 'php://stdin';
}
else {
    if ($source) {
        $file_name = $options["source"];
    }
    if ($stats) {
        $stats_en = true;
        $stats_file_name = $options["stats"];
    }
}

if ( ! $stats &&
    (   $stats_obj->isCommentsEn() ||
        $stats_obj->isJumpsEn() ||
        $stats_obj->isLocEn() ||
        $stats_obj->isLabelsEn()
    )
){
    exit($err->getMissingParameter());
}

if ($help &&
    ($stats ||
        $source ||
        $stats_obj->isCommentsEn() ||
        $stats_obj->isJumpsEn() ||
        $stats_obj->isLocEn() ||
        $stats_obj->isLabelsEn()
    )
){
    exit($err->getMissingParameter());
}
elseif ($help) {
    echo "IPP project #1\n"
        ."for more information please see the train guide or RTFM!\n";
    exit(0);
}

/*** DEBUG  ***/
if ($debug){
    var_dump($options);
    echo "Source:", $source, "\nStat:", $stats, "\nHelp:", $help;

    echo "\nOPTIONS:" , count($options);
    echo "\nFILE:", $file_name;
    echo"\nstat:", $stats_en;
    echo "\nSTAT>", $stats_file_name;

    echo "\nComments ", $stats_obj->getComments();
    $stats_obj->inc_comments();
    echo "\nComments after ---------- ", $stats_obj->getComments();
    echo "\nJumps ", $stats_obj->getJumps();
    echo "\nLabels ", $stats_obj->getLabels();
    echo "\nLoc ", $stats_obj->getLoc();

    echo "\n";
    if ($stats_obj->isCommentsEn()){
        echo "CPMMENTS ENABLED";
    }
}

/*** Opening files ***/

$input_file = fopen($file_name, 'r') or die("Couldn't open the file");

if ($input_file == 0) {
    err_out($ERR_MISSING_PARAM);
}

if ($stats_en && $stats_file_name !== '')
{
    $stats_file = fopen($stats_file_name, 'w');
    if ($stats_file == 0)
    {
        err_out($err->getCouldnotOpenOUTfile());
    }
}

$begin_header = 0;

/**
 * XML output initialization
 */
$xw = xmlwriter_open_memory(); // beg
xmlwriter_set_indent($xw, 1);
$res = xmlwriter_set_indent_string($xw, '');

do {

    $line = fgets($input_file);
    $line = preg_replace('/\s+/', " ", $line);
    $word_a = explode(" ", $line);
    //$word_a = preg_split("/ (\s+|#) /", $line, null, PREG_SPLIT_NO_EMPTY);
    //var_dump($word_a);

    /** Removes empty strings from array */
    if (!strcmp($word_a[0], "")) {
        for ($j=0; $j < (count($word_a)-1); $j++) {
            $word_a[$j] = $word_a[$j+1];
        }
    }

    $instruction_list_string_preg = 'DEFVAR|MOVE|CREATEFRAME|PUSHFRAME|POPFRAME|CALL|'.
        'RETURN|PUSHS|POPS|ADD|SUB|MUL|IDIV|LT|GT|EQ|AND|OR|NOT|INT2CHAR|'.
        'STRI2INT|READ|WRITE|CONCAT|STRLEN|SETCHAR|GETCHAR|TYPE|LABEL|JUMP|'.
        'JUMPIFEQ|JUMPIFNEQ|DPRINT|BREAK|EXIT'; //FLOAT2INT

    /*
     * checks the first string (token), whether it is one of the instrictions
     */
    if ( ( $beg_header == 0 )
        &&
        ( preg_match('/('. $instruction_list_string_preg .')/',
                strtoupper($word_a[0])) == 1) ) //obhajoba
    {
        err_out($err->getMissingHeaderErr());
    }


    /**
     * the SWITCH
     * takes array of strings from input file
     * Takes the instruction and its parameters
     * Checks all the parameters using regex
     *
     * Returns non 0 value if something is not right
     *
     */
    switch (strtoupper($word_a[0])) {
        case '#':
            $comment = 1;
            break;

        case 'MOVE':
            // <var>
            if (var_regex($word_a[1])){
                //echo "$word_a[1]";
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }

            // <symb>
            if (symb_regex($word_a[2]))
            {
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                gen_ins_end($xw);
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }
            break;

        case 'DEFVAR':
            // <var>
            if (var_regex($word_a[1])){
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "var", "arg1", $word_a[1]);
                gen_ins_end($xw);
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }
            break;

        case 'CALL':
            // <label>
            if (preg_match('/[(a-zA-Z0-9)|(\_\-\$\&\%\*)]+/', $word_a[1])){
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "label", "arg1", $word_a[1]);
                gen_ins_end($xw);
                //echo "\n$word_a[0]\n" . "$word_a[1]\n";
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }
            break;
        case 'RETURN':
            //generate
            generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
            gen_ins_end($xw);
            break;

        case 'PUSHS':
            // <symb>
            if (symb_regex($word_a[1]))
            {
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                generate_symb($xw, 1, $word_a);
                gen_ins_end($xw);
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }
            break;

        case 'POPS':
            // <var>
            if (var_regex($word_a[1])){
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "var", "arg1", $word_a[1]);
                gen_ins_end($xw);
            }
            else {
                err_out($ERR_LEX_SYNTAX);
            }
            break;

        case 'ADD':
        case 'SUB':
        case 'MUL':
        case 'IDIV':

        case 'LT':
        case 'GT':
        case 'OR'://$#$
        case 'EQ':

        case 'AND'://$#$

        case 'GETCHAR'://$#$
        case 'SETCHAR'://$#$
        case 'CONCAT'://$#$
            //var
            if (var_regex($word_a[1])) {}
            else { err_out($ERR_LEX_SYNTAX); }

            // <symb>
            if (symb_regex($word_a[2])){}
            else{ err_out($ERR_LEX_SYNTAX); }

            // <symb>
            if (symb_regex($word_a[3]))
            {
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_symb($xw, 3, $word_a);
                gen_ins_end($xw);
            }
            else{ err_out($ERR_LEX_SYNTAX); }
            break;

        case 'NOT':
        case 'STRLEN'://$#$
        case 'INT2CHAR':
            // <var>
            if (var_regex($word_a[1])){}
            else { err_out($ERR_LEX_SYNTAX); }

            // <symb>
            if (symb_regex($word_a[2]))
            {
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                gen_ins_end($xw);
            }
            else{ err_out($ERR_LEX_SYNTAX); }
            break;
        case 'STRI2INT':
            //<var>
            if (var_regex($word_a[1])) {}
            else { err_out($ERR_LEX_SYNTAX); }

            //<symb>
            if (symb_regex($word_a[2])) {}
            else { err_out($ERR_LEX_SYNTAX); }

            //<symb>
            if (symb_regex($word_a[3]))
            {
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_symb($xw, 3, $word_a);
                gen_ins_end($xw);
            }
            else { err_out($ERR_LEX_SYNTAX); }
            break;
        case 'INT2FLOAT':
        case 'FLOAT2INT':
        case 'TYPE':
            // <var>
            if (var_regex($word_a[1])){}
            else { err_out($ERR_LEX_SYNTAX); }

            // <symb>
            if (symb_regex($word_a[2]))
            {
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                gen_ins_end($xw);
            }
            else { err_out($ERR_LEX_SYNTAX); }
            break;

        case 'LABEL':
        case 'JUMP':
            // <label>
            if (preg_match('/\S+/', $word_a[1])){
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "label", "arg1", $word_a[1]);
                gen_ins_end($xw);
            }
            else { err_out($ERR_LEX_SYNTAX); }
            break;

        case 'JUMPIFEQ':
        case 'JUMPIFNEQ':
            // label
            if (preg_match('/\S+/', $word_a[1])){}
            else { err_out($ERR_LEX_SYNTAX); }
            // symb
            if (symb_regex($word_a[2])){}
            //generate
            else { err_out($ERR_LEX_SYNTAX); }

            //symb
            if (symb_regex($word_a[3]))
            {
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "label", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_symb($xw, 3, $word_a);
                gen_ins_end($xw);
            }
            else { err_out($ERR_LEX_SYNTAX); }
            break;

        case 'READ':  // <var> <type>
            if (var_regex($word_a[1])) {}
            else { err_out($ERR_LEX_SYNTAX); }

            if (preg_match('/int|float|string|bool/', $word_a[2])) {
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                gen_arg($xw, "var", "arg1", $word_a[1]);
                gen_arg($xw, "type", "arg2", $word_a[2]);
                gen_ins_end($xw);
            }
            else { err_out($ERR_LEX_SYNTAX); }
            break;

        case 'WRITE':
        case 'EXIT':
        case 'DPRINT':
            // <symb>
            if (symb_regex($word_a[1]))
            {
                //generate
                generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
                generate_symb($xw, 1, $word_a);
                gen_ins_end($xw);
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }
            break;

        case 'BREAK':
        case 'CREATEFRAME':
        case 'PUSHFRAME':
        case 'POPFRAME':
            //generate
            generate_instruction_start($xw, $line_counter, strtoupper($word_a[0]));
            gen_ins_end($xw);
            break;
        /*
        case '.IPPcode19':
            $beg_header++;
            if ($beg_header !== 1) {
                err_out($ERR_LEX_SYNTAX);
            }
            xmlwriter_start_document($xw, '1.0', 'UTF-8');
            break;
        */
        default:

            // #immediate comment after hashtag
            if (preg_match('/#.*/', $word_a[0])) {
                $comment = 1;
            }
            elseif ($word_a[0] == ""){}
            elseif (preg_match('/.IPPCODE19/', strtoupper($word_a[0])) == 1) {
                $beg_header++;
                if ($beg_header !== 1) {
                    err_out($err->getMissingHeaderErr());
                }
                xmlwriter_start_document($xw, '1.0', 'UTF-8');
            }
            elseif (preg_match('/('. $instruction_list_string_preg .')/', strtoupper($word_a[0])) === 0) {
                // jedna sa o chybu zleho tvaru instrukcie
                exit($err->getOpcodeErr());
            }
            elseif ($beg_header == 0 && (preg_match('/('. $instruction_list_string_preg .')/', strtoupper($word_a[0])) === 1)){
                exit($err->getMissingHeaderErr());
            }
            elseif (preg_match('/\s+/', $line) == 1) {}
            else {
                if (!feof($input_file)) {
                    err_out($ERR_LEX_SYNTAX);
                }
            }
            break;
    }//SWITCH

    /*** Processing STATISTICS ***/
    foreach ($word_a as &$value) {
        if (preg_match('/#.*/', $value)){
            $stats_obj->inc_comments();
        }
    }

    $to_check = strtoupper($word_a[0]);
    if ($to_check == 'LABEL'){
        $stats_obj->inc_labels();
    }


    switch ($to_check) {
        case 'JUMP':
        case 'JUMPIFEQ':
        case 'JUMPIFNEQ':
            $stats_obj->inc_jumps();
            break;
        default:
            break;
    }

    // not working // if ($word_a[0] == "" || (preg_match('/#.*/', $to_check) == 1) || (preg_match('/\s+/
    //', $line) == 1)){}
    if (preg_match('/('. $instruction_list_string_preg .')/',
            strtoupper($word_a[0])) == 1){
        $stats_obj->inc_loc();
    }
    else {
        $not_processed_instructions++;
    }
    /*** Stat End ***/
    // Finishing of XML output
    // - there is only closing tag needed for the PROGRAM
    xmlwriter_end_element($xw);
    xmlwriter_end_document($xw);
    echo xmlwriter_output_memory($xw);

    // opening PROGRAM tag
    if (($beg_header == 1) && ($f == 0)) {
        echo "<program language=\"IPPcode19\">\n";
        $f++;
    }

} while (!feof($input_file)); // WHILE

if ($beg_header == 1) {
    echo "</program>\n"; //closing PROGRAM tag
}


if ($stats_en){
    $string_to_write_stats = '';
    if ($stats_obj->isLocEn()){
        $string_to_write_stats = $string_to_write_stats . $stats_obj->getLoc() . "\n";
    }

    if ($stats_obj->isCommentsEn()){
        $string_to_write_stats = $string_to_write_stats . $stats_obj->getComments() . "\n";
    }

    if ($stats_obj->isLabelsEn()){
        $string_to_write_stats = $string_to_write_stats . $stats_obj->getLabels() . "\n";
    }

    if ($stats_obj->isJumpsEn()){
        $string_to_write_stats = $string_to_write_stats . $stats_obj->getJumps() . "\n";
    }
    fwrite($stats_file, $string_to_write_stats);
    fclose($stats_file);
}

exit(0);

/***  Fin  ***/

?>