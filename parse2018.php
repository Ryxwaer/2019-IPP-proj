<?php
/////////////////////////////////////////////////
////     Projekt> Parser IPPcode18 na XML    ////
//
// Autor> Jakub Sencak
// Login> xsenca00
//
// Beg>  13-Feb-2019
// Try>  00-Mar-2019  50-80% ??
// Dead> 00-Mar-2019
/////////////////////////////////////////////////

/* Improve:
 *
 * Overovat string@
 *    - string@\010 => escape sekvence
 **
 **
 *
 **
 *
 */

include 'functions.php';

/***  Global variables  ***/

$f = 0; // first header flag
$line_counter = 1; // necessary for output
$end_switch = 1; // $#$ not needed
$input_file = 0; // empty file
$comment = 0; // comment
$beg_header = 0; // used to determine the number of header strings ".IPPcode18"

/***  Error numbers  ***/
$ERR_MISSING_PARAM = 10; // parameter is missing
$ERR_INPUT_FILE  = 11;   // error loading input file
$ERR_OUTPUT_FILE = 12;   // error loading output file
$ERR_FATAL_ERROR = 99;   // sth went horribly wrong
$ERR_LEX_SYNTAX  = 21;   // lexical or syntax error
//$ERR_ = ; //


/***  Start  ***/
///////// PARAMETERS /////////

$param_counter = count($argv);
//echo $param_counter;

if (($param_counter == 2) && ($argv[1] == "--help")) {
    echo "IPP project #1\n"
        ."for more information please see the train guide or RTFM!\n";
    exit;
}
elseif (($param_counter > 2) && ($argv[1] == "--help")) {
    err_out($input_file, $ERR_MISSING_PARAM);
}



$input_file = fopen('php://stdin', 'r');

//$iin = "/home/jakub/Programming/ipp-part1/testy/input8";// . "$i";
//$bubu = readline();
//echo "$iin\n";

//$input_file = fopen("$iin", 'r');

$beg_header = 0;

if ($input_file == 0) {
    err_out($input_file, $ERR_MISSING_PARAM);
}

/*
    while ((preg_match('/.IPPcode18/', $word_a[0]) == 0) && ($beg_header == 0)){
        $line = fgets($input_file);    //echo "$line";
        $line = preg_replace('/\s+/', " ", $line);
        $word_a = explode(" ", $line);
        if (feof($input_file) == 0) {
            break;
        }
    }*/


/**
 * XML output initialization
 */
$xw = xmlwriter_open_memory(); // beg
xmlwriter_set_indent($xw, 1);
$res = xmlwriter_set_indent_string($xw, '');


/**
 * Cycle, which will take each line and transform it into an
 * array of strings.
 *
 * It is using fgets |(ususal pipe) explode | Automat | generate |
 *
 *
 */

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

    if (
        ( $beg_header == 0 )
        &&
        ( preg_match('/(DEFVAR|MOVE|CREATEFRAME|PUSHFRAME|POPFRAME|CALL|'.
                'RETURN|PUSHS|POPS|ADD|SUB|MUL|IDIV|LT|GT|EQ|AND|OR|NOT|INT2CHAR|'.
                'STRI2INT|READ|WRITE|CONCAT|STRLEN|SETCHAR|GETCHAR|TYPE|LABEL|JUMP|'.
                'JUMPIFEQ|JUMPIFNEQ|DPRINT|BREAK)/',
                $word_a[0]) == 1)
    )
    {
        err_out($input_file, $ERR_LEX_SYNTAX);
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
            if (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])){
                //echo "$word_a[1]";
            }
            else{
                err_out($input_file, $ERR_LEX_SYNTAX);
            }

            // <symb>
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[2])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[2])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[2])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[2]))
            ) {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_instruction_end($xw);
            }
            else{
                err_out($input_file, $ERR_LEX_SYNTAX);
            }
            break;

        case 'DEFVAR':
            // <var>
            if (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])){
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_instruction_end($xw);
            }
            else{
                err_out($input_file, $ERR_LEX_SYNTAX);
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
                err_out($input_file, $ERR_LEX_SYNTAX);
            }
            break;

        case 'RETURN':
            //generate
            generate_instruction_start($xw, $line_counter, $word_a[0]);
            generate_instruction_end($xw);
            break;

        case 'PUSHS':
            // <symb>
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[1])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[1])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[1]))
            ) {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_symb($xw, 1, $word_a);
                generate_instruction_end($xw);
            }
            else{
                err_out($input_file, $ERR_LEX_SYNTAX);
            }
            break;

        case 'POPS':
            // <var>
            if (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])){
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_instruction_end($xw);
            }
            else {
                err_out($input_file, $ERR_LEX_SYNTAX);
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
            if (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])){}
            else { err_out($input_file, $ERR_LEX_SYNTAX); }

            // <symb>
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[2])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[2])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[2])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[2]))
            ) {}
            else{ err_out($input_file, $ERR_LEX_SYNTAX); }

            // <symb>
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[3])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[3])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[3])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[3]))
            ) {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_symb($xw, 3, $word_a);
                generate_instruction_end($xw);
            }
            else{ err_out($input_file, $ERR_LEX_SYNTAX); }
            break;

        case 'NOT':
        case 'STRLEN'://$#$
        case 'INT2CHAR':
            // <var>
            if (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])){}
            else { err_out($input_file, $ERR_LEX_SYNTAX); }

            // <symb>
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[2])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[2])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[2])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[2]))
            ) {
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_instruction_end($xw);
            }
            else{ err_out($input_file, $ERR_LEX_SYNTAX); }
            break;

        case 'STRI2INT':
            //<var>
            if (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])){}
            else { err_out($input_file, $ERR_LEX_SYNTAX); }

            //<symb>
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[2])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[2])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[2])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[2]))
            ) {}
            else { err_out($input_file, $ERR_LEX_SYNTAX); }

            //<symb>
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[3])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[3])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[3])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[3]))
            ) {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_symb($xw, 3, $word_a);
                generate_instruction_end($xw);
            }
            else { err_out($input_file, $ERR_LEX_SYNTAX); }
            break;

        case 'TYPE':
            // <var>
            if (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])){}
            else { err_out($input_file, $ERR_LEX_SYNTAX); }

            // <symb>
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[2])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[2])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[2])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[2]))
            ) {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_instruction_end($xw);
            }
            else { err_out($input_file, $ERR_LEX_SYNTAX); }
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
            else { err_out($input_file, $ERR_LEX_SYNTAX); }
            break;

        case 'JUMPIFEQ':
        case 'JUMPIFNEQ':
            // label
            if (preg_match('/\S+/', $word_a[1])){}
            else { err_out($input_file, $ERR_LEX_SYNTAX); }
            // symb
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[2])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[2])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[2])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[2]))
            ) {}
            //generate
            else { err_out($input_file, $ERR_LEX_SYNTAX); }

            //symb
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[3])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[3])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[3])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[3]))
            ) {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "label", "arg1", $word_a[1]);
                generate_symb($xw, 2, $word_a);
                generate_symb($xw, 3, $word_a);
                generate_instruction_end($xw);
            }
            else { err_out($input_file, $ERR_LEX_SYNTAX); }
            break;

        case 'READ':  // <var> <type>
            if (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])){}
            else { err_out($input_file, $ERR_LEX_SYNTAX); }

            if (preg_match('/int|string|bool/', $word_a[2])) {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_arg($xw, "var", "arg1", $word_a[1]);
                generate_arg($xw, "type", "arg2", $word_a[2]);
                generate_instruction_end($xw);
            }
            else { err_out($input_file, $ERR_LEX_SYNTAX); }
            break;

        case 'WRITE':  // <symb>
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[1])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[1])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[1]))
            ) {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_symb($xw, 1, $word_a);
                generate_instruction_end($xw);
            }
            else { err_out($input_file, $ERR_LEX_SYNTAX); }
            break;

        case 'DPRINT':
            // <symb>
            if (
                (preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[1])) ||
                (preg_match('/^((bool@)(true|false))$/', $word_a[1])) ||
                (preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[1])) ||
                (preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[1]))
            ) {
                //generate
                generate_instruction_start($xw, $line_counter, $word_a[0]);
                generate_symb($xw, 1, $word_a);
                generate_instruction_end($xw);
            }
            else{
                err_out($input_file, $ERR_LEX_SYNTAX);
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

        default:
            // #immediate comment after hashtag
            if (preg_match('/#.*/', $word_a[0])) {
                $comment = 1;
            }
            // Header
            elseif (preg_match('/.IPPcode18/', $word_a[0]) == 1) {
                $beg_header++;
                if ($beg_header !== 1) {
                    err_out($input_file, $ERR_LEX_SYNTAX);
                }
                xmlwriter_start_document($xw, '1.0', 'UTF-8');
            }
            elseif (preg_match('/\s+/', $line) == 1) {}
            else {
                if (!feof($input_file)) {
                    err_out($input_file, $ERR_LEX_SYNTAX);
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

// CLOSE FILE
//fclose($input_file); // $#$ // NO NEED TO CLOSE STDIN

/***  Fin  ***/

//Bin

//^(string@[[:alnum:]]+)|(string@()\\\d{3})|^string@

/*
    while ((preg_match('/.IPPcode18/', $word_a[0]) == 0) && ($beg_header == 0)){
        $line = fgets($input_file);    //echo "$line";
        $line = preg_replace('/\s+/', " ", $line);
        $word_a = explode(" ", $line);
        if (feof($input_file) == 0) {
            break;
        }
    }*/
//if ($comment == 1) {$comment = 0; continue;}
//echo "\n$word_a[0]";

//if ($beg_header == 0) {
//    err_out($input_file, $ERR_LEX_SYNTAX);
//}


//echo preg_match('/^(LF|TF|GF)@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[2]);
//echo preg_match('/^((bool@)(true|false))$/', $word_a[2]);
//echo preg_match('/^(int@(\+|\-)[[:digit:]]+)|(int@[[:digit:]]+)|int@/', $word_a[2]);
//echo preg_match('/^(string@[[:alnum:]]+)|string@/', $word_a[2]);

?>