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
    //parent::__construct(); -> no parent constructor

    $this->ensureHttps();
    $this->checkPublicDokuOnlyIntern();
  }

  public function register(&$controller) {
    //override for implement
  }

  public function ensureHttps(){
    if(!isset($_SERVER['HTTPS'])
    || $_SERVER['HTTPS'] == ""
    || $_SERVER['HTTPS'] == "off"){
      $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
      header("Location: $redirect");
    }
  }

  public function checkPublicDokuOnlyIntern(){
    if( $this->isNotPublicSupportArea()
    && !$this->isHostAllowedToAccesNotPublicSupportArea())
      header('Location: doku.php?not_allowed_from_public=1');
  }

  private function isNotPublicSupportArea(){
    return preg_match(self::$PUBLIC_WIKI_ID_MATCHER, $_SERVER['REQUEST_URI']);
  }

  private function isHostAllowedToAccesNotPublicSupportArea(){
    return in_array($this->userIp(), self::$PUBLIC_WIKI_ALLOWED_IPS);
  }

  private function userIp(){
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      return $_SERVER['HTTP_X_FORWARDED_FOR'];

    return $_SERVER['REMOTE_ADDR'];
  }
}
