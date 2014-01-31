<?php
session_start();

include("config.php");
try {
    $db = new PDO("mysql:host=".$host.";dbname=".$database,$userDB,$passwordDB); 
}	
    catch (PDOException $e){
    echo 'Connection failed: ' . $e->getMessage();
}

$selectUser = $db->query('SELECT logID,userID FROM loginlog where userID="'.mysql_real_escape_string($_SESSION['userID']).'" 
														and username="'.mysql_real_escape_string($_SESSION['username']).'"
														and sessieID="'.mysql_real_escape_string($_SESSION['sessieID']).'"');	
$rowUser	= $selectUser->fetch(PDO::FETCH_ASSOC);
if(!$rowUser)
{
	session_destroy();
	header("Location: login.php");
}?>
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
		
		$projectID = mysql_real_escape_string($_GET['projectID']);
		$result = $db->query("SELECT count(questionID)
							FROM questions where projectID = '".$projectID."' group by questionID")->fetchAll();
		
		/* show error when there are no questions in the database */
		if(count($result) == 0)
		{
			echo "There are no questions!";
		}
		else
		{
			$sqlA = "SELECT *
					FROM   answer
					WHERE projectID='".$projectID."'
							and userID='".$rowUser['userID']."'";
			$resultAnswer = $db->query($sqlA);
			
			if($resultAnswer->rowCount() == count($result))
			{
				$finished = TRUE;
				$_SESSION['currentQuestion'] = $resultAnswer->rowCount();
			}
			else
				$_SESSION['currentQuestion'] = $resultAnswer->rowCount()+1;
			
			/* Show the progress of the questions. */
			if(isset($_POST['submit']) && $_SESSION['currentQuestion'] < count($result) && isset($_POST['answer']))
				echo '<h1>Question '.($_SESSION['currentQuestion']+1).'/'.count($result).'</h1>';
			else
				echo '<h1>Question '.($_SESSION['currentQuestion']).'/'.count($result).'</h1>';
			
			if(isset($_POST['submit']))
			{
				if(isset($_POST['answer']))
				{
					$answer 	= $db->quote($_POST['answer']);
					$questionNo = $db->quote($_SESSION['currentQuestion']);
					
					$sqlQuestion = "SELECT questionID,projectID,type
									FROM   questions
									WHERE projectID='".$projectID."'
									ORDER BY questionID LIMIT 1 OFFSET ".($_SESSION['currentQuestion']-1)."";
					$resultQuestion = $db->query($sqlQuestion);
					$question = $resultQuestion->fetch(PDO::FETCH_ASSOC);
					
					
					/* insert the answer from in the user into the database. */
						if($question['type'] == 'open')
							$type = 'answerOpen';
						else
							$type = 'answerMulti';
							
							if ($insert_stmt = $db->prepare("INSERT INTO answer (
									questionID,projectID, userID, ".$type."
							) VALUES (?, ?, ?, ?)")) 
							{
								$answer = mysql_real_escape_string($_POST['answer']);
								$insert_stmt->bindParam(1,$question['questionID']);
								$insert_stmt->bindParam(2,$projectID);
								$insert_stmt->bindParam(3,$rowUser['userID']);
								$insert_stmt->bindParam(4,$answer);
								$insert_stmt->execute();	
							}
						
						/* End of test; show the result */
						if($_SESSION['currentQuestion'] == count($result))
						{
							echo 'End of test.<br />';
							$_SESSION['currentQuestion'] = 1;
							$finished = TRUE;
							
						}
						else
							$_SESSION['currentQuestion']++;							
				}
				else
					echo 'Nothing selected! <br /><br />';
			}

			$sqlQuestion = "SELECT questionID,projectID,type,title,option1,option2,option3,option3,option4
							FROM   questions
							WHERE projectID='".$projectID."'
							ORDER BY questionID LIMIT 1 OFFSET ".($_SESSION['currentQuestion']-1)."";
			$resultQuestion = $db->query($sqlQuestion);

			if (!$resultQuestion) 
			{
				echo "Could not successfully run query ($sqlQuestion) from DB: " . mysql_error();
			}
			else
			{
				if ($resultQuestion->rowCount() == 0 || $finished) 
				{
					echo 'No new questions!';
					$_SESSION['currentQuestion'] = 1;
				}	
				else
				{
				
					if($_SESSION['currentQuestion'] >= 0 && !$finished)
					{	
						
						$question = $resultQuestion->fetch(PDO::FETCH_ASSOC);					
	?>
							<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>?projectID=<?php echo $projectID;?>" method="post">
								<?php echo $question["title"];?> <br />
								<?php
								if($question['type'] == 'multi')
								{
									echo '<input type="radio" name="answer" value="1" />
											'. $question['option1'] . '<br />'."\n";
									echo '<input type="radio" name="answer" value="2" />
											'. $question['option2'] . '<br />'."\n";
									if($question['option3'])
									{
										echo '<input type="radio" name="answer" value="3" />
												'. $question['option3'] . '<br />'."\n";
									}
									if($question['option4'])
									{
										echo '<input type="radio" name="answer" value="4" />
											'. $question['option4'] . '<br />'."\n";
									}
								}
								elseif($question['type'] == 'open')
								{
									echo '<TEXTAREA name="answer" ROWS=5 COLS=70></TEXTAREA>';
								}
								?>                
								<input type="submit" name="submit" value="Submit" />
							</form>
	<?php
						
					}
				}
				
			}
		}
		?>
		</div>
		<?php include("inc/footer.php"); ?>
</div>

</body>
</html>