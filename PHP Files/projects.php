<?php
session_start();
date_default_timezone_set('europe/paris');

include("config.php");
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
		if(isset($_GET['join']))
		{
			$sql = "SELECT username,userID,location,nationality,age,gender
					FROM users 
					WHERE username='".$rowUser['username']."' and userID=".$rowUser['userID']; 
			$result = $db->query($sql);						
			$userRow = $result->fetch(PDO::FETCH_ASSOC);
			$ageDif = date("Y")-$userRow['age'];
			$projectID = mysql_real_escape_string($_GET['join']);
			if($ageDif < 10) { $ageP = 1;}
			elseif($ageDif > 9 && $ageDif < 20) { $ageP = 2; }
			elseif($ageDif > 19 && $ageDif < 30) { $ageP = 3; }
			elseif($ageDif > 29 && $ageDif < 40) { $ageP = 4; }
			$select = $db->query('SELECT * 
									FROM project 
									WHERE projectID="'.$projectID.'" and
																DATE(now()) BETWEEN DATE(beginDate) AND DATE(endDate)');	
			if($select->rowCount() > 0 && !empty($select)) 
			{
				$row = $select->fetch(PDO::FETCH_ASSOC);
				if(!$row['locationPref'] || $row['locationPref'] == $userRow['location'])
				{
					if(!$row['nationalityPref'] || $row['nationalityPref'] == $userRow['nationality'])
					{
						if($row['agePref'] == 0 || $ageP == $row['agePref'])
						{
							$sql_int = "SELECT *
										FROM interests 
										WHERE userID='".$rowUser['userID']."'"; 
							$resultInt = $db->query($sql_int);								
							$rowInt = $resultInt->fetch(PDO::FETCH_ASSOC);
							
							$count = 0;
							$interest = explode(',',$row['interests']);
							$c = count($interest);
							for($i=0;$i<$c-1;$i++)
							{
								if($rowInt[$interest[2]] == 1)
									$count++;
							}
							if($count == count($interest)-1)
							{
								if ($insert_stmt = $db->prepare("INSERT INTO joinproject (
										projectID,userID
								) VALUES (?,?)")) 
								{ 
									$insert_stmt->bindParam(1,$row['projectID']);
									$insert_stmt->bindParam(2,$userRow['userID']); 									
									// Execute the prepared query.
									$insert_stmt->execute();
								echo 'You joined the project!';
								}
							}
						}
					}
				}
			}
		}
		?>
			<table width="100%">
				<tr>
					<td style="width:200px;"><b>Title</b></td>
					<td><b>Questions</b></td>
					<td><b>Begin date</b></td>
					<td><b>End date</b></td>
					<td><b>Join</b></td>
				</tr>
					<?php
					$sql = "SELECT username,userID,location,nationality,age,gender
							FROM users 
							WHERE username=".$db->quote($_SESSION['username'])." and userID=".$rowUser['userID']; 
					$result = $db->query($sql);						
					$userRow = $result->fetch(PDO::FETCH_ASSOC);
					$ageDif = date("Y")-$userRow['age'];
					if($ageDif < 10) { $ageP = 1;}
					elseif($ageDif > 9 && $ageDif < 20) { $ageP = 2; }
					elseif($ageDif > 19 && $ageDif < 30) { $ageP = 3; }
					elseif($ageDif > 29 && $ageDif < 40) { $ageP = 4; }
					$select = $db->query('SELECT * 
										FROM project 
										WHERE DATE(now()) BETWEEN DATE(beginDate) AND DATE(endDate)');	
					while ($row = $select->fetch(PDO::FETCH_ASSOC)) 
					{	
						if(!$row['locationPref'] || $row['locationPref'] == $userRow['location'])
						{
							if(!$row['nationalityPref'] || $row['nationalityPref'] == $userRow['nationality'])
							{
								if($row['agePref'] == 0 || $ageP == $row['agePref'])
								{
									$sql_int = "SELECT *
												FROM interests 
												WHERE userID='".mysql_real_escape_string($_SESSION['userID'])."'"; 
									$resultInt = $db->query($sql_int);								
									$rowInt = $resultInt->fetch(PDO::FETCH_ASSOC);
									
									
										$count = 0;
										$interest = explode(',',$row['interests']);
										$c = count($interest);
										for($i=0;$i<$c-1;$i++)
										{
											if($rowInt[$interest[2]] == 1)
												$count++;
										}
										
									if($count == count($interest)-1)
									{
										$result = $db->query('SELECT projectID 
															FROM questions 
															WHERE projectID="'.$row['projectID'].'"');	
										$answerQ = $db->query('SELECT * 
															FROM joinproject 
															WHERE projectID="'.$row['projectID'].'" 
																	and userID="'.$rowUser['userID'].'"');
										$countQ = $db->query('SELECT questionID 
															FROM questions 
															WHERE projectID="'.$row['projectID'].'"');
										$countA = $db->query('SELECT answerID 
															FROM answer 
															WHERE projectID="'.$row['projectID'].'" 
																		and userID="'.$rowUser['userID'].'"');
										if($countQ->rowCount() == 0)
										{
											$link = 'No questions yet';
										} elseif($countQ->rowCount() == $countA->rowCount())
										{
											$link = 'Completed';
										}
										else
										{
											if($answerQ->rowCount() > 0){
												$text = 'Answer questions';
												$link = '<a href="../questions.php?projectID='.$row['projectID'].'">'.$text.'</a>';
											} else {
												$text = 'Join project';
												$link = '<a href="../projects.php?join='.$row['projectID'].'">'.$text.'</a>';
											}
										}
										?>
											<tr>
												<td><?php echo $row['name'];?></td>
												<td><?php echo $result->rowCount();?></td>
												<td><?php echo $row['beginDate'];?></td>
												<td><?php echo $row['endDate'];?></td>
												<td><?php echo $link;?></td>
											</tr>
										<?php
									}
								}
							}
						}
					}
					?>			
						
			</table>
		</div>
		<?php include("inc/footer.php"); ?>
</div>

</body>
</html>