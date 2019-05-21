<style>
	.signup {
		border:1px solid #999999; font: normal 14px helvetica; color: #444444;
	}
</style>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
	
<script>
$(document).ready(function(){
    $('.pass_show').append('<span class="ptxt">Show</span>');  
});
  

$(document).on('click','.pass_show .ptxt', function(){ 

    $(this).text($(this).text() == "Show" ? "Hide" : "Show"); 

    $(this).prev().attr('type', function(index, attr){return attr == 'password' ? 'text' : 'password'; }); 

}); 
 </script>  

<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
<h2>Login User</h2>
    <form name="form" role="form" method="post" enctype="multipart/form-data" action="login.php">
        <div class="form-group">
            <label for="userName">Username</label>
            <input type="text" name="userName" id="userName" class="form-control"  required />
        </div>
        <div class="form-group pass_show">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required />
        </div>
        <div class="form-actions">
            <button type="submit" name="submit" class="btn btn-primary">Login</button>
            <a href="index.php" class="btn btn-link">Register</a>
        </div>
    </form>
</div>

<?php
require_once('dbConnection.php');
require_once('user.php');

if (isset($_POST['userName'])
    && isset($_POST['password'])) {
	    $dbConn = createDBConnection();
        $newUser = new User();
        $fixedUserName = mysql_entities_fix_string($dbConn, $_POST['userName']);
        $newUser->setUsername($fixedUserName);
        $newUser->setPasswordHash($_POST['password']);
        $newUser->setDbConn($dbConn);
        $_SESSION['userName'] = $fixedUserName;
    	$_SESSION['password'] = $newUser->getPasswordHash();	
		

       $userAuthentic = $newUser->authenticateUser($dbConn, $newUser);
       //echo "user".$userAuthentic;
       if($userAuthentic=="success")
	   {
		   $validUser = $newUser->getAuthenticatedUser($dbConn, $newUser);
		   $dbConn->close();
		   header('Location:http://localhost/CS174Cluster/UploadFilesPage.php');
	   }
	   else
		{
			//$dbConn->close();
			echo "Error with login";
		}
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
?>
