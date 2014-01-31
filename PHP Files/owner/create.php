<?php
session_start();
date_default_timezone_set('Europe/Berlin');

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
		<h1>Create a new project</h1>
<?php
if(isset($_POST['submit'])){
	if($_POST['title'] && $_POST['beginDate'] && $_POST['endDate'])
	{
		$title 		= mysql_real_escape_string($_POST['title']);
		$beginDate	= mysql_real_escape_string($_POST['beginDate']);
		$beginDate .= ' 23:59:59';
		$endDate	= mysql_real_escape_string($_POST['endDate']);
		$endDate .= ' 23:59:59';
		$natPref	= mysql_real_escape_string($_POST['natPref']);
		$locPref	= mysql_real_escape_string($_POST['locPref']);
		$agePref	= mysql_real_escape_string($_POST['agePref']);
		$now = time();
		
		if(strtotime($beginDate) >= $now) 
		{
			if(strtotime($beginDate) < strtotime($endDate))
			{
				$select = $db->query('SELECT * FROM interests');
				$total_column = $select->columnCount();
				$interest = '';
				for ($counter = 1; $counter <= $total_column-1; $counter ++) {
					$meta = $select->getColumnMeta($counter);			
					if($meta['name'] != 'userID' && $meta['name'] != 'interestID')
					{
						if($_POST[$meta['name']] == 1)
							$interest .= $meta['name'].',';
					}
				}
				
				if ($insert_stmt = $db->prepare("INSERT INTO project (
						name,userID,beginDate,endDate,agePref,locationPref,nationalityPref,interests
				) VALUES (?, ?, ?, ?, ?, ?, ?,?)")) 
				{ 
					$insert_stmt->bindParam(1,$title);
					$insert_stmt->bindParam(2,$_SESSION['userID']); 
					$insert_stmt->bindParam(3,$beginDate); 
					$insert_stmt->bindParam(4,$endDate);
					$insert_stmt->bindParam(5,$agePref);
					$insert_stmt->bindParam(6,$locPref);
					$insert_stmt->bindParam(7,$natPref);
					$insert_stmt->bindParam(8,$interest);
					
					// Execute the prepared query.
					$insert_stmt->execute();
					echo "Project created!";
				}
			}
			else
				echo '<font color="red">Begindate and enddate are not correct.</font>';
		}
		else
			echo '<font color="red">Date is not in future.</font>';
	} 
	else
		echo '<font color="red">Please fill in the title, begindate and enddate.</font>';
}
?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<table width="100%">
			<tr>
				<td style="width:200px;">Title</td>
				<td><input type="text" name="title" value="" style="width:400px;"/></td>
			</tr>
			<tr>
				<td>Begin date (yyyy-mm-dd)</td>
				<td><input type="text" name="beginDate" value="" style="width:200px;"/></td>
			</tr>
			<tr>
				<td>End date (yyyy-mm-dd)</td>
				<td><input type="text" name="endDate" value="" style="width:200px;"/></td>
			</tr>
			<tr>
				<td>Age pref</td>
				<td>
					<select name="agePref">
						<option value=""></option>
						<option value="1"> <10</option>
						<option value="2"> >10 and <20</option>
						<option value="3"> >20 and <30</option>
						<option value="4"> >30 and <40</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="nationality">Nationality: </label></td>
				<td>
					<select name="natPref">
						<option value=""></option>
						<option value="NL">Netherlands</option>
						<option value="BE">Belgian</option>
						<option value="UK">British</option>								
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="country">Country:</label></td>
				<td>
					<select name="locPref">
					<option value=""></option>
					<option value="NL">Netherlands</option>
					<option value="BE">Belgium</option>
					<option value="UK">United Kingdom</option>		
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">Select the interests ...</td>
			</tr>
				<?php
				$select = $db->query('SELECT * FROM interests');

				$total_column = $select->columnCount();

				for ($counter = 1; $counter <= $total_column-1; $counter ++) {
					$meta = $select->getColumnMeta($counter);
					if($meta['name'] != 'userID' && $meta['name'] != 'interestID')
					{
				?>
					<tr>
						<td><label for="<?php echo $meta['name'];?>"><?php echo ucfirst($meta['name']);?></label></td>
						<td><input type="radio" name="<?php echo $meta['name'];?>" value="1">Yes
						<input type="radio" name="<?php echo $meta['name'];?>" value="0" checked>No</td>
					</tr>
				<?php
					}
				}
				?>			
			<tr>
				<td align="center" colspan="2">
					<input type="submit" name="submit" value="Submit"/>
				</td>
			</tr>
			
		</table>
	</form>	
		</div>

		<?php include("../inc/footer.php"); ?>
</div>

</body>
</html>