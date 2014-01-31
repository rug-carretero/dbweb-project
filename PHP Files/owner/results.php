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
		<h1>Results</h1>
		<?php
		if(isset($_GET['projectID']))
		{
			if(isset($_GET['openQ']))
			{
				$projectID = mysql_real_escape_string($_GET['projectID']);
				$questionID = mysql_real_escape_string($_GET['openQ']);
				$select = $db->query('SELECT projectID,name 
										FROM project 
										WHERE userID="'.$rowUser['userID'].'" and projectID='.$projectID.'');	
				$row = $select->fetch(PDO::FETCH_ASSOC); 
				if($row)
				{
					$selectQ = $db->query('SELECT title,questionID 
											FROM questions 
											WHERE projectID="'.$row['projectID'].'" and questionID="'.$questionID.'"');
					$rowQ = $selectQ->fetch(PDO::FETCH_ASSOC); 
					echo '<h2>'.$rowQ['title'].'</h2>';
					echo '<table width="100%">';
					echo '	<tr>';
					echo '		<td><b>#</b></td>';
					echo '		<td><b>Answer</b></td>';
					echo '	</tr>';
					$count = 1;
					$result = $db->query('SELECT answerOpen
										FROM answer 
										WHERE projectID="'.$row['projectID'].'" and questionID="'.$rowQ['questionID'].'"');
					while ($rowA = $result->fetch(PDO::FETCH_ASSOC)) 
					{
						echo '<tr>';
						echo '	<td>'.$count.'</td>';
						echo '	<td>'.$rowA['answerOpen'].'</td>';
						echo '</tr>';
					}
					echo '</table>';
					echo '<a href="results.php?projectID='.$row['projectID'].'">Go back</a>';
				}
			}
			else
			{
				$projectID = mysql_real_escape_string($_GET['projectID']);
				$select = $db->query('SELECT projectID,name 
										FROM project 
										WHERE userID="'.$rowUser['userID'].'" and projectID='.$projectID.'');	
				$row = $select->fetch(PDO::FETCH_ASSOC); 
				if($row)
				{
					echo '<table width="100%">';
					echo '<tr>';
					echo '	<td><b>Question</b></td>';
					echo '	<td><b>Type</b></td>';
					echo '	<td><b>A</b></td>';
					echo '	<td><b>B</b></td>';
					echo '	<td><b>C</b></td>';
					echo '	<td><b>D</b></td>';
					echo '</tr>';
					$selectQ = $db->query('SELECT * FROM questions WHERE projectID="'.$row['projectID'].'"');	
					while ($rowQ = $selectQ->fetch(PDO::FETCH_ASSOC)) 
					{	
							$resultA = $db->query('SELECT projectID,answerMulti 
												FROM answer 
												WHERE projectID="'.$row['projectID'].'" and answerMulti=1 and questionID="'.$rowQ['questionID'].'"');
						$resultB = $db->query('SELECT projectID,answerMulti 
												FROM answer 
												WHERE projectID="'.$row['projectID'].'" and answerMulti=2 and questionID="'.$rowQ['questionID'].'"');
						$resultC = $db->query('SELECT projectID,answerMulti 
												FROM answer 
												WHERE projectID="'.$row['projectID'].'" and answerMulti=3 and questionID="'.$rowQ['questionID'].'"');
						$resultD = $db->query('SELECT projectID,answerMulti 
												FROM answer 
												WHERE projectID="'.$row['projectID'].'" and answerMulti=4 and questionID="'.$rowQ['questionID'].'"');
						$resultOpen = $db->query('SELECT answerID 
												FROM answer 
												WHERE projectID="'.$row['projectID'].'" and questionID="'.$rowQ['questionID'].'"');
						echo '<tr>';
						echo '	<td><a href="results.php?questionID='.$rowQ['questionID'].'">'.$rowQ['title'].'</a></td>';
						echo '	<td>'.$rowQ['type'].'</td>';
						if($rowQ['type'] == 'multi')
						{
							echo '	<td>'.$resultA->rowCount().'</td>';
							echo '	<td>'.$resultB->rowCount().'</td>';
							echo '	<td>'.($rowQ['option3'] ? $resultC->rowCount() : '-').'</td>';
							echo '	<td>'.($rowQ['option4'] ? $resultD->rowCount() : '-').'</td>';		
						}
						else
							echo '	<td colspan="4"><a href="results.php?projectID='.$rowQ['projectID'].'&openQ='.$rowQ['questionID'].'">
														See open answers ('.$resultOpen->rowCount().')
													</a></td>';									
						echo '</tr>';
					}
					echo '</table>';
					echo '<a href="results.php">Go back</a>';
				}
			}
		}
		elseif(isset($_GET['questionID']))
		{
			$questionID = mysql_real_escape_string($_GET['questionID']);
			$select = $db->query('SELECT type,title,option1,option2,option3,option4 
									FROM questions 
									WHERE userID="'.$rowUser['userID'].'" and questionID='.$questionID.'');	
			$row = $select->fetch(PDO::FETCH_ASSOC); 
			if($row)
			{
				echo '<b>'.$row['title'].'</b><br />';
				if($row['type'] == 'multi')
				{
					echo 'A) '.$row['option1'].'<br />';
					echo 'B) '.$row['option2'].'<br />';
					echo 'C) '.$row['option3'].'<br />';
					echo 'D) '.$row['option4'].'<br />';
				}
				else
					echo 'Open question';
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
			</tr>
				<?php
				$select = $db->query('SELECT projectID,name,endDate,beginDate FROM project where userID="'.$rowUser['userID'].'"');	
				while ($row = $select->fetch(PDO::FETCH_ASSOC)) 
				{	
					$result = $db->query('SELECT projectID FROM questions where projectID="'.$row['projectID'].'"');	
				?>
					<tr>
						<td><?php echo $row['name'];?></td>
						<td><?php echo $result->rowCount();?></td>
						<td><?php echo $row['beginDate'];?></td>
						<td><?php echo $row['endDate'];?></td>
						<td><a href="results.php?projectID=<?php echo $row['projectID'];?>">See results</a></td>
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