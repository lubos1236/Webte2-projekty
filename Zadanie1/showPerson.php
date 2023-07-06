<?php
require("../Zadanie1/header.php");

ini_set("display_errors", 1);
ini_set("display_startip_errors", 1);
error_reporting(E_ALL);

require_once("config.php");


if (!isset($_GET['id'])) {
    exit("id not exist");
}


try {

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add']))
	{
        $query="INSERT INTO place (placing,discipline,game_id,person_id)
        VALUES (?,?,?,?)";
        $stmt=$db->prepare($query);
		$stmt->execute([$_POST['placing'],$_POST['discipline'],$_POST['game'],$_POST['id']]);

        $query = "INSERT INTO history (user_id,activity) VALUES (?,?)";
        $stmt=$db->prepare($query);
        $stmt->execute([$_SESSION['user_id'],"New game record created (id=".$_POST['game'].") for person with id=".$_POST['id']]);
	}
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete']))
    {
        $query="DELETE FROM place WHERE id=?;";
        $stmt=$db->prepare($query);
        $stmt->execute([$_POST['id']]);

        $query = "INSERT INTO history (user_id,activity) VALUES (?,?)";
        $stmt=$db->prepare($query);
        $stmt->execute([$_SESSION['user_id'],"Game record deleted for person with id=".$_GET['id']]);
    }




    $query = "SELECT * FROM `person` WHERE id=?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET["id"]]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);

    $query = "SELECT place.id, place.placing, place.discipline, games.type, games.year, games.city, games.country FROM `place`
            JOIN games ON game_id=games.id
            WHERE person_id =?
            ORDER BY games.year DESC;";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET["id"]]);
    $gamesData = $stmt->fetchALL(PDO::FETCH_ASSOC);


    $query = "SELECT id, year, city FROM `games`";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);





} catch (PDOException $e) {
    echo $e->getMessage();
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <link href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.css">
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script  src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

</head>

<body>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add']))
    echo '<script>$(document).ready(function () {toastr.success("Game record successfully added");});</script>';
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete']))
    echo '<script>$(document).ready(function () {toastr.success("Game record successfully deleted");});</script>';
?>


    <H1 class="container">Information</H1>
    <div class="container">
        <div class="container bootstrap snippets bootdey">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-user-information">
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>
                                                <span class="glyphicon glyphicon-user  text-primary"></span>
                                                Name
                                            </strong>
                                        </td>
                                        <td class="text-primary">
                                            <?php echo $person['name']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>
                                                <span class="glyphicon glyphicon-cloud text-primary"></span>
                                                Surname
                                            </strong>
                                        </td>
                                        <td class="text-primary">
                                            <?php echo $person['surname']; ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <strong>
                                                <span class="glyphicon glyphicon-bookmark text-primary"></span>
                                                Day Of Birth
                                            </strong>
                                        </td>
                                        <td class="text-primary">
                                            <?php echo $person['birth_day']; ?>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td>
                                            <strong>
                                                <span class="glyphicon glyphicon-eye-open text-primary"></span>
                                                Place Of Birth
                                            </strong>
                                        </td>
                                        <td class="text-primary">
                                            <?php echo $person['birth_country'] . ", " . $person['birth_place']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>
                                                <span class="glyphicon glyphicon-envelope text-primary"></span>
                                                Day Of Death
                                            </strong>
                                        </td>
                                        <td class="text-primary">
                                            <?php
                                            if ($person['death_day'] != null)
                                                echo $person['death_day']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>
                                                <span class="glyphicon glyphicon-calendar text-primary"></span>
                                                Place Of Death
                                            </strong>
                                        </td>
                                        <td class="text-primary">
                                            <?php
                                            if ($person['death_day'] != null)
                                                echo $person['death_country'] . ", " . $person['death_place']; ?>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if(isset($_SESSION["logged"]) && $_SESSION["logged"]===true)
        echo '<a href="#add" class="edit" data-toggle="modal"> <button type="button" class="btn btn-success ">New game</button></a>';
        ?>
        <table id="table" class="table">
            <thead>
                <?php 
                if(isset($_SESSION["logged"]) && $_SESSION["logged"]===true)
                    echo "<tr><td>Place</td><td>Discipline</td><td>Type</td><td>Yrear</td><td>Place</td><td>Action</td></tr></thead>";
                else
                    echo "<tr><td>Place</td><td>Discipline</td><td>Type</td><td>Yrear</td><td>Place</td></tr></thead>";
                ?>
            <tbody>
            <?php
                foreach($gamesData as $data){
                    if(isset($_SESSION["logged"]) && $_SESSION["logged"]===true)
                    {
                        $btns= '<td>
                        <form action="#" method="post"> 
                        <a style="text-decoration: none; color: black;" href="editGame.php?id='.$data["id"].'"><button type="button" class="btn btn-info">Edit</button></a> 

                        <input type="hidden" name="id" value="'.$data["id"].'">
                        <input type="submit" name="delete" class="btn btn-danger" value="Delete" /> 

                        </form>
                        </td>';

                        echo "<tr><td>".$data['placing']."</td><td>".$data['discipline']."</td><td>".$data['type']."</td><td>".$data['year']."</td><td>".$data['country'].",".$data['city']."</td>".$btns."</tr></thead>";
                    }

                    else
                        echo "<tr><td>".$data['placing']."</td><td>".$data['discipline']."</td><td>".$data['type']."</td><td>".$data['year']."</td><td>".$data['country'].",".$data['city']."</td></tr></thead>";
                }
            ?>
            </tbody>
        </table>
    </div>




    <div id="add" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="#" method="post">
					<div class="modal-header">						
						<h4 class="modal-title">New Game</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
                    <div class="form-group">
                		<input type="hidden" value="<?php echo $_GET['id']; ?>" class="form-control" name="id" required>
                		<label>Placing</label>
                		<input type="text" class="form-control" name="placing" required>
                	</div>
                	<div class="form-group">
                		<label>Discipline</label>
                		<input type="text" class="form-control" name="discipline" required>
                	</div>
                    <div class="form-group">
                		<label>Type</label>
                        <select name="game">
                            <?php
                            foreach($games as $g)
                            {
                                echo "<option value=".$g['id'].">".$g['year']." ".$g['city']."</option>";
                            } 
                            ?>
                        </select>
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
<script>
    $(document).ready(function () {
        $('#table').DataTable({
            paging: false,
            ordering: false,
            info:false,
            searching: false,
        });
    });
</script>

</body>

</html>