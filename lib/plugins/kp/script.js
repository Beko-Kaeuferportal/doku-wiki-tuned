jQuery(function($){
  var support_menu_selector = '#dindexmenu_20070331535335832d84f0b202';
  var suport_mail = 'it_support@kaeuferportal.de';

  if (!window.hideSidebar) return;

  //support_menu_selector = '#dindexmenu_5839539815335925eaf77439';//id=techniken

  if($('a.login').length){
    //add other js contend here for usage in the hole wiki
    $('.dTreeNode').children().not(support_menu_selector).hide();

    $('.dTreeNode:first').html($('<a class="support" href="doku.php?id=support">&lt;IT-Support&gt;</span>'));
  }
});
