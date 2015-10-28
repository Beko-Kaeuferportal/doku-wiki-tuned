jQuery(function($){
  var suport_mail = 'it_support@kaeuferportal.de';
  var menusToKeep = ['support', 'vertrieb'];

  //support_menu_selector = '#dindexmenu_5839539815335925eaf77439';//id=techniken

  if($('a.login').length){
    $('#dokuwiki__aside .clip:first > .dTreeNode').each(function(){
      for (var i = 0; i < menusToKeep.length; i++) {
        var id = menusToKeep[i];
        if ($(this).html().indexOf('id=' + id) >= 0) return;
      }
      $(this).remove();
      $(this).next().remove();
    });
  }
});
