<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Federico Bernardin federico@bernardin.it
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This class is used to evaluate fields
 *
 * @package TYPO3
 * @subpackage T3Regsitration
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class  tx_t3registration_evaluate {

    /**
     * Function to check if $value is an email
     * @param $conf array contain the options
     * @param $value string/integer value to evaluate
     * @return boolean true if it's correct, 0 otherwise
     */
    public function isEmail($conf,$value) {
        if (preg_match('/[A-Z0-9._%+-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}/i',$value)){
            $subparts = explode('@',$value);
            if (is_array($conf) && isset($conf['blacklist'])){
                if(!t3lib_div::inList($conf['blacklist'],$subparts[1])){
                    return true;
                }
                else{
                    return false;
                }
            }
            return true;
        }
        else
        return false;
    }

    /**
     * Function to check if $value is a number
     * @param $conf array contain the options
     * @param $value string/integer value to evaluate
     * @return boolean true if it's correct, 0 otherwise
     */
    public function isInt($conf,$value) {
        if (is_numeric($value)){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Function to check if $value satisfy the regular expression
     * @param $conf array contain the options
     * @param $value string/integer value to evaluate
     * @return boolean true if it's correct, 0 otherwise
     */
    public function regexp($conf,$value) {
        if (!is_array($conf)) {
            if (preg_match($conf,$value)){
                return true;
            }
            else{
                return false;
            }
        }
        else
        return false;
    }

    /**
     * Function to check if $value is a date (the divider for date are: .,-,/)
     * @param $conf string date format
     * @param $value string value to evaluate
     * @return boolean true if it's correct, 0 otherwise
     */
    public function checkDate($conf,$value) {
        if (!is_array($conf)) {
            //checks if the format is in correct format
            if(preg_match('/([dmyDMY]){1}([\/\.\-]{1})([dmyDMY]){1}([\/\.\-]{1})([dmyDMY]){1}/',$conf,$matches)){
                $dateArray = split($matches[2],$value);
                //checks if the split is an array of 3 elements
                if (count($dateArray) === 3){
                    $evalDate = array();
                    $evalDate[strtolower($matches[1])] = $dateArray[0];
                    $evalDate[strtolower($matches[3])] = $dateArray[1];
                    $evalDate[strtolower($matches[5])] = $dateArray[2];
                    //checks if it's a correct date
                    if(checkdate($evalDate['m'],$evalDate['d'],$evalDate['y'])){
                        return mktime(0,0,0,$evalDate['m'],$evalDate['d'],$evalDate['y']);
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }
        else
        return false;
    }
}