<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 20.2.19
 * Time: 20:17
 */

class Statistics
{
    private $_loc;
    private $_comments;
    private $_labels;
    private $_jumps;

    private $loc_en;
    private $comments_en;
    private $labels_en;
    private $jumps_en;

    function __construct()
    {
        $this->_loc = 0;
        $this->_comments = 0;
        $this->_labels = 0;
        $this->_jumps = 0;

        $this->loc_en = NULL;
        $this->comments_en = NULL;
        $this->labels_en = NULL;
        $this->jumps_en = NULL;
    }
    /** *** GETS *** */
    /**
     * @return int
     */
    public function getComments(): int
    {
        return $this->_comments;
    }

    /**
     * @return int
     */
    public function getJumps(): int
    {
        return $this->_jumps;
    }

    /**
     * @return int
     */
    public function getLabels(): int
    {
        return $this->_labels;
    }

    /**
     * @return int
     */
    public function getLoc(): int
    {
        return $this->_loc;
    }

    /**
     * @return bool
     */
    public function isCommentsEn(): bool
    {
        return $this->comments_en;
    }

    /**
     * @return bool
     */
    public function isJumpsEn(): bool
    {
        return $this->jumps_en;
    }

    /**
     * @return bool
     */
    public function isLabelsEn(): bool
    {
        return $this->labels_en;
    }

    /**
     * @return bool
     */
    public function isLocEn(): bool
    {
        return $this->loc_en;
    }

    /**
     * Checks whether there is a parameter in array of parameters
     * @param $params_array
     */
    public function setEnable ($params_array){
        $this->loc_en = array_key_exists("loc", $params_array);
        $this->comments_en = array_key_exists("comments", $params_array);
        $this->labels_en = array_key_exists("labels", $params_array);
        $this->jumps_en = array_key_exists("jumps", $params_array);
    }

    /** ***************************************
     * Increment values
     *
     */
    public function inc_loc(){
        $this->_loc++;
    }

    public function inc_comments(){
        $this->_comments++;
    }

    public function inc_labels(){
        $this->_labels++;
    }

    public function inc_jumps(){
        $this->_jumps++;
    }
}