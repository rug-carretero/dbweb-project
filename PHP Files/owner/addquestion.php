<?php
session_start();
include("../config.php");
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
		<h1 style="margin-bottom:0;">Crowdsourcing</h1>
	</div>

		<?php include("../inc/menu.php"); ?>

		<div id="content" style="background-color:#EEEEEE;height:600px;width:800px;float:left;">
		
<?php
if(isset($_GET['projectID']))
{
	$projectID = mysql_real_escape_string($_GET['projectID']);
	$select = $db->query('SELECT projectID,name FROM project where userID="'.$_SESSION['userID'].'" and projectID='.$projectID.'');	
	$row = $select->fetch(PDO::FETCH_ASSOC); 
	if($row)
	{
		echo '<h1>Add questions to project: '.$row['name'].'</h1>';
		if(isset($_POST['submit']))
		{
			if(isset($_POST['question']) && isset($_POST['type']))
			{
				$question	= mysql_real_escape_string($_POST['question']);
				$type		= mysql_real_escape_string($_POST['type']);
				
				if($type == 'open')
				{
					if ($insert_stmt = $db->prepare("INSERT INTO questions (
							projectID, type, title
					) VALUES (?, ?, ?)")) 
					{ 
						$insert_stmt->bindParam(1,$projectID);
						$insert_stmt->bindParam(2,$_POST['type']); 
						$insert_stmt->bindParam(3,$question); 
						
						// Execute the prepared query.
						$insert_stmt->execute();
						echo "Open question added to the project!";
					}
				}
				if($type == 'multi' && isset($_POST['option1']) && isset($_POST['option2']))
				{
					if ($insert_stmt = $db->prepare("INSERT INTO questions (
							projectID, type, title, option1, option2, option3, option4
					) VALUES (?, ?, ?, ?, ?, ?, ?)")) 
					{ 
						$option1 = mysql_real_escape_string($_POST['option1']);
						$option2 = mysql_real_escape_string($_POST['option2']);
						$option3 = mysql_real_escape_string($_POST['option3']);
						$option4 = mysql_real_escape_string($_POST['option4']);
						
						$insert_stmt->bindParam(1,$projectID);
						$insert_stmt->bindParam(2,$_POST['type']); 
						$insert_stmt->bindParam(3,$question); 
						$insert_stmt->bindParam(4,$option1); 
						$insert_stmt->bindParam(5,$option2); 
						$insert_stmt->bindParam(6,$option3); 
						$insert_stmt->bindParam(7,$option4); 
						
						// Execute the prepared query.
						$insert_stmt->execute();
						echo "Multi choice question added to the project!";
					}
				}
				else if($type == 'multi' && (!isset($_POST['option1']) || !isset($_POST['option2'])))
					echo "You selected multi, please fill in at least 2 options.";
			}
			else
				echo "Fill in the question and type.";
		}
?>
	<form method="post">
		<table width="100%">
			<tr>
				<td style="width:150px;">Question:</td>
				<td><input type="text" name="question" value="" style="width:400px;" /></td>
			</tr>
			<tr>
				<td>Type:</td>
				<td><input type="radio" name="type" value="multi" />Multi
					<input type="radio" name="type" value="open" />Open</td>
			</tr>
			<tr>
				<td colspan="2"><br /><br />Fill in your answers if its a multi choice qustion.</td>
			</tr>
			<tr>
				<td>Option 1:</td>
				<td><input type="text" name="option1" value="" style="width:400px;" /></td>
			</tr>
			<tr>
				<td>Option 2:</td>
				<td><input type="text" name="option2" value="" style="width:400px;" /></td>
			</tr>
			<tr>
				<td>Option 3:</td>
				<td><input type="text" name="option3" value="" style="width:400px;" /></td>
			</tr>
			<tr>
				<td>Option 4:</td>
				<td><input type="text" name="option4" value="" style="width:400px;" /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;"><br /><input type="submit" name="submit" value="submit" /></td>
			</tr>
		</table>
	</form>
<?php	
	}
	else
		echo 'Not your project!';
}
else
	echo 'Project not found.';
?>
	
	</div>

		<?php include("../inc/footer.php"); ?>
</div>

</body>
</html>