<!DOCTYPE html>
<html>
<head>
	<title>CS174 Project</title>
	<style>
	.signup {
		border:1px solid #999999; font: normal 14px helvetica; color: #444444;
	}
	</style>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
	<script>
	function validate(form) {
		fail = validateFirstName(form.firstName.value)
		fail += validateLastName(form.lastName.value)
		fail += validateUserName(form.userName.value)
		fail += validatePassword(form.password.value, form.passwordConfirm.value)
		fail += validateEmail(form.email.value)
		
		if (fail == "") {
			alert ("validation successful!")	
			return true
		} else { alert(fail); return false }
	}
	</script>
</head>


<div class="container">
	<div class="row">
	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<form role="form" method="post" action="register.php" autocomplete="off" 
				onsubmit="return validate(this)">

				<h2>Register User</h2>
				<hr>
				<div class="form-group">
					<input type="text" name="firstName" id="firstName" class="form-control input-lg" placeholder="firstName "  tabindex="1">
					<p class="error" id="firstnameError"></p>
				</div>
				<div class="form-group">
					<input type="text" name="lastName" id="lastName" class="form-control input-lg" placeholder="lastName"  tabindex="2">
					<p class="error" id="surnameError"></p>
				</div>
				<div class="form-group">
					<input type="text" name="userName" id="userName" class="form-control input-lg" placeholder="userName"  tabindex="3">
					<p class="error" id="usernameError"></p>
				</div>
				<div class="form-group">
					<input type="text" name="email" id="email" class="form-control input-lg" placeholder="Email" tabindex="4">
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="form-group">
							<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" tabindex="5">
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="form-group">
							<input type="password" name="passwordConfirm" id="passwordConfirm" class="form-control input-lg" placeholder="Confirm Password" tabindex="6">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Register" class="btn btn-primary btn-block btn-lg" tabindex="5"></div>
				<p class="col-xs-6 col-md-6">Already a member? <a href='login.php'>Login</a></p>

				</div>
				<hr>
			</form>
		</div>
	</div>
</div>

<script src="//code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="validate.js"></script>

<?php

require_once('user.php');
require_once('register.php');

?>