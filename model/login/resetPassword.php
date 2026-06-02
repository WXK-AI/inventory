<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$resetPasswordUsername = '';
	$resetPasswordCurrentPassword = '';
	$resetPasswordPassword1 = '';
	$resetPasswordPassword2 = '';
	$hashedPassword = '';
	
	if(isset($_POST['resetPasswordUsername'], $_POST['resetPasswordCurrentPassword'], $_POST['resetPasswordPassword1'], $_POST['resetPasswordPassword2'])){
		$resetPasswordUsername = htmlentities($_POST['resetPasswordUsername']);
		$resetPasswordCurrentPassword = $_POST['resetPasswordCurrentPassword'];
		$resetPasswordPassword1 = $_POST['resetPasswordPassword1'];
		$resetPasswordPassword2 = $_POST['resetPasswordPassword2'];
		
		if($resetPasswordUsername !== '' && $resetPasswordCurrentPassword !== '' && $resetPasswordPassword1 !== '' && $resetPasswordPassword2 !== ''){
			
			// Check if username is empty
			if($resetPasswordUsername == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter your username.</div>';
				exit();
			}
			
			// Check if current password is empty
			if($resetPasswordCurrentPassword == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter your current password.</div>';
				exit();
			}
			
			// Check if passwords are empty
			if($resetPasswordPassword1 == '' || $resetPasswordPassword2 == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter both passwords.</div>';
				exit();
			}
			
			// Check if username is available
			$usernameCheckingSql = 'SELECT * FROM user WHERE username = :username';
			$usernameCheckingStatement = $conn->prepare($usernameCheckingSql);
			$usernameCheckingStatement->execute(['username' => $resetPasswordUsername]);
			$user = $usernameCheckingStatement->fetch(PDO::FETCH_ASSOC);
			
			if(!$user){
				// Username doesn't exist. Hence can't reset password
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Username does not exist.</div>';
				exit();
			} elseif(!password_verify($resetPasswordCurrentPassword, $user['password'])){
				// Current password is incorrect. Hence, the account owner is not verified.
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Current password is incorrect.</div>';
				exit();
			} else {
				// Check if passwords are equal
				if($resetPasswordPassword1 !== $resetPasswordPassword2){
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Passwords do not match.</div>';
					exit();
				} else {
					// Start UPDATING password to DB
					// Hash the password using PHP's secure password hashing API
					$hashedPassword = password_hash($resetPasswordPassword1, PASSWORD_DEFAULT);
					$updatePasswordSql = 'UPDATE user SET password = :password WHERE username = :username';
					$updatePasswordStatement = $conn->prepare($updatePasswordSql);
					$updatePasswordStatement->execute(['password' => $hashedPassword, 'username' => $resetPasswordUsername]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Password reset complete. Please login using your new password.</div>';
					exit();
				}
			}
		} else {
			// One or more mandatory fields are empty. Therefore, display a the error message
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter all fields marked with a (*)</div>';
			exit();
		}
	}
?>
