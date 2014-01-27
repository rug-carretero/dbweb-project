<?php
session_start();
date_default_timezone_set('GMT');

if(!isset($_SESSION['username']))
{
	header("Location: login.php");
}

include("config.php");
include("inc/nationalities.php");
include("inc/countries.php");
try {
    $db = new PDO("mysql:host=localhost;dbname=project",$userDB,$passwordDB); 
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
		<h1 style="margin-bottom:0;">Crowdsourcing</h1></div>

		<?php include("inc/menu.php"); ?>

		<div id="content" style="background-color:#EEEEEE;height:600px;width:800px;float:left;">
		<h1 style="margin-bottom:0;">Join projects</h1>
		<?php
		/* If quiz is finished, don't show a new quiz but only the result. */
		$finished = FALSE;
				
		$result = $db->query("SELECT count(q_number)
							FROM questions where projectID = '".mysql_real_escape_string($_GET['projectID'])."' group by q_number")->fetchAll();
		
		
		
		?>
		</div>
		<?php include("inc/footer.php"); ?>
</div>

</body>
</html>