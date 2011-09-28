<?php
/*

	General-purpose HTML routines

*/

function SLAM_makeErrorHTML($v,$inline=false,$attrs=''){
	$class = ($inline) ? 'error error_inline' : 'error';
	return "<div class='$class' $attrs>$v</div>";
}

function SLAM_makeNoteHTML($v,$inline=false,$attrs=''){
	$class = ($inline) ? 'note note_inline' : 'note';
	return "<div class='$class' $attrs>$v</div>";
}

function SLAM_makeHiddenInput($v,$n)
{
	/* returns a hidden HTML input
		$v = value
		$n = name
	*/
	return "<input type='hidden' name='$n' id='$n' value='$v' />\n";
}

function SLAM_makeMenuHTML($c,$a,$attrs,$b=false,$r=false)
{
	/* returns a HTML drop-down menu
		$c = selected value
		$a = array of options
		$attrs = attributes for the menu
		$b = prepend a blank option?
		$r = read only?
	*/

	if (!is_array($a))
		$a = array();

	if ($b) // stick an empty option onto the front of the provided array
		$a = array_merge(array(''=>''),$a);
		
	//if ($r) // most browsers don't support read-only menu, give them a text box instead
	//	return SLAM_makeInputHTML($c,40,255,$attrs,true);
	// NOTE: can use disabled instead, but we need to insert a hidden input as disabled values aren't sent
	// NOTE2: since the only readonly/disabled fields that we ever save from (CURRENTLY!) are the Identifier and Researcher fields, we don't need to insert the hidden input
	$z = ($r) ? 'disabled' : '';
	
	$s = "<select $attrs $z>\n";
	foreach($a as $k=>$v)
		$s.= (($c == $v) || ($c == $k)) ? "<option value='$v' selected>$k</option>\n" : "<option value='$v'>$k</option>\n";
	return "$s</select>\n";
}

function SLAM_makeCheckBoxes($c,$a,$attrs,$r=false)
{
	/* generates a checkbox array
		$c = selected value(s) (can be an array or a comma-delimited list)
		$a = array of options
		$attrs = attributes for the boxes
		$r = read only?
	*/
	
	if (!is_array($c))
		$c = explode(',',$c);

	foreach ($a as $v)
		$s.= (in_array($v,$c)) ? "$v<input type='checkbox' $attrs value='$v' checked />\n" : "$v<input type='checkbox' $attrs value='$v' />\n";
	return $s;
}

function SLAM_makeInputHTML($value,$s,$m,$attrs,$r=false)
{
	/* returns a HTML form text field
		$value = default value of the field
		$s = size
		$m = maxlength
		$attrs = attributes for the field
		$f = read only?
	*/
	
	$z = ($r) ? 'readonly' : '';		
	return "<input value='$value' size='$s' maxlength='$m' $attrs $z>";
}

function SLAM_makeTxtareaHTML($value,$c,$w,$attrs,$r=false)
{
	/* returns a HTML form textarea
		$value = default value of the textarea
		$c = column size
		$w = row size
		$attrs = attributes for the textarea
		$r = read only?
	*/
	
	$z = ($r) ? 'readonly' : '';	
	return "<textarea cols='$c' rows='$w' $attrs $z>$value</textarea>";
}

function SLAM_makeButtonHTML($v,$attrs,$r=false)
{
	/* returns a HTML button
	$v = button label (value)
	$attrs = attributes for the button
	$r = read-only?
	*/
	
	$z = ($r) ? 'readonly' : '';	
	return "<input type='button' value='$v' $attrs $z />";
}

?>