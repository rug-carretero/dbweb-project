<?php
include("config.php");
include("inc/salt.php");
try {
    $db = new PDO("mysql:host=".$host.";dbname=".$database,$userDB,$passwordDB); 
}	
    catch (PDOException $e){
    echo 'Connection failed: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
	<title>
		Crowdsourcing
	</title>
<body>

<div id="container" style="width:1000px">

	<div id="header" style="background-color:#FFA500;">
		<h1 style="margin-bottom:0;">Crowdsourcing</h1>
	</div>
		
		<?php include("inc/menu.php"); ?>
		
		<div id="content" style="background-color:#EEEEEE;height:600px;width:800px;float:left;">
		<h1 style="margin-bottom:0;">Register</h1>
<?php
	if(isset($_POST['submit']))
	{
		$error_msg = '';
		if($_POST['username'] && $_POST['password'] && $_POST['confirmpassword'] && $_POST['email'] && isset($_POST['usertype']))
		{
			if($_POST['password'] == $_POST['confirmpassword'] && strlen($_POST['password']) >= 8)
			{
				$username 	= strtolower(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
				$email 		= filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
				$email 		= filter_var($email, FILTER_VALIDATE_EMAIL);
				$password 	= filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
				$usertype 	= filter_input(INPUT_POST, 'usertype', FILTER_SANITIZE_STRING);
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
				{
					// Not a valid email
					$error_msg .= 'The email address you entered is not valid<br />';
				}
				
				$prep_stmt = "SELECT userID FROM users WHERE email = ? LIMIT 1";
				$stmt = $db->prepare($prep_stmt);
			 
				if ($stmt) {
					$stmt->bindParam(1, $email);
					$stmt->execute();
					
					if ($stmt->rowCount() == 1) 
					{
						// A user with this email address already exists
						$error_msg .= 'A user with this email address already exists.<br />';
					}
				}
				
				$prep_stmt = "SELECT userID FROM users WHERE username = ? LIMIT 1";
				$stmt = $db->prepare($prep_stmt);
			 
				if ($stmt) {
					$stmt->bindParam(1, $username);
					$stmt->execute();
					
					if ($stmt->rowCount() == 1) 
					{
						// A user with this email address already exists
						$error_msg .= 'A user with this username already exists.<br />';
					}
				}
				
				if (empty($error_msg)) 
				{
					// Create a random salt
					$random_salt = generateSalt();
			 
					// Create salted password 
					$password = hash('sha512', $password . $random_salt);
			 
					// Insert the new user into the database 
					if ($insert_stmt = $db->prepare("INSERT INTO users (
							username, email, password, salt, groupID
					) VALUES (?, ?, ?, ?, ?)")) 
					{
			 
						$insert_stmt->bindParam(1,$username);
						$insert_stmt->bindParam(2,$email); 
						$insert_stmt->bindParam(3,$password);
						$insert_stmt->bindParam(4,$random_salt);
						$insert_stmt->bindParam(5,$usertype);
						
						// Execute the prepared query.
						$insert_stmt->execute();						
						
						$sth = $db->query('SELECT userID FROM users WHERE username="'.$username.'" AND password="'.$password.'" LIMIT 1');
						$user = $sth->fetch(PDO::FETCH_ASSOC);
						 
						if ($sth->rowCount() == 1) 
						{
							if ($insert_interest = $db->prepare("INSERT INTO interests (
																userID) VALUES (?)")) 
							{
								$insert_interest->bindParam(1,$user['userID']);
								$insert_interest->execute();
						
								$error_msg = "Thank you for registering! Please login to set up your account. <br />\n";
							}
						}
						else
							$error_msg = "Try again. <br />\n";				
					}
				}
			}
			else
				$error_msg = "Password doesn't match the confirmation!<br />\n";
		}
		else
			$error_msg = "Fill in all fields!<br />\n";
	}
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
	<fieldset style="width:600px">
	<?php
	if (!empty($error_msg)) {
		echo '<p style="color:red;">'.$error_msg.'</p>';
	}
	?>
	<i>Please give a username, password and email address. Use at least 8 characters for your password. </i>
	<table>
		<col width="150">
		<tr>
			<td><label for="username">Username: </label></td>
			<td><input type="text" name="username" maxlength="50" />
		</tr>
		<tr>
			<td><label for="password">Password: </label></td>
			<td><input type="password" name="password" maxlength="50" />
		</tr>
		<tr>
			<td><label for="password">Confirm your password: </label></td>
			<td><input type="password" name="confirmpassword" maxlength="50" />
		</tr>
		<tr>
			<td><label for="email">Email address: </label></td>
			<td><input type="text" name="email" maxlength="50" />
		</tr>
		<tr>
			<td><label for="usertype">Type user: </label></td>
			<td><input type="radio" name="usertype" value="1" />User <input type="radio" name="usertype" value="2" />Owner 
		</tr>
		<tr>
			<td align="center" colspan="2">
				<input type="submit" name="submit" value="Register"/>
			</td>
		</tr>
		</table>
	</fieldset>
</form>
		</div>

		<?php include("inc/footer.php"); ?>
</div>

</body>
</html>