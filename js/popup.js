function togglePopupMenu(url, id, options)
{
	options['noclose'] = 'true';

	/* if the popup is already loaded, destroy it */
	var test=document.getElementById(id);
	if ((test !== null) && (test.getAttribute('xml-url') == url))
		document.body.removeChild(test);
	else
		showPopupDiv( url, id, options );
}

function showPopupDiv( url, id, options )
{
	var sDiv=document.createElement('div');
	sDiv.setAttribute('id',id);
	sDiv.setAttribute('xml-url',url);

	if (options['top'] != null)
		sDiv.style.top=options['top'];
	if (options['left'] != null)
		sDiv.style.left=options['left'];
	if (options['bottom'] != null)
		sDiv.style.bottom=options['bottom'];
	if (options['right'] != null)
		sDiv.style.right=options['right'];
	
	document.body.appendChild(sDiv);
	clientSideInsertSrc(id, url);

	if (options['noclose'] == null)
		sDiv.innerHTML = sDiv.innerHTML+"<a id='popupDivCloseA' onClick=\"removeBodyId('"+id+"')\">close</a>";

	/* return false to abort link redirection */
	return false;
}

function showPopupIframe( src, id, width, height )
{
	var test=document.getElementById(id);
	if (test != null)
		document.body.removeChild(test);

	var sDiv=document.createElement('div');
	sDiv.setAttribute('id',id);
	sDiv.innerHTML = "<a id='popupDivCloseA' onClick=\"removeBodyId('"+id+"')\">close</a><iframe id='fileMangerIframe' src='"+src+"' width='"+width+"px' height='"+height+"px' marginwidth='0' marginheight='0' frameborder='no' scrolling='no'></iframe>";

	document.body.appendChild(sDiv);

	return false;
}

/* from http://stackoverflow.com/questions/442404/dynamically-retrieve-html-element-x-y-position-with-javascript */
function getOffset( id )
{
	var _x = 0;
	var _y = 0;

	var el = document.getElementById( id );
	while( el && !isNaN( el.offsetLeft) && !isNaN( el.offsetTop ) )
	{
		_x += el.offsetLeft - el.scrollLeft;
		_y += el.offsetTop - el.scrollTop;
		el = el.offsetParent;
	}

	return { 'top': _y, 'left': _x};
}

function alignToBottomRight( id )
{
	var el = document.getElementById( id );
	loc = getOffset( id );
	
	var right = document.documentElement.offsetWidth-(loc['left']+el.offsetWidth);	
	return { 'top':(loc['top']+el.offsetHeight)+'px', 'right':right+'px'};
}