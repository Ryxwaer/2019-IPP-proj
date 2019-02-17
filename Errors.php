<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 13.2.19
 * Time: 20:17
 */

class Errors
{
    private $missing_parameter = 10;
    private $couldnot_open_INfile = 11;
    private $couldnot_open_OUTfile = 12;
    private $missing_header_err = 21;
    private $opcode_err = 22;
    private $lex_err = 23;
    private $internal_err = 99;

    /**
     * @return int
     */
    public function getMissingParameter(): int
    {
        return $this->missing_parameter;
    }

    /**
     * @return int
     */
    public function getLexErr(): int
    {
        return $this->lex_err;
    }

    /**
     * @return int
     */
    public function getMissingHeaderErr(): int
    {
        return $this->missing_header_err;
    }

    /**
     * @return int
     */
    public function getOpcodeErr(): int
    {
        return $this->opcode_err;
    }

    /**
     * @return int
     */
    public function getCouldnotOpenINfile(): int
    {
        return $this->couldnot_open_INfile;
    }

    /**
     * @return int
     */
    public function getCouldnotOpenOUTfile(): int
    {
        return $this->couldnot_open_OUTfile;
    }

    /**
     * @return int
     */
    public function getInternalErr(): int
    {
        return $this->internal_err;
    }
}