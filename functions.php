<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 13.2.19
 * Time: 15:31
 */


/**
 * Returns error value on STDERR
 * Closes input file, if it is not closed
 * and exits
 *
 *
 */
function err_out($input_file, $ret_val)
{
    //echo "\n\nERR>";
    fwrite(STDERR, $ret_val);
    /*
    if ($input_file != 0) {
        fclose($input_file);
    }*/
    //kws();
    //echo "\n";
    exit($ret_val);
}

/**
 * Instruction generator
 * output: <instruction order="$num_line" opcode="$word">
 * param: $xw output file of xmllib
 * param: &$line_counter reference to line counter
 *             is incremented everytime an instruction generated
 * param: $num_line
 * param: $
 *
 * return: void
 */
function generate_instruction_start($xw, &$line_counter, $word)
{
    $num_line = $line_counter;
    $line_counter++;
    // <instruction
    xmlwriter_start_element($xw, 'instruction');

    // order="$num_line"
    xmlwriter_start_attribute($xw, 'order');
    xmlwriter_text($xw, $num_line);
    xmlwriter_end_attribute($xw);

    //opcode="$word">
    xmlwriter_start_attribute($xw, 'opcode');
    xmlwriter_text($xw, $word);
    xmlwriter_end_attribute($xw);
}

/**
 *
 * <arg1 type="label">end</arg1>
 * param: $xw output file of xmllib
 * param: $type
 * param: $arg
 * param: $attr
 * return: void
 */
function generate_arg($xw, $type, $arg, $attr)
{
    //  <arg1
    xmlwriter_start_element($xw, $arg);

    //type="label">
    xmlwriter_start_attribute($xw, 'type');
    xmlwriter_text($xw, $type);
    xmlwriter_end_attribute($xw);

    // >end< //text
    xmlwriter_text($xw, $attr);

    // </arg1>
    xmlwriter_end_element($xw);
}

/**
 * Checks parameter and generates a symbol parameter
 *
 * param: $xw output file of xmllib
 * param: $order
 * param: $word_a array of tokens
 *
 *
 * return: void
 */
function generate_symb($xw, $order, $word_a)
{
    $arg = "arg" . "$order";
    if (preg_match('/^(L|T|G)F@[[:alnum:]\_\-\$\&\%\*]+/', $word_a[$order]) !== 1) {
        $txt = explode ("@",$word_a[$order]);
        if (count($txt) == 1) {
            generate_arg($xw, $txt[0], $arg, "");
        }
        else {
            generate_arg($xw, $txt[0], $arg, $txt[1]);
        }
        //var_dump($txt);
    }
    else {
        generate_arg($xw, "var", $arg, $word_a[$order]);
    }
}

/**
 * Generates end of instruction element
 * param: $xw output file of xmllib
 *
 *
 * return: void
 */
function generate_instruction_end($xw)
{
    xmlwriter_end_element($xw);
}

/* fin */
?>