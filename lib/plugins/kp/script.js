jQuery(function($){
  var support_menu_selector = '#dindexmenu_20070331535335832d84f0b202';
  var suport_mail = 'it_support@kaeuferportal.de';

  if($('a.login').length){
    //add other js contend here for usage in the hole wiki
    $('.dTreeNode').children().not(support_menu_selector).hide();
    $('.dTreeNode:first').html($('<a href="mailto:'+suport_mail+'">&lt;IT-Support&gt;</a>'));
  }
});
