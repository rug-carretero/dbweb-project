<div id="menu" style="background-color:#FFD700;height:600px;width:200px;float:left;">
	<b>Menu</b><br>
	<?php
	if(!isset($_SESSION['userID']) && !isset($_SESSION['username']))
	{
	?>
		<a href="login.php">Login</a><br>
		<a href="register.php">Register</a><br>
	<?php
	}
	else
	{
	?>
		<a href="editprofile.php">Edit profile</a><br />
		<a href="login.php?logout">Logout</a><br />
		
	<?php
	}
	?>
</div>