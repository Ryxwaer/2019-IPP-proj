<?php
include 'functions.php';
include 'Errors.php';

$err = new Errors();

$f = 0; // first header flag

$line_counter = 1;
$beg_header = 0; // used to determine the number of header strings ".IPPcode18"
$param_counter = count($argv);

/***  Error numbers  ***/
$ERR_MISSING_PARAM = 10; // parameter is missing
$ERR_INPUT_FILE  = 11;   // error loading input file
$ERR_OUTPUT_FILE = 12;   // error loading output file
$ERR_FATAL_ERROR = 99;   // sth went horribly wrong
$ERR_LEX_SYNTAX  = 21;   // lexical or syntax error

$longopts  = array(
    "source::",    // Optional value
    "stat",        // No value
    "help",           // No value
);
$options = getopt("", $longopts);
var_dump($options);

$help = array_key_exists("help", $options);
$stat = array_key_exists("stat", $options);
$stat_en = false;
$source = array_key_exists("source", $options);

switch (count($options)) {
    case 0:
        $file_name = 'php://stdin';
        break;
    case 1:
        if ($help){
            echo "IPP project #1\n"
                ."for more information please see the train guide or RTFM!\n";
            return;
        }
        elseif ($source){
            $file_name = $options["source"];
        }
        elseif ($stat){
            $file_name = 'php://stdin';
            $stat_en = true;
        }
        else{
            exit($ERR_MISSING_PARAM); //$#$
        }
        break;
    case 2:
        if ($stat && $source){
            $file_name = $options["source"];
            $stat_en = true;
        }
        else {
            exit($ERR_MISSING_PARAM); //$#$
        }
        break;
    default:
        exit($ERR_MISSING_PARAM); //$#$
        break;
} //switch

echo "Source:", $source, "\nStat:", $stat, "\nHelp:", $help;

echo "\nOPTIONS:" , count($options);
echo "\nFILE:", $file_name;
echo"\nstat:", $stat_en;
echo "\n";

$input_file = fopen($file_name, 'r') or die("Couldn't open the file");

if ($input_file == 0) {
    err_out($ERR_MISSING_PARAM);
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

    /** Removes empty strings from array */
    if (!strcmp($word_a[0], "")) {
        for ($j=0; $j < (count($word_a)-1); $j++) {
            $word_a[$j] = $word_a[$j+1];
        }
    }

    /*
     * checks the first string (token), whether it is one of the instrictions
     */
    if ( ( $beg_header == 0 )
        &&
        ( preg_match('/(DEFVAR|MOVE|CREATEFRAME|PUSHFRAME|POPFRAME|CALL|'.
                'RETURN|PUSHS|POPS|ADD|SUB|MUL|IDIV|LT|GT|EQ|AND|OR|NOT|INT2CHAR|'.
                'STRI2INT|READ|WRITE|CONCAT|STRLEN|SETCHAR|GETCHAR|TYPE|LABEL|JUMP|'.
                'JUMPIFEQ|JUMPIFNEQ|DPRINT|BREAK)/',
                $word_a[0]) == 1) )
    {
        err_out($ERR_LEX_SYNTAX);
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
    switch ($word_a[0]) {
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
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_instruction_end($xw);
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }
            break;

        case 'DEFVAR':
            // <var>
            if (var_regex($word_a[1])){
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_instruction_end($xw);
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }
            break;

        case 'CALL':
            // <label>
            if (preg_match('/[(a-zA-Z0-9)|(\_\-\$\&\%\*)]+/', $word_a[1])){
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "label", "arg1", $word_a[1]);
                generate_instruction_end($xw);
                //echo "\n$word_a[0]\n" . "$word_a[1]\n";
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }
            break;
        case 'RETURN':
            //generate
            generate_instruction_start($xw, $line_counter, $word_a[0]);
            generate_instruction_end($xw);
            break;

        case 'PUSHS':
            // <symb>
            if (symb_regex($word_a[1]))
            {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_symb($xw, 1, $word_a);
                generate_instruction_end($xw);
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }
            break;

        case 'POPS':
            // <var>
            if (var_regex($word_a[1])){
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_instruction_end($xw);
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
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_symb($xw, 3, $word_a);
                generate_instruction_end($xw);
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
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_instruction_end($xw);
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
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_symb($xw, 3, $word_a);
                generate_instruction_end($xw);
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
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_instruction_end($xw);
            }
            else { err_out($ERR_LEX_SYNTAX); }
            break;

        case 'LABEL':
        case 'JUMP':
            // <label>
            if (preg_match('/\S+/', $word_a[1])){
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "label", "arg1", $word_a[1]);
                generate_instruction_end($xw);
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
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "label", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_symb($xw, 3, $word_a);
                generate_instruction_end($xw);
            }
            else { err_out($ERR_LEX_SYNTAX); }
            break;

        case 'READ':  // <var> <type>
            if (var_regex($word_a[1])) {}
            else { err_out($ERR_LEX_SYNTAX); }

            if (preg_match('/int|float|string|bool/', $word_a[2])) {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_arg($xw, "type", "arg2", $word_a[2]);
                generate_instruction_end($xw);
            }
            else { err_out($ERR_LEX_SYNTAX); }
            break;

        case 'WRITE':  // <symb>
            if (symb_regex($word_a[1]))
            {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_symb($xw, 1, $word_a);
                generate_instruction_end($xw);
            }
            else { err_out($ERR_LEX_SYNTAX); }
            break;
        case 'EXIT':
        case 'DPRINT':
            // <symb>
            if (symb_regex($word_a[1]))
            {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_symb($xw, 1, $word_a);
                generate_instruction_end($xw);
            }
            else{
                err_out($ERR_LEX_SYNTAX);
            }
            break;

        case 'BREAK':
            generate_instruction_start($xw, $line_counter, $word_a[0]);
            generate_instruction_end($xw);
            break;

        case 'CREATEFRAME':
            //generate
            generate_instruction_start($xw, $line_counter, $word_a[0]);
            generate_instruction_end($xw);
            break;

        case 'PUSHFRAME':
            //generate
            generate_instruction_start($xw, $line_counter, $word_a[0]);
            generate_instruction_end($xw);
            break;

        case 'POPFRAME':
            //generate
            generate_instruction_start($xw, $line_counter, $word_a[0]);
            generate_instruction_end($xw);
            break;

        case '.IPPcode19':
            $beg_header++;
            if ($beg_header !== 1) {
                err_out($ERR_LEX_SYNTAX);
            }
            xmlwriter_start_document($xw, '1.0', 'UTF-8');
            break;

        default:

            // #immediate comment after hashtag
            if (preg_match('/#.*/', $word_a[0])) {
                $comment = 1;
            }
            // Header
            /*elseif (preg_match('/.IPPcode19/', $word_a[0]) == 1) {
                $beg_header++;
                if ($beg_header !== 1) {
                    err_out($ERR_LEX_SYNTAX);
                }
                xmlwriter_start_document($xw, '1.0', 'UTF-8');
            }*/
            elseif (preg_match('/\s+/', $line) == 1) {}
            else {
                if (!feof($input_file)) {
                    err_out($ERR_LEX_SYNTAX);
                }
            }
            break;
    }//SWITCH

    // Finishing of XML output
    // - there is only closing tag needed for the PROGRAM
    xmlwriter_end_element($xw);
    xmlwriter_end_document($xw);
    echo xmlwriter_output_memory($xw);

    // opening PROGRAM tag
    if (($beg_header == 1) && ($f == 0)) {
        echo "<program language=\"IPPcode18\">\n";
        $f++;
    }

} while (!feof($input_file)); // WHILE
echo "</program>\n"; //closing PROGRAM tag

/***  Fin  ***/

?>