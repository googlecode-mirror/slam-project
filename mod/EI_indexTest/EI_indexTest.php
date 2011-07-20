<?php

function EI_indexTest_request_1($config,$db,$user,$request)
{

	return true;
}

function EI_indexTest_content_1($config,$db,$user,$request,$result,&$content)
{
	$content="FOO\n$content";

	return true;
}

?>