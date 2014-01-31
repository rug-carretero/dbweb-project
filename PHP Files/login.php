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
							WHERE username='".mysql_real_escape_string(strtolower($_POST['username']))."'"; 
					$result = $db->query($sql);

					if($result->rowCount() > 0 && !empty($result)) 
					{		
						$row = $result->fetch(PDO::FETCH_ASSOC);
						$hash = hash('sha512',$_POST['password'] . $row['salt']);
						
						if($hash == $row['password'])
						{
							if ($insert_stmt = $db->prepare("INSERT INTO loginlog (
									username,userID,sessieID
							) VALUES (?, ?, ?)")) 
							{
								$randSessie = rand(100000,999999);
								$insert_stmt->bindParam(1,$row['username']);
								$insert_stmt->bindParam(2,$row['userID']); 
								$insert_stmt->bindParam(3,$randSessie);
															
								// Execute the prepared query.
								$insert_stmt->execute();						
								
								$_SESSION['sessieID'] = $randSessie;
								$_SESSION['username'] = $row['username'];
								$_SESSION['userID'] = $row['userID'];
								header("Location: index.php");
							}
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
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					Username: <input type="text" name="username" /><br />
					Password: <input type="password" name="password" /><br />
					<input type="submit" name="submit" value="Log in!" />
				</form>
	</div>

		<?php include("inc/footer.php"); ?>
</div>

</body>
</html>