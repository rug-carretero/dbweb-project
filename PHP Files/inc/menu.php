<div id="menu" style="background-color:#FFD700;height:600px;width:200px;float:left;">
	<b>Menu</b><br>
	<?php
	if(!isset($_SESSION['userID']) && !isset($_SESSION['username']))
	{
	?>
		<a href="../login.php">Login</a><br>
		<a href="../register.php">Register</a><br>
	<?php
	}
	else
	{
		$prep_stmt = "SELECT userID, groupID FROM users WHERE userID = ? LIMIT 1";
		$stmt = $db->prepare($prep_stmt);
	 
		if ($stmt) 
		{
			$stmt->bindParam(1, $_SESSION['userID']);
			$stmt->execute();
			
			if ($stmt->rowCount() == 1) 
			{
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
						
	?>
				<a href="../projects.php">Join projects</a><br />
				<a href="../editprofile.php">Edit profile</a><br />
				<a href="../login.php?logout">Logout</a><br />
				
	<?php
				if($user['groupID'] == 2)
				{
	?>
					<br /><br />
					<b>Owner menu</b></br>
					<a href="../owner/results.php">See results</a><br />
					<a href="../owner/project.php">Projects</a><br />
					<a href="../owner/create.php">Create project</a><br />			
	<?php
				}
			}
		}
	}
	?>
</div>