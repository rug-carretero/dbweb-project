<?php
session_start();
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
				<?php 
		if(isset($_SESSION['username']))
		{
		?>
			Welcome <?php echo ucfirst($_SESSION['username']); ?>!<br><br>
			
			You have participated in 
			<?php 
			$sql = "SELECT userID FROM joinproject WHERE userID = ".$db->quote($_SESSION['userID']);
			$result = $db->query($sql);
			echo $result->rowCount();
			?>
			project(s) and you helped answer
			<?php
			$sql = "SELECT userID FROM answer WHERE userID = ".$db->quote($_SESSION['userID']);
			$result = $db->query($sql);
			echo $result->rowCount();
			?>
			question(s) during the participation in those projects.
			
			
		<?php
		} else {
		?>
			This website provides a platform for people who want to make use of the crowd sourcing method - project owners - and participants in crowd sourcing projects.<br><br>
			
			Project owners are able to set up multiple projects. Each project has its own set of questions. These questions can be either an open question or a multiple choice question. Furthermore the project owner is able to set criteria for his or her projects. For example a project can only be available for people in a certain country or based on certain interests.<br><br>
	
			Participants can as well as project owners participate in project(s) of their choice. They are freely to choose to participate in the project available for them and then answer the questions related to those projects.<br><br>
			
			If you register now, you are able to make use of these fantastic opportunities!
		<?php
		}
		echo '</div>';
		include("inc/footer.php"); ?>
</div>

</body>
</html>