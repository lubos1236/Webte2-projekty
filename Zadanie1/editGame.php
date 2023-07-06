<?php
require("../Zadanie1/header.php");
require_once("config.php");
require_once("restricted.php");

if (!isset($_GET['id']))
	exit("id not exist");



try{
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
        $query="UPDATE place SET placing=?, discipline=?, game_id=?
		WHERE id=?";
        $stmt=$db->prepare($query);
		$stmt->execute([$_POST['placing'],$_POST['discipline'],$_POST['game'],$_POST['id']]);
        
        $query = "INSERT INTO history (user_id,activity) VALUES (?,?)";
        $stmt=$db->prepare($query);
        $stmt->execute([$_SESSION['user_id'],"Game with id=".$_POST['id']." edited"]);
	}
    
    $query = "SELECT place.placing, place.discipline, games.type, games.year, games.city, games.country FROM `place`
    JOIN games ON game_id=games.id
    WHERE place.id =?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET["id"]]);
    $gameData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $query = "SELECT id, year, city FROM `games`";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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

        <title>Edit Game</title>
    </head>
    <body>
        <?php
        if($_SERVER["REQUEST_METHOD"] == "POST")
        echo '<script>$(document).ready(function () {toastr.success("Game record successfully edited");});</script>';
        ?>
        
        <form class="container" action="#" method="post">
            <div class="modal-header">						
                <h4 class="modal-title">Edit Person</h4>
            </div>
            <div class="modal-body">				
                <div class="form-group">
                    <input type="hidden" value="<?php echo $_GET['id']; ?>" class="form-control" name="id" required>
                    <label>Placing</label>
                    <input type="text" value="<?php echo $gameData['placing']; ?>" class="form-control" name="placing" required>
                </div>
                <div class="form-group">
                    <label>Discipline</label>
		<input type="text" value="<?php echo $gameData['discipline']; ?>" class="form-control" name="discipline" required>
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
	<input type="button" class="btn btn-default" value="Back" onclick="history.back()">
	<input type="submit" class="btn btn-info" value="Save">
</div>
</form>
    
</body>
</html>