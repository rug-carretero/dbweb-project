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
		<h1>Projects</h1>

		<table width="100%">
			<tr>
				<td style="width:200px;">Title</td>
				<td>Questions</td>
				<td>Begin date</td>
				<td>End date</td>
				<td>#</td>
				<td>#</td>
			</tr>
				<?php
				$select = $db->query('SELECT projectID,name,endDate,beginDate FROM project where userID="'.$_SESSION['userID'].'"');	
				while ($row = $select->fetch(PDO::FETCH_ASSOC)) 
				{	
					$result = $db->query('SELECT projectID FROM questions where projectID="'.$row['projectID'].'"');	
				?>
					<tr>
						<td><?php echo $row['name'];?></td>
						<td><?php echo $result->rowCount();?></td>
						<td><?php echo $row['beginDate'];?></td>
						<td><?php echo $row['endDate'];?></td>
						<td><a href="addquestion.php?projectID=<?php echo $row['projectID'];?>">Add question</a></td>
						<td></td>
					</tr>
				<?php
					
				}
				?>			
					
		</table>
	</div>

		<?php include("../inc/footer.php"); ?>
</div>

</body>
</html>