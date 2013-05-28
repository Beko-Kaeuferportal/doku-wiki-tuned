<?php

/* Hides the wysiwyg markup from DW editor */

if(!defined('wysiwyg_GUID')) define('wysiwyg_GUID','33008AD8-D1B7-4739-A27F-FB64353077D1');

function get_wysiwyg($name, $markup) {
  preg_match("/<wysiwyg ".$name." >>>>>".wysiwyg_GUID."(.*?)".wysiwyg_GUID."<<<<< \\/>/",$markup,$matches);
  return $matches[1];
}
  
function save_wysiwyg($pickle, $markup) {
  // put back the wysiwyg bits
  if(isset($pickle)) {
    foreach ($pickle as $name) {
      foreach ($name as $key => $html) {
        $markup = insert_wysiwyg($key, $html, $markup);
      }
    }
  }
  // add the >>>> <<<< bits to new markup
  $markup = preg_replace("/(?<=<wysiwyg )([^\\s]*) (?=\\/>)/","$1 >>>>>".wysiwyg_GUID."Edit me!".wysiwyg_GUID."<<<<< ",$markup);
  return $markup;
}

function load_wysiwyg($markup) {
  // take out the wysiwyg bits
  $allPickled = array();
  $names = nameList_wysiwyg($markup);
  foreach($names as $name) {
    $pickle = array();
    $pickle[$name] = get_wysiwyg($name, $markup);
    $allPickled[] = $pickle;
    $markup = hide_wysiwyg($name, $markup);
  }
  $ret = array();
  $ret['markup'] = $markup;
  $ret['pickle'] = $allPickled;
  return $ret;
}

function hide_wysiwyg($name, $markup) {
  $ret = preg_replace("/(?<=<wysiwyg\\s".$name.") >>>>>".wysiwyg_GUID.".*?".wysiwyg_GUID."<<<<< (?=\\/>)/", " ", $markup);  
  return $ret;
}

function insert_wysiwyg($name, $html, $markup) {
  return preg_replace("/(?<=<wysiwyg ".$name.") (?=\\/>)/", " >>>>>".wysiwyg_GUID.$html.wysiwyg_GUID."<<<<< ", $markup);
}

function nameList_wysiwyg($markup) {
  $nameList = array();
  preg_match_all("/<wysiwyg ([^\\s]*).*?\\/>/",$markup,$matches);
  foreach ($matches[1] as $match) {
    $nameList[] = $match;
  }
  return $nameList;
}

?>