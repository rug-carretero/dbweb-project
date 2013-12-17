<?php
session_start();

include("inc/salt.php");

if(isset($_GET['logout']))
{
	session_destroy();
	header("Location: index.php");
	exit;
}

if(isset($_SESSION['username']))
{
	header("Location: index.php");
	exit;
}

include("config.php");
try {
    $conn = new PDO("mysql:host=localhost;dbname=project",$userDB,$passwordDB); 
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
		<h1 style="margin-bottom:0;">Login</h1>
			<?php
			if(isset($_POST['submit']))
			{
				if(!$_POST['username'] || !$_POST['password'])
				{
					echo 'Fill in all fields!';
				}
				else
				{
					$sql = "SELECT password,salt,username, userID 
							FROM users 
							WHERE username='". $conn->quote($_POST['username'])."'"; 
					$result = $conn->query($sql);

					if($result->rowCount() > 0 && !empty($result)) 
					{		
						$row = $result->fetch(PDO::FETCH_ASSOC);
						$hash = hash('sha512',$_POST['password'] . $row['salt']);
						
						if($hash == $row['password'])
						{
							$_SESSION['username'] = $row['username'];
							$_SESSION['userID'] = $row['userID'];
							header("Location: index.php");
							echo 'You\'re logged in!';
						}
						else
							echo 'Invalid username or password!';
					}
					else
					{
						echo 'Invalid username or password!';
					}
				}
			}
			?>
				<form action="login.php" method="post">
					Username: <input type="text" name="username" /><br />
					Password: <input type="password" name="password" /><br />
					<input type="submit" name="submit" value="Log in!" />
				</form>
	</div>

		<?php include("inc/footer.php"); ?>
</div>

</body>
</html>