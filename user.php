<?php
class User{
 
  var
  $firstName,
  $lastName,
  $userName,
  $passwordHash,
  $email,
  $user_id,
  $dbConn;


  function __construct(){
  }

  function getPasswordHash() {
    return $this->passwordHash;
  }

  function getUserName() {
    return $this->userName;
  }
  function getDbConn() {
  	return $this->dbConn;
  }
  
    function setFirstname($firstName) {
    $this->firstName = $firstName;
  }
  
    function setLastName($lastName) {
    $this->lastName = $lastName;
  }

  function getFirstname() {
    return $this->firstName;
  }
  function getLastName() {
    return $this->lastName;
  }
  
  function setUsername($userName) {
    $this->userName = $userName;
  }

  function setPasswordHash($passwordHash) {
    $this->passwordHash = $passwordHash;
  }

  function setEmail($email) {
    $this->email = $email;
  }
  function setDbConn($dbConn) {
    $this->dbConn = $dbConn;
  }


	public function isValidUserName($userName){
		if (strlen($userName) < 3) return false;
		if (strlen($userName) > 17) return false;
		if (!ctype_alnum($userName)) return false;
		return true;
	}

	public function login($newUser){
		if (!$this->isValidUsername($newUser->$userName)) return false;
		if (strlen($newUser->$password) < 3) return false;
		if($this->authenticateUser($newUser) == 1){
		    return true;
		}
	}

	public function logout(){
		session_destroy();
	}

	public function is_logged_in(){
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
			return true;
		}
  }

  function add_user($connection, $firstName, $lastName, $userName, $password, $email) 
  {
	  $SALT1 = "qm&h*";
	  $SALT2 = "pg!@";
	  $token = hash('ripemd128', "$SALT1$password$SALT2");
	  $query = "INSERT INTO users_table (first_name, last_name, user_name, password, email) VALUES('$firstName', '$lastName', '$userName', '$token', '$email')";
	  $result = $connection->query($query);
	  //$duplicate = preg_match("duplicate", strtolower($result));
	  if (!$result) 
	  {
		  //if($duplicate) 
		  die($connection->error);
	  }
	  else 
	  {
	  	$connection->close();
	  	return 1;
	  }
  }
  
  
  
  function authenticateUser($connection, $newUser)
  {
	  	  $userName = mysql_entities_fix_string($connection, $newUser->getUserName());    
		  $password = mysql_entities_fix_string($connection, $newUser->passwordHash);  
		  $query = "SELECT * FROM users_table WHERE user_name='$userName'";    
		  $result = $connection->query($query);
		  if (!$result) die($connection->error);
		  elseif ($result->num_rows) 
		  {
			  $row = $result->fetch_array(MYSQLI_NUM);
			  $result->close();
			  $SALT1 = "qm&h*";
			  $SALT2 = "pg!@";
			  $token = hash('ripemd128', "$SALT1$password$SALT2");
			  $connection->close();
			  if ($token == $row[4]) 
			  {
				  echo "$row[0] $row[1] : Hello and welcome, $row[0], you are now logged in as '$row[2]'";
				  echo "<br>"."<br>";
				  header('fileContentsPage.php');
				  return "success";
			  }				 
			  else return "Invalid username/password combination";
		  }			  
		 else 
			 return "Please register";
	 
  }
  
  
  function getAuthenticatedUser($connection, $user)
  {	
	  $connection = createDbConnection();
	  $userName = mysql_entities_fix_string($connection, $user->userName);    
	   $query = "SELECT * FROM users_table WHERE user_name='$userName'"; 
	   $result = $connection->query($query);
		if (!$result) die($connection->error);
		elseif ($result->num_rows) 
		  {
			  $row = $result->fetch_array(MYSQLI_NUM);
			  $result->close();		
			  return $row;			 
		  }
		$connection->close();  
		return 0;		  
  }
  
	function mysql_entities_fix_string($connection, $string) 
	  {
		  return htmlentities(mysql_fix_string($connection, $string));
	  }
	  function mysql_fix_string($connection, $string) 
	  {
	  	
		  if (get_magic_quotes_gpc()) 
			  $string = stripslashes($string);
		  return $connection->real_escape_string($string);
	  }

}

?>
