<?php
session_start();
include("../config.php");
date_default_timezone_set('europe/paris');
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
		<h1>Projects</h1>
		<?php
		if(isset($_GET['projectID']))
		{
			$projectID = mysql_real_escape_string($_GET['projectID']);
			$select = $db->query('SELECT projectID,name,beginDate 
									FROM project 
									WHERE userID="'.$rowUser['userID'].'" and projectID='.$projectID.'');	
			$row = $select->fetch(PDO::FETCH_ASSOC); 
			if($row)
			{
				if(isset($_GET['delete']))
				{
					if(isset($_GET['questionID']))
					{
						$questionID = mysql_real_escape_string($_GET['questionID']);
					
						//$now = time();
						//if(strtotime($row['beginDate']) >= $now) 
						//{
							$sql = "DELETE FROM questions 
									WHERE userID =  '".$rowUser['userID']."' and questionID = '".$questionID."'";
							$stmt = $db->exec($sql);
							echo 'Question deleted.';
						//}
						//else
						//	echo 'Can\'t delete this question, project already started.';
					}
					else
					{
						//$now = time();
						//if(strtotime($row['beginDate']) >= $now) 
						//{
							$selectQ = $db->query('SELECT * 
													FROM questions 
													WHERE projectID="'.$row['projectID'].'"');	
							while ($rowQ = $selectQ->fetch(PDO::FETCH_ASSOC)) 
							{
								$sql = "DELETE FROM questions 
												WHERE userID =  '".$rowUser['userID']."' and projectID = '".$row['projectID']."'";
								$stmt = $db->exec($sql);							
							}
							$selectQ = $db->query('SELECT * FROM answer 
															WHERE projectID="'.$row['projectID'].'"');	
							while ($rowQ = $selectQ->fetch(PDO::FETCH_ASSOC)) 
							{
								$sql = "DELETE FROM answer WHERE projectID = '".$row['projectID']."'";
								$stmt = $db->exec($sql);							
							}
							$selectQ = $db->query('SELECT * FROM joinproject where projectID="'.$row['projectID'].'"');	
							while ($rowQ = $selectQ->fetch(PDO::FETCH_ASSOC)) 
							{
								$sql = "DELETE FROM joinproject WHERE projectID = '".$row['projectID']."'";
								$stmt = $db->exec($sql);							
							}
							$sql = "DELETE FROM project WHERE userID =  '".$rowUser['userID']."' and projectID = '".$projectID."'";
							$stmt = $db->exec($sql);
							echo 'Project deleted.';
						//}
						//else
						//	echo 'Can\'t delete this project, project already started.';
					}
				}
				else
				{
					echo '<table width="100%">';
					echo '<tr>';
					echo '	<td><b>Question</b></td>';
					echo '	<td><b>Type</b></td>';
					echo '	<td><b>Delete</b></td>';
					echo '</tr>';
					$selectQ = $db->query('SELECT * FROM questions where projectID="'.$row['projectID'].'"');	
					while ($rowQ = $selectQ->fetch(PDO::FETCH_ASSOC)) 
					{	
						echo '<tr>';
						echo '	<td><a href="results.php?questionID='.$rowQ['questionID'].'">'.$rowQ['title'].'</a></td>';
						echo '	<td>'.$rowQ['type'].'</td>';
						echo '	<td><a href="project.php?projectID='.$row['projectID'].'&questionID='.$rowQ['questionID'].'&delete">Delete question</a></td>';
						
						echo '</tr>';
					}
					echo '</table>';
					echo '<a href="project.php">Go back</a>';
				}
			}
		}
		else
		{
		?>
		<table width="100%">
			<tr>
				<td style="width:200px;">Title</td>
				<td>Questions</td>
				<td>Begin date</td>
				<td>End date</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
				<?php
				$select = $db->query('SELECT projectID,name,endDate,beginDate FROM project where userID="'.$rowUser['userID'].'"');	
				while ($row = $select->fetch(PDO::FETCH_ASSOC)) 
				{	
					$result = $db->query('SELECT projectID FROM questions where projectID="'.$row['projectID'].'"');	
				?>
					<tr>
						<td><a href="project.php?projectID=<?php echo $row['projectID'];?>"><?php echo $row['name'];?></a></td>
						<td><?php echo $result->rowCount();?></td>
						<td><?php echo $row['beginDate'];?></td>
						<td><?php echo $row['endDate'];?></td>
						<td><a href="addquestion.php?projectID=<?php echo $row['projectID'];?>">Add question</a></td>
						<td><a href="results.php?projectID=<?php echo $row['projectID'];?>">See results</a></td>
						<td><a href="project.php?projectID=<?php echo $row['projectID'];?>&delete">Delete project	</a></td>
					</tr>
				<?php
					
				}
				?>			
					
		</table>
		<?php
		}
		?>
	</div>

		<?php include("../inc/footer.php"); ?>
</div>

</body>
</html>