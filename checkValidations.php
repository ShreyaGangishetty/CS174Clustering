<?php

function sanitize_input($string) 
{
    if (get_magic_quotes_gpc())
	{
		$string = stripslashes($string);
	}
    return htmlentities ($string);	
}


function validateFileName()
{
    $fail = "";
    $fileName = "";
    if (isset($_POST['fileName'])) {
        $fileName = sanitize_input($_POST['fileName']);
    }
    $fail = strlen($fileName)==0 ? 'Please enter file name<br>' : '';
    return $fail;
}

function checkFileName()
{
	if (isset($_POST['fileName'])) 
	{
		$response = validateFileName();

		if ($response == "") {
			$fileName = sanitize_input($_POST['fileName']);
			$result = true;
		}
		else
		{
			echo "$response";
			$result = false;
		}
		return $result? $fileName: "";
	}
}

function checkUploadedFile()
{	
	if ($_FILES)
	{
		if ($_FILES['file_upload']['type'] == 'text/plain')
		{
			$fileName = $_FILES['file_upload']['name'];
			move_uploaded_file($_FILES['file_upload']['tmp_name'], $fileName);
			$fileContent = sanitize_input(file_get_contents($fileName));
			if(strlen($fileContent)==0)
			{
				echo "Please upload non-empty files"; 
				return false;
			}
			else
			{
				return $fileContent;
			}			
		}
	}
}

?>