<?php
/**
 * WYSIWYG plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Luke Howson <mail@lukehowson.com>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
include_once(DOKU_PLUGIN."wysiwyg/fckeditor/fckeditor.php");
include_once(DOKU_PLUGIN."wysiwyg/hider.php");

$__wysiwyg_global_hasToolbar = false;

class syntax_plugin_wysiwyg extends DokuWiki_Syntax_Plugin {
  
  function getSort(){ return 15; }
  
  /**
   * Connect pattern to lexer
   */
  function connectTo($mode) {
    $this->Lexer->addSpecialPattern('<wysiwyg.*?'.$wysiwyg_GUID.'<<<<< />',$mode,'plugin_wysiwyg');
  }
    
  /**
   * General Info
   *
   * Needs to return a associative array with the following values:
   *
   * author - Author of the plugin
   * email  - Email address to contact the author
   * date   - Last modified date of the plugin in YYYY-MM-DD format
   * name   - Name of the plugin
   * desc   - Short description of the plugin (Text only)
   * url    - Website with more information on the plugin (eg. syntax description)
   */
  function getInfo(){
    return array(
        'author' => 'Luke Howson',
        'email'  => 'mail@lukehowson.com',
        'date'   => '2007-07-23',
        'name'   => 'PageTemplate Plugin',
        'desc'   => "links to a page. If the specified page doesn't exist, it creates a templated version",
        );
  }
  
  /**
   * Syntax Type
   *
   * Needs to return one of the mode types defined in $PARSER_MODES in parser.php
   */
  function getType(){ return 'container';}
  
  /**
   * Allowed Mode Types
   *
   * Defines the mode types for other dokuwiki markup that maybe nested within the
   * plugin's own markup. Needs to return an array of one or more of the mode types
   * defined in $PARSER_MODES in parser.php
   */
  function getAllowedTypes() {
  return array(); }
  
  /**
   * Paragraph Type
   *
   * Defines how this syntax is handled regarding paragraphs. This is important
   * for correct XHTML nesting. Should return one of the following:
   *
   * 'normal' - The plugin can be used inside paragraphs
   * 'block'  - Open paragraphs need to be closed before plugin output
   * 'stack'  - Special case. Plugin wraps other paragraphs.
   *
   * @see Doku_Handler_Block
   */
  function getPType(){
    return 'block';
  }
  
  function handle($match, $state, $pos, &$handler){ return array($match, $state, $pos); }
  function render($mode, &$renderer, $data){
    global $__wysiwyg_global_hasToolbar;         
    if ($mode != "xhtml") return true;
    global $USERINFO, $ID, $wysiwyg_GUID;
    preg_match("/<wysiwyg ([^\\s]*) >>>>>".wysiwyg_GUID."(.*?)".wysiwyg_GUID."<<<<< \\/>/",$data[0],$matches);
    $html = $matches[2];
    $name = $matches[1];
    if (!$__wysiwyg_global_hasToolbar) {
      $renderer->doc .= '<div id="wysiwyg_adobe_style_toolbar" class="wysiwyg_hidden"> </div>';
      $__wysiwyg_global_hasToolbar = true;
    }
    $renderer->doc .= '<div id="wysiwyg_view_'.$name.'" class="wysiwyg_view_'.$name.' wysiwyg_view">'.$html;
    $renderer->doc .= '<div><a onhover="wysiwyg_highlight(\''.$name.'\');" onmouseover="wysiwyg_highlight(\''.$name.'\');" onmouseout="wysiwyg_unhighlight(\''.$name.'\');" onclick="wysiwyg_edit(\''.$name.'\');" class="wysiwyg_edit_button wysiwyg_hidden wysiwyg_button button">edit</a></div>';
    $renderer->doc .= '</div>';
    $renderer->doc .= '<form onsubmit="return wysiwyg_save(\''.$name.'\');" method="post" action="" id="wysiwyg_editor_'.$name.'"  class="wysiwyg_editor_'.$name.' wysiwyg_initially_hidden">';
    $renderer->doc .= $this->_editor($name, $html);
    $renderer->doc .= '<div><a onclick="wysiwyg_quit(\''.$name.'\');" class="wysiwyg_button button"> quit&nbsp;</a>';
    $renderer->doc .= '<a onclick="wysiwyg_save(\''.$name.'\');" class="wysiwyg_button button"> &nbsp;save </a></div>';
    $renderer->doc .= "</form>";
    return true;
  }
  
  function _editor($name, $html) {
    $oFCKeditor = new FCKeditor($name) ;
    $oFCKeditor->BasePath = DOKU_BASE."lib/plugins/wysiwyg/fckeditor/";
    $oFCKeditor->Value = $html;
    $oFCKeditor->ToolbarSet = 'dokuwiki';
    $oFCKeditor->Height = '150';
    return $oFCKeditor->CreateHtml() ;
  }
}
//Setup VIM: ex: et ts=4 enc=utf-8 :
