function cloneLastTR( id, num )
{
	var table = document.getElementById( id );
	var rows = table.getElementsByTagName('tr');
	var root=rows[ rows.length -1 ].parentNode;
	
	var clones = [];
	for( var i=num; i>0; i--)
	{
		clones[ clones.length ] = rows[ rows.length -i ].cloneNode(true);
		setupSLAMFormFields( clones[ clones.length -1 ] );
	}
	
	for( var i=0; i<num; i++)
		root.appendChild( clones[i] );

	if( typeof(document.cloneTRcounter) == 'undefined' )
		document.cloneTRcounter = num;
	else
		document.cloneTRcounter+=num;
	
	return;
}

function removeLastTR( id, num )
{
	if( (typeof(document.cloneTRcounter) == 'undefined') || (document.cloneTRcounter == 0) )
		return;
	
	var table = document.getElementById( id );	
	var rows = table.getElementsByTagName('tr');
	var root=rows[ rows.length -1 ].parentNode;

	for( var i=0; i<num; i++)
		root.removeChild( rows[ rows.length -1 ] );
	
	document.cloneTRcounter-=num;
	
	return;
}

function setupSLAMFormFields( obj )
{
	/* function recursively sets any form fields starting with "SLAM_" to null, and increments any such form fields that possess an integer value */

	re = new RegExp( "^(SLAM_[a-zA-Z\_]*)([0-9]*)([\[\]]*)",'i');

	/* attempt to increment the form element name and set it's value */
	if( obj.name != null )
	{
		match = re.exec( obj.name );
		if( match != null )
		{
			if( match[2] != '' )
				match[2] = String(parseFloat( match[2] )+1);
			
			obj.name = match[1]+match[2]+match[3];
			obj.value = '';
		}
	}

	/* now do the same to all to children nodes */
	var children = obj.childNodes;
	
	for( var i=0; i<children.length; i++)
		setupSLAMFormFields( children[ i ] );
	
	return;
}