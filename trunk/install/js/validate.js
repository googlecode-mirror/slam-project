function validatePos( obj, regex )
{
	var re = new RegExp( regex, 'g');
	
	var old = obj.value;
	var bad = obj.value.replace( re, '' );
	var txt = obj.value.replace( bad, '' );

	if( old != txt )
	{
		alert("That character can't be entered in this field.");
		obj.value = obj.value.replace( bad, '' );
		obj.focus();
	}
	return;
}

function validateNeg( obj, regex )
{
	var re = new RegExp( regex, 'g' );
	var bad = re.exec( obj.value );
	
	if( bad != null )
	{
		alert("That character can't be entered into this field.");
		obj.value = obj.value.replace( bad, '' );
		obj.focus();
	}
	
	return;
}

