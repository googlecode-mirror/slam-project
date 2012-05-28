function togglePopupMenu(url, id, options)
{
	options['noclose'] = 'true';

	/* if the popup is already loaded, destroy it */
	removeBodyId( id );
	showPopupDiv( url, id, options );
	
	setTimeout(function(){
		document.onclick=function(){hideifOutsideClick(this.id, id)};
	},100);
	
	return;
}

function showPopupDiv( url, id, options )
{
	var sDiv=document.createElement('div');
	
	removeBodyId( id );	
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

function showFileManager( src )
{
	var test=document.getElementById('fileManagerDiv');
	if (test != null)
		document.body.removeChild(test);
	
	var sDiv=document.createElement('div');
	sDiv.setAttribute('id','fileManagerDiv');
	
	var myImg=document.createElement('img');
	myImg.setAttribute('src','img/grey_loader_circle.gif');
	myImg.setAttribute('id','fileManagerLoadImage');

	sDiv.appendChild( myImg );
	document.body.appendChild(sDiv);

	sDiv.innerHTML += "<a id='popupDivCloseA' onClick=\"removeBodyId('fileManagerDiv')\">close</a>\<iframe id='fileMangerIframe' onLoad=\"removeBodyId('fileManagerLoadImage')\" src='"+src+"' width='510px' height='310px' marginwidth='0' marginheight='0' frameborder='no' scrolling='no'></iframe>";

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

function hideifOutsideClick( e, divid )
{
	var target = (e && e.target) || (event && event.srcElement); 
	var parent = document.getElementById( divid );
	
	if( !isChildNode(parent, target) )
	{
		document.onclick = function(){};
		return removeBodyId( divid );
	}
	return false;
} 

function isChildNode( parent, testChild )
{
	/* see if the testChild node is a subnode of parent */
	while(testChild.parentNode)
	{
		if( testChild == parent )
			return true;
		testChild=testChild.parentNode;
	} 
	return false;
}

