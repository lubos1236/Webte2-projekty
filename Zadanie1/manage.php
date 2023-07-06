<?php
require("../Zadanie1/header.php");
require_once("config.php");
require_once("restricted.php");




try{
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])){
        $query="DELETE FROM person WHERE id=?;";
        $stmt=$db->prepare($query);
        $stmt->execute([$_POST['id']]);

        $query = "INSERT INTO history (user_id,activity) VALUES (?,?)";
        $stmt=$db->prepare($query);
        $stmt->execute([$_SESSION['user_id'],"Person with id=".$_POST['id']." deleted"]);
    }
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])){

        $query = "SELECT * FROM `person` 
        WHERE name=? AND surname=?";
        $stmt = $db->prepare($query);
        $stmt->execute([$_POST['name'],$_POST['surname']]);
        if($stmt->rowCount()==0){
            $query="INSERT INTO person (name,surname,birth_day,birth_place,birth_country,death_day,death_place,death_country)
            VALUES (?,?,?,?,?,?,?,?)";
            $stmt=$db->prepare($query);
            $death_day= $_POST['death_day']==""? null: $_POST['death_day'];
            $death_place= $_POST['death_place']==""? null: $_POST['death_place'];
            $death_country= $_POST['death_country']==""? null: $_POST['death_country'];
            $stmt->execute([$_POST['name'],$_POST['surname'],$_POST['birth_day'],$_POST['birth_place'],$_POST['birth_country'],$death_day,$death_place,$death_country]);

            $query = "SELECT * FROM `person` 
            WHERE name=? AND surname=?";
            $stmt = $db->prepare($query);
            $stmt->execute([$_POST['name'],$_POST['surname']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $query = "INSERT INTO history (user_id,activity) VALUES (?,?)";
            $stmt=$db->prepare($query);
            $stmt->execute([$_SESSION['user_id'],"Add new person with id=".$data['id']]);

        }
        else
        unset($stmt);


        
    }

    $query="SELECT * FROM `person`;";
    $stmt=$db->query($query);
    $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch(PDOException $e){
    echo $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bootstrap CRUD Data Table for Database with Modal Form</title>
<script
    src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
    crossorigin="anonymous"></script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <link href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.css">
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script  src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function () {
    $('#table').DataTable();
});
</script>
</head>
<body>
    <?php
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete']))
        echo '<script>$(document).ready(function () {toastr.success("Person successfully deleted");});</script>';
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add']))
        echo '<script>$(document).ready(function () {toastr.success("Person successfully added");});</script>';
    ?>


<div class="container">
        <H1>Manage</H1>
        <a href="#addEmployeeModal" class="edit" data-toggle="modal"> <button type="button" class="btn btn-success ">New Person</button></a>
        <table id="table" class="table">
            <thead>
                <tr><td>Name</td><td>Surname</td><td>Birth Day</td><td>Birth Place</td><td>Birth Country</td><td>Death Day</td><td>Death Place</td><td>Death Country</td><td>Action</td></tr>
            </thead>
            <tbody>
                <?php
                    
                foreach($results as $row){
                    $btns= '<td>
                    <form action="#" method="post"> 
                        <a style="text-decoration: none; color: black;" href="editPerson.php?id='.$row["id"].'"><button type="button" class="btn btn-info">Edit</button></a>
                        <input type="hidden" name="id" value="'.$row["id"].'">
                        <input type="submit" name="delete" class="btn btn-danger" value="Delete" />  
                    </form>
                </td>';
                    echo "<tr><td><a href='showPerson.php?id=".$row["id"]."'>".$row['name']."</td><td>".$row['surname']."</td><td>".$row['birth_day']."</td><td>".$row['birth_place']."</td><td>".$row['birth_country']."</td><td>".$row['death_day']."</td><td>".$row['death_place']."</td><td>".$row['death_country']."</td>".$btns."</tr>";
                }
            ?>
            </tbody>
        </table>
</div>

<div id="addEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
					<div class="modal-header">						
						<h4 class="modal-title">Add Person</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
                    <div class="form-group">
	                	<label>Name</label>
	                	<input type="text" class="form-control" name="name" required>
	                </div>
	                <div class="form-group">
	                	<label>Surname</label>
	                	<input type="text" class="form-control" name="surname" required>
	                </div>
                    <div class="form-group">
	                	<label>Birth Day</label>
	                	<input type="date" class="form-control" name="birth_day" required>
	                </div>
                    <div class="form-group">
	                	<label>Birth Place</label>
	                	<input type="text" class="form-control" name="birth_place" required>
	                </div>
                    <div class="form-group">
	                	<label>Birth Country</label>
	                	<input type="text" class="form-control" name="birth_country" required>
	                </div>
                    <div class="form-group">
	                	<label>Death Day</label>
	                	<input type="date" class="form-control" name="death_day">
	                </div>
                    <div class="form-group">
	                	<label>Death Place</label>
	                	<input type="text" class="form-control" name="death_place">
	                </div>
                    <div class="form-group">
	                	<label>Death Country</label>
	                	<input type="text" class="form-control" name="death_country">
	                </div>				
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-success" name="add" value="Add">
					</div>
				</form>
			</div>
		</div>
	</div>

</body>
</html>