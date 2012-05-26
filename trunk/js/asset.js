/*
	Asset edit javascript functions
*/

function doEditJS()
{
	/*
	 empty stub for now
	*/
	
	return;
}

function doNonEditableWarning( )
{
	showPopupDiv( 'pub/warning_modal.html', 'warningModalDiv', [] );
	
	var div = document.getElementById( 'warningModalText' );
	div.innerHTML = 'You are not authorized to modify some of the assets you have selected. Edits made to attributes on this page will not be saved to those assets.';
	
	return;
}

function populatePermsPanel( id )
{
	var string = document.getElementById( id ).value;
	var perms = eval("(" + base64_decode(string) + ")");
	
	/* set owner fields & menu */
	var field = document.getElementById('perms-ownername');
	field.value = perms.owner;

	var menu = document.getElementById('perms-owner');
	for (var i=0; i<menu.length; i++)
		if (i == perms.owner_access){ menu.options[i].selected=true; }
	
	/* set project fields & menu */
	field = document.getElementById('perms-projectlist');
	field.value = perms.projects.join("\n");
	
	menu = document.getElementById('perms-project');
	for (var i=0; i<menu.length; i++)
		if (i == perms.project_access){ menu.options[i].selected=true; }

	/* set user (everyone) status */
	menu = document.getElementById('perms-user');
	for (var i=0; i<menu.length; i++)
		if (i == perms.default_access){ menu.options[i].selected=true; }
	
	return;
}

function returnPermsPanel()
{
	var perms = {};
	perms.owner = document.getElementById('perms-ownername').value;
	var temp = document.getElementById('perms-owner');
	perms.owner_access = temp.options[temp.options.selectedIndex].value;

	var temp = document.getElementById('perms-projectlist').value;
	perms.projects = temp.split("\n");
	
	var temp = document.getElementById('perms-project');
	perms.project_access = temp.options[temp.options.selectedIndex].value;
	
	var temp = document.getElementById('perms-user');
	perms.default_access = temp.options[temp.options.selectedIndex].value;
	
	var field = document.getElementById('permissions');
	field.value = base64_encode(JSON.stringify(perms));
	return;
}

function jumpToIdentifier( url )
{
	document.location.href=url;
	return;
}

function setSwitchableTR( status )
{
	var ids = document.getElementsByTagName('tr');
	for (var i=0; i < ids.length; i++)
	{
		var tr = ids[i];
		if ( tr.className == 'TRhidable' ){ tr.style.display=status; }
	}
	
	/* return false to abort link redirection */
	return false;
}

function doProjectMenu( selection, id )
{
	el = document.getElementById( id );
	el.value = selection;
	
	if (selection == 'Other'){ unhideBodyId( id ); }
	else { hideBodyId( id ); }
	
	return;
}

function get10Date()
{
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1;//January is 0!
	var yyyy = today.getFullYear();
	if(dd<10){ dd='0'+dd; }
	if(mm<10){ mm='0'+mm; }
	return yyyy+'-'+mm+'-'+dd;
}

/* from http://files.dontpanic82.com/unbeforeunload.html */
function isDirty(){
	//empty function. overwritten by setDirty
}

window.onbeforeunload = function(){
	//if isDirty returns a string - confirmation box
	return isDirty();
};

function setDirty( ev )
{
	var ev = ev || window.event; // W3C/IE DOM
	var el = ev.target || ev.srcElement; // W3C/IE DOM
	
	var kc = ev.charCode || ev.keyCode; //FF - charCode
	var formChanged = false;
	
	var check=document.getElementById('editRecord');
	if ((el.value=='Save') || (el.value=='Save Changes') || (check === null))
	{
		window.isDirty = function(){ return; };
		return;
	}
	
	//if checkbox, radio, combobox option
	if( ev.type == 'click' ){					
		//click on an option in a combobox
		if( el.nodeName.toLowerCase() == 'option' ){ formChanged = true; }
		//click on radiobutton/checkbox
		if( el.type && ( /radio|checkbox/i ).test( el.type )){ formChanged = true; }
	}
	
	//keypresses that alter form values
	if( ev.type == 'keypress' ){					
		//ignore F1-12, tab (9)
		if( (kc > 111 && kc < 124) || kc == 9 ){ return; }
		
		//keypress inside textarea/text-field
		if( ( /input|textarea/i ).test(el.nodeName) &&
			kc > 31 ){ formChanged = true; }
		if( el.type && ( /radio|checkbox/i ).test(el.type) &&
			kc == 32 ){ formChanged = true;	}
		//cursor up/down inside combobox
		if( (/select|option/i ).test( el.nodeName ) &&
			( /37|38|39|40/ ).test( kc ) ){ formChanged = true; }					
	}									
	
	//if form has changed (is dirty), or event doesn't alter the form, return
	if( isDirty() || !formChanged ){ return; }

	//overwrite isDirty-function
	window.isDirty = function(){ return 'Unsaved edits will be lost.'; };
}

/* setup commands */	
document.onkeypress = setDirty;
document.onclick = setDirty;