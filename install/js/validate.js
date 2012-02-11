function validate( obj, regex )
{
	var re = new RegExp( regex, 'g');
	
	var old = obj.value;
	var bad = obj.value.replace( re, '' );
	var txt = obj.value.replace( bad, '' );

	if( old != txt )
	{
		alert("That character can't be entered in this field.");
		obj.value = txt;
		obj.focus();
	}
	return;
}

