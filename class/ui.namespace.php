<?php namespace UI;
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * boolean_word
 * Take a T/F value and return a pretty response
 */
function boolean_word($boolean,$string='') { 

  if ($string == '') { 
    $string = $boolean ? 'True' : 'False';
  }

  if ($boolean) { 
    return '<span class="label label-success">' . $string . '</span>';
  }
  else {
    return '<span class="label label-important">' . $string . '</span>';
  }

  return false; 

} // boolean_word
?>
