<?php 	
session_start();

function createDBConnection() 
	{
		$hostName = 'localhost';
		$db_username = 'root';
		$db_password = '';
		$conn = mysqli_connect($hostName, $db_username, $db_password);
		if (mysqli_connect_errno())
		{
		  echo "Failed to connect to database: " . mysqli_connect_error();
		}
		else
		{
			$CREATE_HW6_DB = "CREATE DATABASE IF NOT EXISTS cs174_hw6";
			$result = $conn->query($CREATE_HW6_DB);
			if (!$result) die ($conn->error);
			$result = $conn->query("use cs174_hw6");
			if (!$result) die ($conn->error);
			$fileTableExists = $conn->query("SELECT 1 FROM models_table LIMIT 1");
			if ($fileTableExists === FALSE) 
			{
				$CREATE_FILES_TABLE = "CREATE TABLE models_table (
				  model_type VARCHAR(64) NOT NULL,
				  model_name VARCHAR(64) NOT NULL,
				  model_data BLOB NOT NULL,
				  user_name VARCHAR(60) NOT NULL,
				  PRIMARY KEY(user_name, model_type, model_name)
				)";

				$result = $conn->query($CREATE_FILES_TABLE);
				if (!$result) die ($conn->error);
			}
			$userTableExists = $conn->query("SELECT 1 FROM users_table LIMIT 1");
			if ($userTableExists === FALSE) 
			{
				$CREATE_USERS_TABLE = "CREATE TABLE users_table (
				  user_id INT(8) UNSIGNED AUTO_INCREMENT,
				  first_name VARCHAR(128) NOT NULL,
				  last_name VARCHAR(128) NOT NULL,
				  user_name VARCHAR(128) UNIQUE NOT NULL,
				  password VARCHAR(32) NOT NULL,
				  email VARCHAR(128) NOT NULL,
				  PRIMARY KEY(user_id)
				)";

				$result = $conn->query($CREATE_USERS_TABLE);
				if (!$result) die ($conn->error);
			}
		} 
		return $conn;	
	}
?>