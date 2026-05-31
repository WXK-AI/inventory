<?php
	session_start();
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$loginUsername = '';
	$loginPassword = '';
	$hashedPassword = '';
	
	if(isset($_POST['loginUsername'], $_POST['loginPassword'])){
		$loginUsername = $_POST['loginUsername'];
		$loginPassword = $_POST['loginPassword'];
		
		if($loginUsername !== '' && $loginPassword !== ''){
			
			// Normalize username input without using deprecated FILTER_SANITIZE_STRING
			$loginUsername = trim(strip_tags($loginUsername));
			
			// Check if username is empty
			if($loginUsername == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Username</div>';
				exit();
			}
			
			// Check if password is empty
			if($loginPassword == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Password</div>';
				exit();
			}
			
			// Check the given username first, then verify the submitted password
			// against the secure hash stored in the database.
			$checkUserSql = 'SELECT * FROM user WHERE username = :username';
			$checkUserStatement = $conn->prepare($checkUserSql);
			$checkUserStatement->execute(['username' => $loginUsername]);
			$row = $checkUserStatement->fetch(PDO::FETCH_ASSOC);
			
			// Check if user exists or not
			if($row && password_verify($loginPassword, $row['password'])){
				// Valid credentials. Hence, start the session
				$_SESSION['loggedIn'] = '1';
				$_SESSION['fullName'] = $row['fullName'];
				
				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Login success! Redirecting you to home page...</div>';
				exit();
			} else {
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Incorrect Username / Password</div>';
				exit();
			}
			
			
		} else {
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Username and Password</div>';
			exit();
		}
	}
?>
