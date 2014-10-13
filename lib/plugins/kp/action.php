<?php
/**
 * KP Wiki Mod
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Käuferportal <it@kaeuferportal.de>
 */

if(!defined('DOKU_INC')) die();

class action_plugin_kp extends DokuWiki_Action_Plugin {
  static $PUBLIC_WIKI_ID_MATCHER  = '/.*id=support.*/i';
  static $PUBLIC_WIKI_ALLOWED_IPS = ['217.110.41.43', '127.0.0.1'];

  public function __construct(){
    $this->ensureHttps(); //can be done every time
  }

  public function register(Doku_Event_Handler &$controller) {
    //hook into befor show action for checking access rights
    $controller->register_hook('TPL_CONTENT_DISPLAY', 'BEFORE', $this, 'checkPublicDokuOnlyIntern');
  }

  private function ensureHttps(){
    if(!isset($_SERVER['HTTPS'])
    || $_SERVER['HTTPS'] == ""
    || $_SERVER['HTTPS'] == "off"){
      $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
      header("Location: $redirect");
    }
  }

  public function checkPublicDokuOnlyIntern(Doku_Event &$event, $param){
    if($this->isLoggedIn())
      return ;

    if($this->isPublicSupportArea()
    && !$this->isHostAllowedToAccesPublicSupportArea()){
      $event->preventDefault();
      html_login();
    }
  }

  private function isPublicSupportArea(){
    return preg_match(self::$PUBLIC_WIKI_ID_MATCHER, $_SERVER['REQUEST_URI']);
  }

  private function isHostAllowedToAccesPublicSupportArea(){
    return in_array($this->userIp(), self::$PUBLIC_WIKI_ALLOWED_IPS);
  }

  private function userIp(){
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      return $_SERVER['HTTP_X_FORWARDED_FOR'];

    return $_SERVER['REMOTE_ADDR'];
  }

  private function isLoggedIn(){
    return (isset($_SERVER['REMOTE_USER']) && $_SERVER['REMOTE_USER'] !== null);
  }
}
