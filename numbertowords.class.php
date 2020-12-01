<?php

/**
 * @author Matan <matanfxp@hotmail.co.il>
 * @date 16/4/2014
 * @time 16:55
 * @file numberrowords.class.php
 * @copyright 2014
 */
 
/**
 * Class for translating numbers into Hebrew.
 *
 * @category Numbers
 * @package  Numbers_To_Words
 * @author   Matan <matanfxp@hotmail.co.il>
 * @license  PHP 3.01 http://www.php.net/license/3_01.txt
*/


if (!function_exists(bcmul)) {
  function bcmul($_ro, $_lo, $_scale=0) {
    return round($_ro*$_lo, $_scale);
  }
}
  
if (!function_exists(bcdiv)) {
  function bcdiv($_ro, $_lo, $_scale=0) {
    return round($_ro/$_lo, $_scale);
  }
}


class NumberToWords
{
    private $Number;
    private $IsNegative;
    private $NumberAsWords = array();
    private $NumberAsArray = array();
    
    /**
     * An array that contains for each element the name of the level (in Hebrew) in range of 0 to 999 translated to words.
     */
    private $NumberText = array(
        /**
         * The translations words to the numbers in range of 0 to 9.
         * @example $this->$NumberText[0][3] -> שלוש
         */
        array('אפס','אחת','שתיים','שלוש','ארבע','חמש','שש','שבע','שמונה','תשע'),
        
        /**
         * The translations words to the numbers in range of 10 to 19.
         * @example $this->$NumberText[1][3] -> שלוש-עשרה
         */
        array('עשר','אחת-עשרה','שתים-עשרה','שלוש-עשרה','ארבע-עשרה','חמש-עשרה','שש-עשרה','שבע-עשרה','שמונה-עשרה','תשע-עשרה'),
        
        /**
         * The translations words to the numbers in range of 0 to 90, difference of 10 between each element.
         * E.g. 0,10..
         * @example $this->$NumberText[2][3] -> שלושים
         */
        array('אפס','עשר','עשרים','שלושים','ארבעים','חמישים','שישים','שבעים','שמונים','תשעים'),
        
        /**
         * The translations words to the numbers in range of 100 to 900, difference of 100 between each element.
         * E.g. 100,200..
         * *First element needs to be empty.
         * @example $this->$NumberText[3][3] -> שלוש מאות
         */
        array('','מאה','מאתיים','שלוש מאות','ארבע מאות','חמש מאות','שש מאות','שבע מאות','שמונה מאות','תשע מאות'),
    );
    
    
    /**
     * An array that contains for each element the name of each 3 digits in the number according to the European numeration.
     * For more levels, just add to this array more data from here(European numeration)&#1509:
     * @link http://en.wikipedia.org/wiki/Names_of_large_numbers
     */
    private $Level = array(
        /**
         * An array for the first 3 digits that has not group name. like milion.
         * First element is empty because there is no group name.
         */
        array(''),
        
        /**
         * The translations words to the numbers in range of 1000 to 9000, difference of 1000 between each element.
         * E.g. 1000,2000..
         * @example $this->$Level[1][3] -> שלושת אלפים
         */
        array('אלף','אלפיים','שלושת אלפים','ארבעת אלפים','חמשת אלפים','ששת אלפים','שבעת אלפים','שמונת אלפים','תשעת אלפים'),
        
        /**
         * The translation word to the number with 6 zeros (European numeration: Million).
         */
        array('מיליון'),
        
        /**
         * The translation word to the number with 9 zeros (European numeration: Milliard).
         */
        array('מיליארד'),
        
        /**
         * The translation word to the number with 12 zeros (European numeration: Billion).
         */
        array('ביליון'),
        
        /**
         * The translation word to the number with 15 zeros (European numeration: Billiard).
         */
        array('ביליארד'),
        
        /**
         * The translation word to the number with 18 zeros (European numeration: Trillion).
         */
        array('טריליון'),
        
        /**
         * The translation word to the number with 21 zeros (European numeration: Trilliard).
         */
        array('טריליארד')
    );
    
    
    private $and_letter = 'ו';
    private $minus_sign = '-';
    private $minus_word = 'מינוס';
    private $group_seperator = ',';
    
    /**
     * This is a static varible that`s collecting existing numbers.
     * @static 
     */  
    private static $Number3DigitsFormated = array();
    
    /**
     * This is a static varible that`s saving every number that has 3 digits to make the script run faster.
     * @static 
     */
    private static $Save3DigitsData = array();
    
    
    /**
     * A construct method that get a number or by default number equals to 0,
     * checks if it is a valid number, if NOT - sets it to 0.
     * In addition, uses the function bcmul() for bigger numbers.
     * Sets if the number is negative or not.
     * @see isValid()
     * @param int $Number   Number that will be translated to words.
     */
    function __construct($Number = 0)
    {
        if(!$this->isValid($Number))
            $Number = 0;
        $this->Number = bcmul(strval($Number),'1');
        $this->IsNegative = ($Number < 0);
    }
    
    /**
     * A valid method for validating number range.
     * @param int $Number   Number that will be checked if it`s a valid number.
     * @return bool         True if valid, false otherwise.
     */
    function isValid($Number)
    {
        return (is_numeric($Number) && $Number >= -9999999999999999999999 && $Number <= 9999999999999999999999);
    }
    
    
    /**
     * The method sets for each key
     * @param array $Current    Contains a max of 3 digits (group)
     * @param int $key          Specify the index of the current group of the number.
     */
    function arrayFormated($current, $key)
    {
        //$current = array_reverse($current);
        self::$Number3DigitsFormated[$key] = implode('', $current);
    }
    
    
    /**
     * The method sets the current number as an array and prepares the number to be translated.
     * @example $this->Number = 12345
     *          Than the final array($this->NumberAsArray) will be:
     *          array(
     *          0 => array(0 => 1, 1 => 2),
     *          1 => array(0 => 3, 1 => 4, 2 => 5)
     *          );
     */
    private function SetNumberAsArray()
    {   
        //$number = strval($this->Number);
        //$numberReverse = strrev($number);
        
        $length = strlen($this->Number);
        $arrNumbers = str_split($this->Number);
        
        if($length <= 3)
            $this->NumberAsArray = array(array_slice($arrNumbers, 0, $length));    
        else
        {
            $rest = $length % 3;
            $this->NumberAsArray = array_merge(array(array_slice($arrNumbers, 0, $rest)),
            array_chunk(array_slice($arrNumbers, $rest, $length - $rest), 3));
        }
        
        array_walk($this->NumberAsArray, array(__CLASS__, 'arrayFormated'));
        //$this->number_format($number, true);
        /*
        $NumberArray = array_chunk(str_split($numberReverse), 3);
        
        array_walk($NumberArray, array(__CLASS__, 'arrReverse2d'));
        
        $this->NumberAsArray = array_reverse($NumberArray);
        */
    }
    
    /**
     * The method get a group of 3 digit MAX and checks if all the elements equals or less than 0.
     * @param array $ArrayNumber
     * @return int              0 if one of none-last elements is bigger than 0, otherwise - the value of the last element.
     */
    private function IsOnlyItDigitOne($ArrayNumber)
    {
        $sizeArr = sizeof($ArrayNumber);
        for($i = 0;$i < $sizeArr-1;$i++)
            if($ArrayNumber[$i] > 0)
                return 0;
        
        return $ArrayNumber[$sizeArr-1];
    }
    
    /**
     * The method sets an array that contains the parts of the translated for each group.
     * Max Group number is 3 digit.
     * @param bool $flagCalculatePart   Default: true -> calculating the data,
     *                                  when sets to false -> uses data that have been saved before.
     * @return array $strArray          Array that contains the parts of the tranlations. 
     */
    private function GetNumberAsText($flagCalculatePart = true)
    {
        $strArray = array();
        $index = 0;
        foreach($this->NumberAsArray as $NumberLevel)
        {
            if(!$flagCalculatePart && array_key_exists(self::$Number3DigitsFormated[$index], self::$Save3DigitsData))
            {
                $strArray[$index] = self::$Save3DigitsData[self::$Number3DigitsFormated[$index]];
                $index++;
                continue;
            }
            
            $strArray[$index] = array();
            $size = sizeof($NumberLevel);
            if($size == 3)
            {
                if($NumberLevel[0] > 0)
                    $strArray[$index][] = $this->NumberText[3][$NumberLevel[0]];
                if($NumberLevel[1] == 1)
                    $strArray[$index][] = ' '.$this->and_letter.$this->NumberText[1][$NumberLevel[2]];
                else
                {
                     if($NumberLevel[1] > 1)
                        $strArray[$index][] = $this->NumberText[2][$NumberLevel[1]];
                    
                    if($NumberLevel[2] > 0)
                        $strArray[$index][] = $this->and_letter.$this->NumberText[0][$NumberLevel[2]];
                }
            }
            else if($size == 2)
            {
                if($NumberLevel[0] == 1)
                    $strArray[$index][] = $this->NumberText[1][$NumberLevel[1]];
                else
                    $strArray[$index][] = $this->NumberText[2][$NumberLevel[0]].(($NumberLevel[1] > 0) ? ' '.$this->and_letter.$this->NumberText[0][$NumberLevel[1]] : '');
            }
            else
                $strArray[$index][] =  $this->NumberText[0][$NumberLevel[0]];//$strArray[$index][] =  (($index > 0) ?  ($this->and_letter.' ') : '').$this->NumberText[0][$NumberLevel[0]];
            
            if(!$flagCalculatePart)
                self::$Save3DigitsData[self::$Number3DigitsFormated[$index]] = $strArray[$index];
            $index++;
        }
        return $strArray;
    }
    
    
    /**
     * Final action, the method build the final result which setting the group level (Ex. Million).
     * @param array $strArray       Array that contains the parts of the translations of each group.
     * @see GetNumberAsText()
     * @param bool $flagComaState   True for coma formated (in words), flase for none comas.
     * @return string $String       The completely translated number.
     */
    private function BuildNumberAsText($strArray, $flagComaState)
    {
        $String = '';
        $flagComa = false;
        
        for($i = 0, $sizeGroups = sizeof($strArray);$i < $sizeGroups;$i++)
        {
            $flagComa = false;
            $OneDigit = $this->IsOnlyItDigitOne($this->NumberAsArray[$i]);
            
            if(($sizeGroups-$i-1 == 1 && $OneDigit > 0) || ($sizeGroups-$i-1 > 1 && $OneDigit == 1))
                unset($strArray[$i][0]);
                
            foreach($strArray[$i] as $GrNumber)
                    $String .= ' '.$GrNumber;
            
            if(array_sum($this->NumberAsArray[$i]) > 0)
                $String .= ' '.$this->Level[$sizeGroups-$i-1][($sizeGroups-$i-1 == 1 && $OneDigit > 0) ? $OneDigit-1 : 0];
            
            if(sizeof($this->NumberAsArray[$i]) > 1 && array_sum($this->NumberAsArray[$i]) > 0 && $flagComaState)
            {
                $String .= $this->group_seperator.' ';
                $flagComa = true;
            }
        }
        
        if($flagComa)
            $String = substr($String, 0, strlen($String) - 2);
        
        if($this->IsNegative)
            $String = $this->minus_word.$String;
        
        
        return trim(str_replace(' '.$this->group_seperator.' ',' ',$String));
    }
    
    /**
     * The method conuts the values that are in string type in the array2d.
     * @param array @array2d    Array that contains the parts of the translated for each group.
     * @see GetNumberAsText()
     * @return int $count       The number of the existings values as strings. 
     */
    private function getNumContainsStrings($array2d)
    {
        $count = 0;
        foreach($array2d as $array)
            foreach($array as $value)
                if(is_string($value) && !empty($value))
                    $count++;
        return $count;
    }
    
    /**
     * The method is managing all the translations actions and sets the final translation numberr.
     * @param bool $flagCalculatePart   Default: true -> calculating the data,
     *                                  when sets to false -> uses data that have been saved before.
     * @return object $this             The current instance of the class.
     */
    function NumberToWords($flagCalculatePart = true)
    {
        if($this->IsNegative)
            $this->Number = bcmul($this->Number, '-1');
            
        $this->SetNumberAsArray();
        $NumberAsText = $this->GetNumberAsText($flagCalculatePart);
        $String = $this->BuildNumberAsText($NumberAsText, ($this->getNumContainsStrings($NumberAsText) > 1));
        $this->NumberAsWords = array(true => $String, false => str_replace($this->group_seperator,'',$String));
        
        if($this->IsNegative)
            $this->Number = bcmul($this->Number, '-1');
        return $this;
    }
    
    /**
     * The method return the number formatted as words with comas or without.
     * @param bool $FlagComa                            Default: true -> to return tranlation with comas,
     *                                                  otherwise to return without comas.
     * @return string $this->NumberAsWords[$FlagComa]   if exists, if not -> '--'
     */
    function getNumberAsWords($FlagComa = true)
    {
        return ((sizeof($this->NumberAsWords) > 0) ? $this->NumberAsWords[$FlagComa] : '--');
    }
    
    /**
     * The method gets a number and return a formatted number with comas.
     * @param string $number
     * @return string $newNumber    The formatted number.
     */
    function number_format($number){
        $number = (string) $number;
        $len = strlen($number);
        
        $newNumber = '';
        for ($i = 0; $i < $len; ++$i)
        {
            if (($i % 3 == 0) && $i)
                $newNumber = ','.$newNumber;
            $newNumber = $number[$len - $i - 1].$newNumber;
        }
        return $newNumber;
    }
    
    
    /**
     * The method returns the get number as formatted or not.
     * @param int $flagFormat   Default: false -> none-format number, true -> with comas.
     * @return string           The number formatted or not.
     */
    function getNumber($flagFormat = false)
    {
        if(!$flagFormat)
            return $this->Number;
        
        if($this->IsNegative)
            return $this->minus_sign.$this->number_format(bcmul($this->Number, '-1'));
        return $this->number_format($this->Number);
        //return (($flagFormat) ? $this->number_format($this->Number) : $this->Number);
    }
    
    /**
     * The method sets a new number and sets the defualts settings as starts.
     * @param int $Number               the new number.
     * @param bool $toWords             sets by default to false -> not calling the translation action.
     *                                  if true -> calling the translations action.
     * @param bool $flagCalculatePart   default: true -> calculating the data,
     *                                  when sets to false -> uses data that have been saved before.
     * @return object $this             The current instance of the class.
     */
    function setNumber($Number, $toWords = false, $flagCalculatePart = true)
    {
        if(!$this->isValid($Number))
            $Number = 0;
        $this->Number = bcmul(strval($Number),'1');
        $this->IsNegative = ($Number < 0);
        $this->NumberAsArray = array();
        $this->NumberAsWords = array();
        
        if($toWords)
            $this->NumberToWords($flagCalculatePart);
        return $this;
    }
    
    /**
     * The magic method called when instance of has been used as string.
     * @return string   A display of the final result: number and translated number.
     */
    function __toString()
    {
        $InFormat = number_format($this->Number);
        $InWords = $this->getNumberAsWords();
        return  <<<EOF
            :מספר <br />{$InFormat}<br /><br />:במילים <br />{$InWords}<br /><br />
EOF;

    }
}

?>		
