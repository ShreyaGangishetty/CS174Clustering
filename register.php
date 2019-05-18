<?php
require_once('user.php');
require_once('dbConnection.php');

$firstName = $lastName = $userName = $password = $email = "";

function fix_string($string) {
    if (get_magic_quotes_gpc())
        $string = stripslashes($string);
        return htmlentities ($string);
}

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
    if (isset($_POST['firstName'])) {
        $firstName = fix_string($_POST['firstName']);
    }
    if (isset($_POST['lastName'])) {
        $lastName = fix_string($_POST['lastName']);
    }
    if (isset($_POST['userName'])) {
        $userName = fix_string($_POST['userName']);
    }
    if (isset($_POST['password'])) {
        $password = fix_string($_POST['password']);
    }
    if (isset($_POST['email'])) {
        $email = fix_string($_POST['email']);
    }

    $fail = validateFirstname($firstName);
    $fail .= validateLastname($lastName);
    $fail .= validateUsername($userName);
    $fail .= validateEmail($email);
    $fail .= validatePassword($password);

    return $fail;

}

if (isset($_POST['submit'])) {
    $response = validateUserRegInput();
	$pattern_duplicateUser = "duplicate";
    if ($response == "") {
        $firstName = fix_string($_POST['firstName']);
        $lastName = fix_string($_POST['lastName']);
        $userName = fix_string($_POST['userName']);
        $email = fix_string($_POST['email']);
		$password = fix_string($_POST['password']);
        $user = new User;
		$user->setFirstname($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setPasswordHash($password);
        $user->setUserName($userName);
		$dbConn = createDBConnection();
        $user->dbConn = $dbConn;

        $result = $user->add_user($dbConn, $firstName, $lastName, $userName, $password, $email);
        $dbConn -> close();
        if ($result) 
		{
            echo "<p> User registered successfully !</p>";

            include 'login.php';
        } 
		else 
		{
            echo "<p> User already exits!";
        }
    }
   	else
		echo "<p> $response </p>";
}

?>
