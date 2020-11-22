<?php

/**
 * @author Matan
 * @email matanfxp@hotmail.co.il
 * @date 16/4/2014
 * @time 16:53
 * @file index.php
 * @copyright 2014
 */
 

function HandleForm()
{
    
    $DefaultMessage = '<br />';
    if(!isset($_POST['myNumber']) || empty($_POST['myNumber']))
    {
        $_POST['myNumber'] = 0;
        $DefaultMessage = '<br />- הוגדר כמספר ברירת המחדל: 0.';
    }
        //return '&#1502;&#1510;&#1496;&#1506;&#1512;, &#1488;&#1498; &#1488;&#1497;&#1504;&#1497; &#1497;&#1499;&#1493;&#1500; &#1500;&#1511;&#1489;&#1500; &#1506;&#1512;&#1498; &#1512;&#1497;&#1511;.';
       
    require_once 'numbertowords.class.php';
    
    $Number = new NumberToWords($_POST['myNumber']);
    
    $MyNumberToWords = $Number->NumberToWords()->getNumberAsWords();
    $MyNumber = $Number->getNumber(true);
    
    return array('Number' => $Number->getNumber(),'Message' => <<<EOF
        {$DefaultMessage}<br /><br />
        <table style="font-size: 10pt;">
            <tr>
                <td>המספר:</td>
                <td style="color: rgb(122, 0, 0);direction: ltr;position: absolute;">{$MyNumber}</td>
            </tr>
            <tr>
                <td>במילים:</td>
                <td style="color: rgb(81, 123, 0);">{$MyNumberToWords}</td>
            </tr>
        </table>
EOF
    );
}

$LastNumber = 0;

$time_start = microtime(true); 

$FormMessage = HandleForm();

if(is_array($FormMessage))
{
    $LastNumber = $FormMessage['Number'];
    $FormMessage = $FormMessage['Message'];
}


$time_end = (microtime(true) - $time_start);

echo <<<EOF

<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>תרגום מספר למילים</title>
</head>
<body>
        <div style="font-family: verdana;direction: rtl;">
            <form action="?send" method="POST">
                <table>
                    <tr>
                        <td><label for=myNumberID" style="color: rgb(65, 153, 198);">אנא הזן את מספרך ;)</label></td>
                        <td><input type="text" name="myNumber" id="myNumberID" maxlength="23" value="{$LastNumber}" size="22" autocomplete="off" placeholder="Example: 7429645"/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="direction: ltr;position: absolute;font-size: 9pt;margin-top: 6px;">טווח -> &plusmn;9,999,999,999,999,999,999,999</td>
                    </tr>
                </table>
                <div id="HoldingSubmit"><input type="submit" name="SubNum" value="הפוך למילים" /></div>
            </form>
        
            <div id="FormMessages">
            - <span style="color: rgb(156, 81, 42);">אפשר להמיר גם מספרים שלילים.</span>
            {$FormMessage}
            </div>
        </div><br /><br />
        <div style="text-align: right;">
            <span style="font-weight: bold;">סה"כ זמן ביצוע בשניות:</span> {$time_end}
            <div style="text-align: center;font-size: 15pt;margin-top: 200px;">
                <span style="color: green;">
                (;</span> על התיכנות (<a href="http://www.fxp.co.il/member.php?u=496847" style="text-decoration: none;color: rgb(91, 28, 0);font-family: verdana;" target="_blank">Activity</a>) כל הזכויות שמורות <a href="mailto:matanfxp@hotmail.co.il" style="text-decoration: none;color: rgb(79, 159, 255);font-family: verdana;font-weight: bold;">למתן</a>
                </span> &copy;
            </div>
        </div>
</body
</html>
EOF;
