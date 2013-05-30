<?php
/**
 * KP Wiki Mod
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Käuferportal <it@kaeuferportal.de>
 */

if(!defined('DOKU_INC')) die();

class action_plugin_kp extends DokuWiki_Action_Plugin {
	public function __construct(){
		//parent::__construct(); -> no parent constructor
		
		$this->ensureHttps();
	}
	
    public function register(&$controller) {
    	//override for implement
    }
    
    public function ensureHttps(){
    	if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "" || $_SERVER['HTTPS'] == "off"){
    		$redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    		header("Location: $redirect");
    	}
    }
}
