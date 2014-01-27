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
		if(isset($_GET['join']))
		{
			$sql = "SELECT username,userID,location,nationality,age,gender
					FROM users 
					WHERE username=".$db->quote($_SESSION['username'])." and userID=".$db->quote($_SESSION['userID']); 
			$result = $db->query($sql);						
			$userRow = $result->fetch(PDO::FETCH_ASSOC);
			$ageDif = date("Y")-$userRow['age'];
			if($ageDif < 10) { $ageP = 1;}
			elseif($ageDif > 9 && $ageDif < 20) { $ageP = 2; }
			elseif($ageDif > 19 && $ageDif < 30) { $ageP = 3; }
			elseif($ageDif > 29 && $ageDif < 40) { $ageP = 4; }
			$select = $db->query('SELECT * FROM project WHERE projectID="'.mysql_real_escape_string($_GET['join']).'" and
																DATE(now()) BETWEEN DATE(beginDate) AND DATE(endDate)');	
			if($select->rowCount() > 0 && !empty($select)) 
			{
				$row = $select->fetch(PDO::FETCH_ASSOC);
				if(!$row['locationPref'] || $row['locationPref'] == $userRow['location'])
				{
					if(!$row['nationalityPref'] || $row['nationalityPref'] == $userRow['nationality'])
					{
						if($row['agePref'] == 0)
						{
							$sql_int = "SELECT *
										FROM interests 
										WHERE userID='".mysql_real_escape_string(strtolower($_SESSION['userID']))."'"; 
							$resultInt = $db->query($sql_int);								
							$rowInt = $resultInt->fetch(PDO::FETCH_ASSOC);
							
							$interest = explode(',',$row['interests']);
							$count = 0;
							foreach($interest as $value)
							{
								if($rowInt[$value] == 1)
									$count++;
							}
							if($count == count($interest))
							{
								if ($insert_stmt = $db->prepare("INSERT INTO joinproject (
										projectID,userID
								) VALUES (?,?)")) 
								{ 
									$insert_stmt->bindParam(1,$row['projectID']);
									$insert_stmt->bindParam(2,$row['userID']); 									
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
					<td style="width:200px;">Title</td>
					<td>Questions</td>
					<td>Begin date</td>
					<td>End date</td>
					<td>Join</td>
				</tr>
					<?php
					$sql = "SELECT username,userID,location,nationality,age,gender
							FROM users 
							WHERE username=".$db->quote($_SESSION['username'])." and userID=".$db->quote($_SESSION['userID']); 
					$result = $db->query($sql);						
					$userRow = $result->fetch(PDO::FETCH_ASSOC);
					$ageDif = date("Y")-$userRow['age'];
					if($ageDif < 10) { $ageP = 1;}
					elseif($ageDif > 9 && $ageDif < 20) { $ageP = 2; }
					elseif($ageDif > 19 && $ageDif < 30) { $ageP = 3; }
					elseif($ageDif > 29 && $ageDif < 40) { $ageP = 4; }
					$select = $db->query('SELECT * FROM project WHERE DATE(now()) BETWEEN DATE(beginDate) AND DATE(endDate)');	
					while ($row = $select->fetch(PDO::FETCH_ASSOC)) 
					{	
						if(!$row['locationPref'] || $row['locationPref'] == $userRow['location'])
						{
							if(!$row['nationalityPref'] || $row['nationalityPref'] == $userRow['nationality'])
							{
								if($row['agePref'] == 0)
								{
									$sql_int = "SELECT *
												FROM interests 
												WHERE userID='".mysql_real_escape_string(strtolower($_SESSION['userID']))."'"; 
									$resultInt = $db->query($sql_int);								
									$rowInt = $resultInt->fetch(PDO::FETCH_ASSOC);
									
									$interest = explode(',',$row['interests']);
									$count = 0;
									foreach($interest as $value)
									{
										if($rowInt[$value] == 1)
											$count++;
									}
									if($count == count($interest))
									{
										$result = $db->query('SELECT projectID FROM questions where projectID="'.$row['projectID'].'"');	
										$answerQ = $db->query('SELECT * FROM joinproject where projectID="'.$row['projectID'].'" 
																				and userID="'.mysql_real_escape_string(strtolower($_SESSION['userID'])).'"');
										if($answerQ->rowCount() > 0){
											$text = 'Answer questions';
											$link = 'questions.php?projectID='.$row['projectID'];
										} else {
											$text = 'Join project';
											$link = 'projects.php?join='.$row['projectID'];
										}
										?>
											<tr>
												<td><?php echo $row['name'];?></td>
												<td><?php echo $result->rowCount();?></td>
												<td><?php echo $row['beginDate'];?></td>
												<td><?php echo $row['endDate'];?></td>
												<td><a href="../<?php echo $link;?>"><?php echo $text;?></a></td>
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