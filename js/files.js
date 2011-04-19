function delete_submit(formid)
{
	var form = document.getElementById(formid);
	
	var answer = confirm("Are you sure you want to delete the selected files? This cannot be undone.");
	if (answer)
		form.submit();
}

function fileUploadSubmit( formid, inputname )
{
	var i=0,file,files=[];

	/* get the names of the currently attached file by scraping the table
	I know this is ugly, eventually it'd be better to embed an array or something in the page */
	while(true)
	{
		file = document.getElementById('asset_file_'+i);
		if(file == null)
			break;
		files[files.length] = file.innerHTML;
		i++;	
	}
	
	/* see if any of the filenames in the form inputs match any of the file names */
	var uploadname,error=false;
	var form = document.getElementById(formid);
	for(i=0; i < form.elements.length; i++)
	{
		if ((form.elements[i].name == inputname) && (form.elements[i].value != ''))
		{
			uploadname = form.elements[i].value.replace(/^.*(\\|\/|\:)/,'');
			if(inArray(uploadname,files))
			{
				var answer = confirm("The file \""+uploadname+"\" will overwite an existing file with the same name. Press Cancel to stop, or OK to continue.");
				if (!answer)
					return false;
			}
		}
	}
	
	window.setTimeout('showUploadingDiv()',1);
	form.submit();
	
	return true;
}

function showUploadingDiv()
{
	/* the delay after which to assume there's been an error */
	var errorDelay = 10*1000;
	
	var sDiv=document.createElement('div');
	sDiv.setAttribute('id','uploadingDiv');
	sDiv.innerHTML = "Please wait while your files are uploaded.<br /><img src='../img/grey_loader_dots.gif' width='43' height='11' alt='[loader dots'] />";
	
	document.body.appendChild(sDiv);
	
	/* set the calllback function */
	window.setTimeout('showUploadingError()',errorDelay);
	
	return;
}

function showUploadingError()
{
	/* hide the status div */
	var sDiv=document.getElementById('uploadingDiv');
	//if (sDiv != null)
	//	sDiv.style.display='none';
	
	/* show the error div */
	//var eDiv=document.createElement('div');
	sDiv.innerHTML = "An error has occured. Please try reattaching your files.<br /><a href='' onClick='hideUploadingDiv()'>Close</a>";
	
	return;
}

function hideUploadingDiv()
{
	var sDiv=document.getElementById('uploadingDiv');
	sDiv.style.display='none';
	return;
}

function inArray(needle, haystack)
{
	var length = haystack.length;
	for(var i = 0; i < length; i++)	{
        if(haystack[i] == needle) return true;
    }
    return false;
}