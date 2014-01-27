<?php
session_start();

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
		<h1 style="margin-bottom:0;">Edit profile</h1>
<?php
if(isset($_POST['submit']))
{
	if($_POST['gender'] && $_POST['nationality'] && $_POST['countries'] && $_POST['age'])
	{
		$gender 	= mysql_real_escape_string($_POST['gender']);
		$natio		= mysql_real_escape_string($_POST['nationality']);
		$age		= mysql_real_escape_string($_POST['age']);
		$country	= mysql_real_escape_string($_POST['countries']);
		if($_POST['age'] > 1900 && $_POST['age'] < 2000)
		{
			if($_POST['gender'] == 'F' || $_POST['gender'] == 'M')
			{			
				$select = $db->query('SELECT * FROM interests');

				$total_column = $select->columnCount();

				for ($counter = 1; $counter <= $total_column-1; $counter ++) {
					$meta = $select->getColumnMeta($counter);
					if($meta['name'] != 'userID' && $meta['name'] != 'interestID')
					{
						$sql_int = "UPDATE interests 
							SET ".$meta['name']."=?
							WHERE userID=?";
						$q_int = $db->prepare($sql_int);
						$q_int->execute(array($_POST[$meta['name']],$_SESSION['userID']));
					}
				}
				// query
				$sql = "UPDATE users 
						SET age=?, nationality=?, location=?, gender=?
						WHERE userID=? and username=?";
				$q = $db->prepare($sql);
				$q->execute(array($age,$natio,$country,$gender,$_SESSION['userID'],$_SESSION['username']));
				$error_msg = "Your profile has been successfully updated.\n";
			}
			else
				$error_msg = 'Wrong gender';
		}
		else
			$error_msg = 'Wrong date of birth';
	}
	else
		$error_msg = "Fill in all forms.";
}

	$sql = "SELECT username, userID, nationality, age, location, gender
			FROM users 
			WHERE username='".mysql_real_escape_string(strtolower($_SESSION['username']))."'"; 
	$result = $db->query($sql);
	
	$sql_int = "SELECT *
				FROM interests 
				WHERE userID='".mysql_real_escape_string(strtolower($_SESSION['userID']))."'"; 
	$resultInt = $db->query($sql_int);
	
	if($result->rowCount() == 0 && empty($result) && $resultInt->rowCount() == 0 && empty($resultInt)) 
	{
		$error_msg = "Users doesn't exist.";
		exit;
	}	
	
	$row 	= $result->fetch(PDO::FETCH_ASSOC);
	$rowInt = $resultInt->fetch(PDO::FETCH_ASSOC);
?>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<fieldset style="width: 500px">
			<?php
			if (!empty($error_msg)) {
				echo '<p style="color:red;">'.$error_msg.'</p>';
			}
			?>
				<p><i>Below you can fill in some more information about yourself. 
				This information will be used to determine if you are eligble to participate in certain projects.
				However it isn't necessary to fill in the additional information.  </i><p>

				<table>
					<col width="150">
					<tr>
						<td><label for="gender">Gender: </label></td>
						<td><input type="radio" name="gender" value="M" <?php if($row['gender'] == 'M') echo "checked";?>>Male
							<input type="radio" name="gender" value="F" <?php if($row['gender'] == 'F') echo "checked";?>>Female</td>
					</tr>
					<tr>
						<td><label for="age">Year of birth: </label></td>
						<td><input type="text" name="age" maxlength="4" value="<?php echo ($row['age'] == 0 ? '' : $row['age']);?>" style="width:50px;" /></td>
					</tr>
					<tr>
						<td><label for="nationality">Nationality: </label></td>
						<td>
							<select name="nationality">
								<option value=""></option>
								<option value="NL" <?php if($row['nationality'] == 'NL') echo 'selected';?>>Netherlands</option>
								<option value="BE" <?php if($row['nationality'] == 'BE') echo 'selected';?>>Belgian</option>
								<option value="UK" <?php if($row['nationality'] == 'UK') echo 'selected';?>>British</option>								
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="country">Country you live in: </label></td>
						<td>
							<select name="countries">
							<option value=""></option>
							<option value="NL" <?php if($row['location'] == 'NL') echo 'selected';?>>Netherlands</option>
							<option value="BE" <?php if($row['location'] == 'BE') echo 'selected';?>>Belgium</option>
							<option value="UK" <?php if($row['location'] == 'UK') echo 'selected';?>>United Kingdom</option>		
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">Are you interested in ...</td>
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
								<td><input type="radio" name="<?php echo $meta['name'];?>" value="1" <?php if($rowInt[$meta['name']] == 1) echo "checked";?>>Yes
									<input type="radio" name="<?php echo $meta['name'];?>" value="0" <?php if($rowInt[$meta['name']] == 0) echo "checked";?>>No</td>
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
			</fieldset>
		</form>
	</div>

		<?php include("inc/footer.php"); ?>
</div>

</body>
</html>