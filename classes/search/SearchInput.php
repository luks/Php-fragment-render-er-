<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SearchInput extends MainFragment {
  
  public function generate($value=null) {
    
    nct_assign('value', $value);   
    $html = nct_fetch('fragments/search/input.tpl', true);
    return $html;
  }
  
}


?>
