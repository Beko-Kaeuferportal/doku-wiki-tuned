<?php
/**
 * AJAX Backend for wysiwyg
 *
 * @author Luke Howson <mail@lukehowson.com>
 * @license     GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

// TODO:
//  - robust getID in ajax.php

//fix for Opera XMLHttpRequests
if(!count($_POST) && @$HTTP_RAW_POST_DATA){
  parse_str($HTTP_RAW_POST_DATA, $_POST);
}
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'inc/common.php');
require_once(DOKU_INC.'inc/events.php');
require_once(DOKU_INC.'inc/pageutils.php');
require_once(DOKU_INC.'inc/html.php');
require_once(DOKU_INC.'inc/auth.php');
require_once(DOKU_INC.'inc/actions.php');
require_once(DOKU_INC.'inc/io.php');
require_once(DOKU_INC.'inc/infoutils.php');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
//close session
session_write_close();

$ajax_indexmenu=new ajax_wysiwyg_plugin;
$action  = $_REQUEST['action']; 
if ($action == 'save') {
  $ajax_indexmenu->save();
}
if ($action == 'edit') {
  $ajax_indexmenu->edit();
}
if ($action == 'quit') {
  $ajax_indexmenu->quit();
}
if ($action == 'checkPerms') {
  $ajax_indexmenu->checkPerms();
}

class ajax_wysiwyg_plugin {
  /**
   * Save
   *
   * @author Luke Howson <mail@lukehowson.com>
   */ 

  var $newPage;

  function quit() {
    global $ID;
    $ID    = getID();
    $ID = cleanID($_REQUEST['id']);
    echo "success";    
    unlock($ID);
  }

  function save () {
    global $ID;
    global $USERINFO;
    $QUERY = trim($_REQUEST['id']);
    $ID    = getID();
    $NS    = getNS($ID);
    $name  = $_REQUEST['name']; 
    $html  = urldecode($_REQUEST['html']);
    $temp  = $_COOKIE;
    $ID = cleanID($_REQUEST['id']);
    //check permissions
    $ACT = act_permcheck($ACT);
    $user = $_SERVER['REMOTE_USER'];
    $groups = $USERINFO['grps'];
    $aclLevel = auth_aclcheck($ID,$user,$groups);
    if ($aclLevel >= AUTH_EDIT) {
      $fileName = wikiFN($ID);
      $this->newPage = io_readFile($fileName);
      $this->newPage = hide_wysiwyg($name, $this->newPage);
      $this->newPage = insert_wysiwyg($name, $html, $this->newPage);
      $this->savePage();
      header('Content-Type: text/html; charset=utf-8');
      header('Cache-Control: public, max-age=3600');
      header('Pragma: public');
      $temp = $_REQUEST;
      echo "success";
    }
    else echo "failure";
    return;
  }
  
  function savePage () {
    global $ID;
    global $DATE;
    global $PRE;
    global $TEXT;
    global $SUF;
    global $SUM;
    
    //spam check
    if(checkwordblock())
      return 'wordblock';
    //conflict check //FIXME use INFO
    if($DATE != 0 && @filemtime(wikiFN($ID)) > $DATE )
      return 'conflict';
    
    //save it
    saveWikiText($ID,con($PRE,$this->newPage,$SUF,1),$SUM,$_REQUEST['minor']); //use pretty mode for con
    //unlock it
    unlock($ID);
    
    //delete draft
    act_draftdel($act);
  }
  
  function edit() {
    global $ID;
    global $USERINFO;
    $user = $_SERVER['REMOTE_USER'];
    $groups = $USERINFO['grps'];
    $aclLevel = auth_aclcheck($ID,$user,$groups);
    if ($aclLevel <  AUTH_EDIT) {
      echo "noperms";
      return;
    }
    $QUERY = trim($_REQUEST['id']);
    $ID    = getID();
    $lock = wikiLockFN($ID);
    if(!($locker = checklock($ID))) {
      if($_SERVER['REMOTE_USER']){
        $success = io_saveFile($lock,$_SERVER['REMOTE_USER']);
      }else{
        $success = io_saveFile($lock,clientIP());
      }
      if ($success ) echo "success";
    }
    else echo "locked:".$locker;
    return;
  }
  
  function checkPerms() {
    global $ID;
    global $USERINFO;
    $QUERY = trim($_REQUEST['id']);
    $ID    = getID();
    $user = $_SERVER['REMOTE_USER'];
    $groups = $USERINFO['grps'];
    $aclLevel = auth_aclcheck($ID,$user,$groups);
    if ($aclLevel <  AUTH_EDIT) {
      echo "noperms";
    }
    else {
      echo "success";
    }
    return;
  }
}
