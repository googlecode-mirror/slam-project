function checkGeneralForm()
{
	var str="./ext/check_gen.php?";
	str += "SLAM_CONF_PREFIX="+document.getElementById('SLAM_CONF_PREFIX').value;
	str += "&SLAM_CONF_PATH="+encodeURIComponent(document.getElementById("SLAM_CONF_PATH").value);
	showPopupDiv( str, "checkGeneral", []);	
}

function checkDatabaseForm()
{
	var str="./ext/check_db.php?";
	str += "SLAM_DB_HOST="+encodeURIComponent(document.getElementById("SLAM_DB_HOST").value);
	str += "&SLAM_DB_NAME="+encodeURIComponent(document.getElementById("SLAM_DB_NAME").value);
	str += "&SLAM_DB_USER="+encodeURIComponent(document.getElementById("SLAM_DB_USER").value);
	str += "&SLAM_DB_PASS="+encodeURIComponent(document.getElementById("SLAM_DB_PASS").value);

	showPopupDiv(str, "checkGeneral", []);
}

function checkFilesForm()
{
	var str="./ext/check_file.php?";
	str += "SLAM_FILE_ARCH_DIR="+encodeURIComponent(document.getElementById("SLAM_FILE_ARCH_DIR").value);
	str += "&SLAM_FILE_TEMP_DIR="+encodeURIComponent(document.getElementById("SLAM_FILE_TEMP_DIR").value);
	
	showPopupDiv(str, "checkGeneral", []);
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

function clientSideInsertSrc(id, url)
{
	/* from http://www.boutell.com/newfaq/creating/include.html */
	
	var req = false;
	// For Safari, Firefox, and other non-MS browsers
	if (window.XMLHttpRequest)
	{
		try { req = new XMLHttpRequest(); }
		catch (e) { req = false; }
	}
	else if (window.ActiveXObject)
	{
		// For Internet Explorer on Windows
		try { req = new ActiveXObject("Msxml2.XMLHTTP"); }
		catch (e)
		{
			try { req = new ActiveXObject("Microsoft.XMLHTTP"); }
			catch (e) { req = false; }
		}
	}

	var element = document.getElementById(id);
	if (!element) { return; }
	
	if (req)
	{
		// Synchronous request, wait till we have it all
		req.open('GET', url, false);
		req.send(null);
		element.innerHTML = req.responseText;
	}
	else
		element.innerHTML = "Sorry, your browser does not support XMLHTTPRequest objects.";
}

function removeBodyId( id )
{
	var hDiv = document.getElementById( id );
	document.body.removeChild(hDiv);
	return;
}