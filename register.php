<?php
require_once('user.php');
require_once('dbConnection.php');

$firstName = $lastName = $userName = $password = $email = "";

if (isset($_POST['submit'])) {
    $dbConn = createDBConnection();
    $_SESSION['db_conn'] = $dbConn;    
    $response = validateUserRegInput();
	$pattern_duplicateUser = "duplicate";
    if ($response == "") {
        $firstName = fix_string($dbConn, $_POST['firstName']);
        $lastName = fix_string($dbConn, $_POST['lastName']);
        $userName = fix_string($dbConn, $_POST['userName']);
        $email = fix_string($dbConn, $_POST['email']);
		$password = fix_string($dbConn, $_POST['password']);
        $user = new User;
		$user->setFirstname($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setPasswordHash($password);
        $user->setUserName($userName);
		
        $result = $user->add_user($dbConn, $firstName, $lastName, $userName, $password, $email);
        if ($result) 
		{
            echo "User registered successfully !";
            include 'login.php';
        } 
		else 
		{
            echo "User already exists!";
        }
    }
   	else
    {
        echo "Response from server is: $response ";
    }
    if($dbConn)  $dbConn->close();
}

function fix_string($connection, $string) 
      {     
          return htmlentities(real_escape($connection, $string));
      }
function real_escape($connection, $string) 
      {
          if (get_magic_quotes_gpc()) 
              $string = stripslashes($string);
          return $connection->real_escape_string($string);
      }
/*function fix_string($string) {
    if (get_magic_quotes_gpc())
        $string = stripslashes($string);
        return htmlentities ($string);
}*/

function validateFirstName($field) {
    return ($field == "") ? "No Firstname entered.<br>" : "";
}

function validateLastName($field) {
    return ($field == "") ? "No Lastname was entered.<br>" : "";
}

function validateUserName($field) {
    if ($field == "") return "No Username was entered<br>";
    if (strlen($field) < 5)
        return "Username must be 5 characters.<br>";
    $pattern = "/[^a-zA-Z0-9_-]/";
    if (preg_match($pattern, $field)) {
        return "Username should contain only alphanumeric characters,"
        ."_ and - characters";
    }

    return "";
}

function validatePassword($field) {
    if ($field == "") return "No Password was entered.<br>";

    if (strlen($field) > 12) return "";

    $pattern = "/^[a-zA-Z0-9!@#\$%\^\&*\)\(+=._-]{6,}$/";

    if (!preg_match($pattern, $field)) {
        return  "Password must atleast 6 characters with atleast one lowercase,".
         "one uppercase, one special character(!@%^#?*&_-)<br>";
    }

    return "";
}

function validateEmail($field) {
    if ($field == "") return "No Email was entered.<br>";

    $pattern = "/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";
    if (preg_match($pattern, $field)) {
        return "";
    } else {
        return "Invalid Email address.<br>";
    }
}

function validateUserRegInput(){
    $fail = "";
    $dbConn = $_SESSION['db_conn'];
    if (isset($_POST['firstName'])) {
        $firstName = fix_string($dbConn, $_POST['firstName']);
    }
    if (isset($_POST['lastName'])) {
        $lastName = fix_string($dbConn, $_POST['lastName']);
    }
    if (isset($_POST['userName'])) {
        $userName = fix_string($dbConn, $_POST['userName']);
    }
    if (isset($_POST['password'])) {
        $password = fix_string($dbConn, $_POST['password']);
    }
    if (isset($_POST['email'])) {
        $email = fix_string($dbConn, $_POST['email']);
    }

    $fail = validateFirstname($firstName);
    $fail .= validateLastname($lastName);
    $fail .= validateUsername($userName);
    $fail .= validateEmail($email);
    $fail .= validatePassword($password);

    return $fail;

}

?>
