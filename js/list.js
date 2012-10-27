function doListJS()
{
	return;
}

function checkAssetListBoxes(category)
{
	var i=0,checked=0;
	var field;
	field = document.getElementById(category+'_checkbox_'+i);
	
	while(true)
	{
		field = document.getElementById(category+'_checkbox_'+i);
		if(field == null){ break; }
		if(field.checked == true){ checked++; }
		i++;
	}
	
	var actions = document.getElementsByName('action');
	for(var i=0; i < actions.length; i++)
	{
		if (checked == 0)
		{
			if (actions[i].value == 'Edit')			{actions[i].disabled = true}
			else if (actions[i].value == 'Tag')		{actions[i].disabled = true}
			else if (actions[i].value == 'Untag')	{actions[i].disabled = true}
			else if (actions[i].value == 'Clone')	{actions[i].disabled = true}
			else if (actions[i].value == 'Delete')	{actions[i].disabled = true}
		}
		else if (checked == 1)
		{
			if (actions[i].value == 'Edit')			{actions[i].disabled = false}
			else if (actions[i].value == 'Tag')		{actions[i].disabled = false}
			else if (actions[i].value == 'Untag')	{actions[i].disabled = false}
			else if (actions[i].value == 'Clone')	{actions[i].disabled = false}
			else if (actions[i].value == 'Delete')	{actions[i].disabled = false}
		}
		else
		{
			if (actions[i].value == 'Edit')			{actions[i].disabled = false}
			else if (actions[i].value == 'Tag')		{actions[i].disabled = false}
			else if (actions[i].value == 'Untag')	{actions[i].disabled = false}
			else if (actions[i].value == 'Clone')	{actions[i].disabled = true}
			else if (actions[i].value == 'Delete')	{actions[i].disabled = false}
		}
	}
	
	return;
}

function toggleCategoryCheckboxes(category)
{
	var i=0, box;
	var box = document.getElementById(category+'_checkbox_'+i);
	
	while (true)
	{
		box = document.getElementById(category+'_checkbox_'+i);
		if (box == null)
			return checkAssetListBoxes(category);;
		box.checked = !box.checked;
		i++;
	}	
}