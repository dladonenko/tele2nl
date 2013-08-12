<?php 

// Pretty print a nested deep var 
// (such as output from print_e($v,true) 
function prettyPrintVar($name,$data)
{
    $captured = preg_split("/\r?\n/",$data);
    print "<script>function toggleDiv(num){
      var span = document.getElementById('d'+num);
      var a = document.getElementById('a'+num);
      var cur = span.style.display;
      if(cur == 'none'){
        a.innerHTML = '-';
        span.style.display = 'inline';
      }else{
        a.innerHTML = '+';
        span.style.display = 'none';
      }
    }</script>";
    print "<b>$name</b>\n";
    print "<pre>\n";
    foreach($captured as $line)
    {
        print debug_colorize_string($line)."\n";
    }
    print "</pre>\n";
}

function next_div($matches)
{
  static $num = 0;
  ++$num;
  return "$matches[1]<a id=a$num href=\"javascript: toggleDiv($num)\">+</a><span id=d$num style=\"display:none\">(";
}

/**
* colorize a string for pretty display
*
* @access private
* @param $string string info to colorize
* @return string HTML colorized
* @global
*/
function debug_colorize_string($string)
{
    $string = preg_replace("/\[(\w*)\]/i", '[<font color="red">$1</font>]', $string);
    $string = preg_replace_callback("/(\s+)\($/", 'next_div', $string);
    $string = preg_replace("/(\s+)\)$/", '$1)</span>', $string);
    /* turn array indexes to red */
    /* turn the word Array blue */
    $string = str_replace('Array','<font color="blue">Array</font>',$string);
    /* turn arrows graygreen */
    $string = str_replace('=>','<font color="#556F55">=></font>',$string);
    return $string;
}
 
