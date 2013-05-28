<?php
/**
 * WYSIWYG Action Plugin:   Hides the HTML from the Dokuwiki editor.
 * 
 * @author     Luke Howson <mail@lukehowson.com>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');
require_once(DOKU_INC.'inc/parserutils.php');
require_once(DOKU_INC.'inc/search.php');
require_once(DOKU_INC.'inc/parser/parser.php');
require_once('hider.php');

class action_plugin_wysiwyg extends DokuWiki_Action_Plugin {
  
  /**
   * return some info
   */
  function getInfo(){
    return array(
        'author' => 'Luke Howson',
        'email'  => 'mail@lukehowson.com',
        'date'   => '2007-09-7',
        'name'   => 'WYSIWYG',
        'desc'   => 'Hides the WYSIWYG html from the DW editor.',
        );
  }
  
  /*
   * Register its handlers with the dokuwiki's event controller
   */
  function register(&$controller) {
    // hider:
    $controller->register_hook('IO_WIKIPAGE_READ', 'AFTER',  $this, '_hideHTML');
    $controller->register_hook('IO_WIKIPAGE_WRITE', 'BEFORE',  $this, '_insertHTML');
    
    // a javascript variable
    $controller->register_hook('TPL_METAHEADER_OUTPUT', 'AFTER',  $this, '_insertID');
    
    // page creation (for wysiwyg group members)
    $controller->register_hook('HTML_PAGE_FROMTEMPLATE', 'BEFORE',  $this, '_createPage');
  }
  
  function _insertID(&$event, $param) {
    echo '<script type="text/javascript"> var ID = "'.getID().'"; </script>';
    return;
  }  
  function _hideHTML(&$event, $param) {
    if($_REQUEST['do'] == 'edit') {
      // hide the HTML
      $ret = load_wysiwyg($event->result);
      @session_start();
      $_SESSION['wysiwyg'] = $ret['pickle'];
      @session_write_close();
      $event->result = $ret['markup'];
    }
    return $event;
  }
  
  function _insertHTML(&$event, $param) {
    // replace the HTML (if missing)
    if ($event->data[3]) return $event;
    @session_start();
    $event->data[0][1] = save_wysiwyg($_SESSION['wysiwyg'], $event->data[0][1]);
    $_SESSION['wysiwyg'] = null;
    @session_write_close();
    return $event;
  }
  
  function isMemberOfwysiwyg() {
    global $INFO;
    // not logged in
 		if(!is_array($INFO['userinfo']['grps'])) {
			return false;
		}
    // is a member of wysiwyg?
    $groups = $INFO['userinfo']['grps'];
    if(array_search("wysiwyg", $groups) ) {
      return true;
    }
    return false;
  }
  
  function _createPage(&$event, $param) {
    if(!$this->isMemberOfwysiwyg()) {
      return $event;
    }
    // create the new page
    $page = getID();
    $newPage = wikiFN($page);
    $myTemplate = realpath(DOKU_PLUGIN."wysiwyg/defaultPage.txt");
    io_makeFileDir($newPage);
    copy($myTemplate, $newPage);

    // redirect to the new page
    $this->redirect($page);
    exit(0);
    return $event;
  }

  function redirect($loc)
  {
    $myPath = getBaseURL();
    $myServer = $_SERVER['HTTP_HOST'];
    echo '<script type="text/javascript">';
    echo 'window.location.replace("http://'.$myServer.$myPath.'doku.php?id='.$loc.'");';
    echo '</script>';
    return;
  }
}

?>