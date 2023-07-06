<?php
require("../Zadanie1/header.php");
require_once("config.php");
require_once("restricted.php");

if (!isset($_GET['id']))
	exit("id not exist");



try{
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$query="UPDATE person SET name=?, surname=?,birth_day=?,birth_place=?,birth_country=?,death_day=?,death_place=?,death_country=?
		WHERE id=?";
        $stmt=$db->prepare($query);
		$death_day= $_POST['death_day']==""? null: $_POST['death_day'];
		$death_place= $_POST['death_place']==""? null: $_POST['death_place'];
		$death_country= $_POST['death_country']==""? null: $_POST['death_country'];
		$stmt->execute([$_POST['name'],$_POST['surname'],$_POST['birth_day'],$_POST['birth_place'],$_POST['birth_country'],$death_day,$death_place,$death_country,$_POST['id']]);

		$query = "INSERT INTO history (user_id,activity) VALUES (?,?)";
        $stmt=$db->prepare($query);
        $stmt->execute([$_SESSION['user_id'],"Person with id=".$_POST['id']." Edited"]);
	}

    $query = "SELECT * FROM `person` WHERE id=?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['id']]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);

}
catch(PDOException $e){
    echo $e->getMessage();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<script
        src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
        crossorigin="anonymous"></script>


        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>


        <link href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.css">
        <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.js"></script> 

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script  src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <title>Edit Person</title>
</head>
<body>
	<?php
	if($_SERVER["REQUEST_METHOD"] == "POST")
	echo '<script>$(document).ready(function () {toastr.success("Person data successfully edited");});</script>';
	?>
<form class="container" action="#" method="post">
    <div class="modal-header">						
	    <h4 class="modal-title">Edit Person</h4>
    </div>
    <div class="modal-body">				
	<div class="form-group">
		<input type="hidden" value=<?php echo $_GET['id']; ?> class="form-control" name="id" required>
		<label>Name</label>
		<input type="text" value=<?php echo $person['name']; ?> class="form-control" name="name" required>
	</div>
	<div class="form-group">
		<label>Surname</label>
		<input type="text" value=<?php echo $person['surname']; ?> class="form-control" name="surname" required>
	</div>
    <div class="form-group">
		<label>Birth Day</label>
		<input type="date" value=<?php echo $person['birth_day']; ?> class="form-control" name="birth_day" required>
	</div>
    <div class="form-group">
		<label>Birth Place</label>
		<input type="text" value=<?php echo $person['birth_place']; ?> class="form-control" name="birth_place" required>
	</div>
    <div class="form-group">
		<label>Birth Country</label>
		<input type="text" value=<?php echo $person['birth_country']; ?> class="form-control" name="birth_country" required>
	</div>
    <div class="form-group">
		<label>Death Day</label>
		<input type="date"<?php if($person['death_day']!=null) echo 'value="'.$person["death_day"].'"' ?> class="form-control" name="death_day">
	</div>
    <div class="form-group">
		<label>Death Place</label>
		<input type="text" <?php if($person['death_place']!=null) echo 'value="'.$person["death_place"].'"' ?> class="form-control" name="death_place">
	</div>
    <div class="form-group">
		<label>Death Country</label>
		<input type="text" <?php if($person['death_country']!=null) echo 'value="'.$person["death_country"].'"' ?> class="form-control" name="death_country">
	</div>
</div>
<div class="modal-footer">
	<a href="manage.php"><input type="button" class="btn btn-default" value="Cancel"></a>
	<input type="submit" class="btn btn-info" value="Save">
</div>
</form>
    
</body>
</html>