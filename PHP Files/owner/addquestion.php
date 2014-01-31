<?php
session_start();
include("../config.php");
try {
    $db = new PDO("mysql:host=".$host.";dbname=".$database,$userDB,$passwordDB); 
}	
    catch (PDOException $e){
    echo 'Connection failed: ' . $e->getMessage();
}
$selectUser = $db->query('SELECT logID,userID,username 
							FROM loginlog 
							WHERE userID="'.mysql_real_escape_string($_SESSION['userID']).'" 
										and username="'.mysql_real_escape_string($_SESSION['username']).'"
										and sessieID="'.mysql_real_escape_string($_SESSION['sessieID']).'"');	
$rowUser	= $selectUser->fetch(PDO::FETCH_ASSOC);
if(!$rowUser)
{
	session_destroy();
	header("Location: login.php");
}
else
{
	$selectUserG = $db->query('SELECT groupID,userID 
								FROM users 
								WHERE userID="'.$rowUser['userID'].'" and username="'.$rowUser['username'].'"');	
	$rowUserG	= $selectUserG->fetch(PDO::FETCH_ASSOC); 
	if(!$rowUserG) header("Location: ../index.php");
	if($rowUserG)
	{
		if($rowUserG['groupID'] != 2)
		{
			header("Location: ../index.php");
			exit;
		}
	}
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
	$select = $db->query('SELECT projectID,name FROM project where userID="'.$rowUser['userID'].'" and projectID='.$projectID.'');	
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
							projectID, type, title, userID
					) VALUES (?, ?, ?, ?)")) 
					{ 
						$type	 = mysql_real_escape_string($_POST['type']);
						$insert_stmt->bindParam(1,$projectID);
						$insert_stmt->bindParam(2,$type); 
						$insert_stmt->bindParam(3,$question); 
						$insert_stmt->bindParam(4,$rowUser['userID']); 
						
						// Execute the prepared query.
						$insert_stmt->execute();
						echo "Open question added to the project!";
					}
				}
				if($type == 'multi' && isset($_POST['option1']) && isset($_POST['option2']))
				{
					if ($insert_stmt = $db->prepare("INSERT INTO questions (
							projectID, userID, type, title, option1, option2, option3, option4
					) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) 
					{ 
						$option1 = mysql_real_escape_string($_POST['option1']);
						$option2 = mysql_real_escape_string($_POST['option2']);
						$option3 = mysql_real_escape_string($_POST['option3']);
						$option4 = mysql_real_escape_string($_POST['option4']);
						$type	 = mysql_real_escape_string($_POST['type']);
						
						$insert_stmt->bindParam(1,$projectID);
						$insert_stmt->bindParam(2,$rowUser['userID']); 
						$insert_stmt->bindParam(3,$type); 
						$insert_stmt->bindParam(4,$question); 
						$insert_stmt->bindParam(5,$option1); 
						$insert_stmt->bindParam(6,$option2); 
						$insert_stmt->bindParam(7,$option3); 
						$insert_stmt->bindParam(8,$option4); 
						
						// Execute the prepared query.
						$insert_stmt->execute();
						echo "Multi choice question added to the project!";
					}
				}
				else if($type == 'multi' && (!isset($_POST['option1']) || !isset($_POST['option2'])))
					echo "You selected multi, please fill in at least the first 2 options.";
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