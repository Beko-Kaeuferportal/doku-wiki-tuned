function getElementsByClassName(className, tag, elm){
	var testClass = new RegExp("(^|\\s)" + className + "(\\s|$)");
	tag = tag || "*";
	elm = elm || document;
	var elements = (tag == "*" && elm.all)? elm.all : elm.getElementsByTagName(tag);
	var returnElements = [];
	var current;
	var length = elements.length;
	for(var i=0; i<length; i++){
		current = elements[i];
		if(testClass.test(current.className)){
			returnElements.push(current);
		}
	}
	return returnElements;
}
function wysiwyg_hide(id) {
    /* hide that id */
    el = document.getElementById(id);
    newClass = el.className;
    alreadyHas = /wysiwyg_hidden/.test(newClass);
    if (alreadyHas) { return; }
    newClass += " wysiwyg_hidden";
    el.className = newClass;
    return;
}

function wysiwyg_show(id) {
    /* show that id */
    el = document.getElementById(id);
    newClass = el.className;
    newClass = newClass.replace(/ wysiwyg_hidden/, "");
    newClass = newClass.replace(/wysiwyg_hidden/, "");
    newClass = newClass.replace(/wysiwyg_initially_hidden/, "");
    el.className = newClass;
    return;
}
function __wysiwyg_showEdit()
{
    buttons = getElementsByClassName("wysiwyg_edit_button");
    for(i=0;i<buttons.length;++i) {
        button = buttons[i];
        newClass = button.className;
        newClass = newClass.replace(/ wysiwyg_hidden/, "");
        newClass = newClass.replace(/wysiwyg_hidden/, "");
        button.className = newClass;
    }
}
function __wysiwyg_hideEdit() 
{
    buttons = getElementsByClassName("wysiwyg_edit_button");
    for(i=0;i<buttons.length;++i) {
        button = buttons[i];
        newClass = button.className;
        newClass += " wysiwyg_hidden";
        button.className = newClass;
    }
}
function _wysiwyg_showEditButtons(ajax) {
    success = ajax.response;
    if (success == 'success') {
        __wysiwyg_showEdit();
    }
    return;
}
function wysiwyg_editButtons() {    
    var ajax = new sack(DOKU_BASE + 'lib/plugins/wysiwyg/ajax.php');
    ajax.encodeURIString = false;
    ajax.setVar('action','checkPerms');
    ajax.setVar('id',ID);    
    ajax.onCompletion = function() { _wysiwyg_showEditButtons(ajax); };
    ajax.runAJAX();
    return;
}

var wysiwyg_pending = Array;
var wysiwyg_has_inited = false;
var wysiwyg_oldBodyPadding = "0px";

function wysiwyg_show_view(name) {
    wysiwyg_hide("wysiwyg_editor_"+name);
    wysiwyg_hide("wysiwyg_adobe_style_toolbar");
    wysiwyg_show("wysiwyg_view_"+name);    
    document.body.style.paddingTop = wysiwyg_oldBodyPadding;
    return;    
}

function wysiwyg_show_editor(name) {
    wysiwyg_hide('wysiwyg_view_'+name);
    wysiwyg_show("wysiwyg_adobe_style_toolbar");
    wysiwyg_show('wysiwyg_editor_'+name);
    wysiwyg_oldBodyPadding = document.body.style.paddingTop;
    // <IE7 lacks display:fixed
    if (!((navigator.appVersion.indexOf('MSIE 5')>-1)||(navigator.appVersion.indexOf('MSIE 6')>-1)) )
        { document.body.style.paddingTop = "80px"; }
    // gecko sets this to 0 if initially display-ed: none;
    toolbar = document.getElementById('wysiwyg_adobe_style_toolbar');
    toolChild = toolbar.firstChild;
    if (toolChild.nextSibling != null) { toolChild.nextSibling.height = 76; }
    else { toolChild.height = 76; }
    // set focus
    oEditor = FCKeditorAPI.GetInstance(name);
    try {
        oEditor.MakeEditable();
    }
    catch (e) {
    }
    oEditor.Focus();
    return;
}

function wysiwyg_confirm_save(name) {
    var confirmed = true;
    // confirmed = confirm("Overwrite?");    
    return confirmed;
}

/* called by the quit button */
function wysiwyg_quit(name) {
    oEditor = FCKeditorAPI.GetInstance(name);
    var confirmed = true;
    if ( oEditor.IsDirty() ) { confirmed = confirm("You will lose your changes."); }
    if (!confirmed) { return false; }
    var ajax = new sack(DOKU_BASE + 'lib/plugins/wysiwyg/ajax.php');
    ajax.encodeURIString = false;
    ajax.setVar('action','quit');
    ajax.setVar('name',name);
    ajax.setVar('id',ID);
    ajax.runAJAX();

    var html = wysiwyg_pending[name];
    oEditor.SetHTML(html);
    wysiwyg_show_view(name);
    __wysiwyg_showEdit();
    return true;
}

function wysiwyg_getArgs(  ) {
    var args = new Object(  );
    var query = location.search.substring(1);     
    var pairs = query.split("&");
    for(var i = 0; i < pairs.length; i++) {
        var pos = pairs[i].indexOf('=');
        if (pos == -1) { continue; }
        var argname = pairs[i].substring(0,pos);
        var value = pairs[i].substring(pos+1);
        args[argname] = unescape(value);
    }
    return args;     // Return the object
}
function wysiwyg_save_complete(name) {
    wysiwyg_show_view(name);
    __wysiwyg_showEdit();
    return;
}
function wysiwyg_send_to_page(html, name) {
    html = '<div class="wysiwyg_view_' + name + ' wysiwyg_view">' + html;
    html += '<a onmouseover="wysiwyg_highlight(\'' +name +'\');" onmouseout="wysiwyg_unhighlight(\'' +name +'\');" onclick="wysiwyg_edit(\'' + name + '\');" class="wysiwyg_button">edit</a></div>';
    page_blocks = getElementsByClass('wysiwyg_view_' + name);
    for (i=0;i<page_blocks.length;i++) {
        page_blocks[i].innerHTML = html;
    }
    return true;
}

function wysiwyg_send_to_server(html, name) {
    var ajax = new sack(DOKU_BASE + 'lib/plugins/wysiwyg/ajax.php');
    wysiwyg_send_to_page(html, name);
    ajax.encodeURIString = false;
    html = escape(html);
    ajax.setVar('action','save');
    ajax.setVar('name',name);
    ajax.setVar('html',html);
    ajax.setVar('id',ID);
    ajax.onCompletion = function() { wysiwyg_save_complete(name, ajax); };
    ajax.runAJAX();
}

/* called by the save button */
function wysiwyg_save(name) {
    if(!wysiwyg_confirm_save(name)) { return false; }
    var oEditor = FCKeditorAPI.GetInstance(name);
    var html = oEditor.GetXHTML();
    wysiwyg_send_to_server(html, name);
    // disable oEditor.
    return false;
}
function _wysiwyg_edit(name, ajax) {
    success = ajax.response;
    if (success != 'success') {
        if (success == 'noperms') {
            alert("You don't have permission to edit this page.");
        }
        else if(success.substr(0,6) == 'locked') {
            alert(success.substr(7) +" is already editing this page.");
        }
        else {
            alert("Some unknown error. Annoy your system admin.");
        }
        return;
    }
    __wysiwyg_hideEdit();
    oEditor = FCKeditorAPI.GetInstance(name);
    var html = oEditor.GetXHTML();
    wysiwyg_pending[name] = html;
    
    // show name editor
    wysiwyg_show_editor(name);
}
/* called by the edit button */
function wysiwyg_edit(name) {
    var ajax = new sack(DOKU_BASE + 'lib/plugins/wysiwyg/ajax.php');
    ajax.encodeURIString = false;
    ajax.setVar('action','edit');
    ajax.setVar('id',ID);    
    ajax.onCompletion = function() { _wysiwyg_edit(name, ajax); };
    ajax.runAJAX();
}
// called when FCKeditor is done starting..
function FCKeditor_OnComplete( editorInstance ){
    //editorInstance.EditorDocument.designMode = "on";
    if (!wysiwyg_has_inited) {
        wysiwyg_editButtons();
        wysiwyg_has_inited = true;
    }
    return true;
}

function _wysiwyg_has (name) {
    for(i=0;i<__wysiwyg_started.length;i++) {
        if (__wysiwyg_started[i] == name) { return true; }
    }
    return false;
}

function wysiwyg_highlight(name) {
    var theDiv = "wysiwyg_view_"+name;
    el = document.getElementById(theDiv);
    newClass = el.className;
    alreadyHas = /wysiwyg_highlighted/.test(newClass);
    if (alreadyHas) { return; }
    newClass += " wysiwyg_highlighted";
    el.className = newClass;
    return;
}

function wysiwyg_unhighlight(name) {
    el = document.getElementById("wysiwyg_view_"+name);
    newClass = el.className;
    newClass = newClass.replace(/ wysiwyg_highlighted/, "");
    newClass = newClass.replace(/wysiwyg_highlighted/, "");
    el.className = newClass;
    return;
}
