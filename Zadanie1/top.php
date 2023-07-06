<?php
require("../Zadanie1/header.php");
require_once("config.php");

try{
    $query="SELECT person.id, person.name,person.surname, COUNT(person.id) FROM `place` 
    JOIN `person` ON place.person_id =person.id 
    JOIN `games` ON place.game_id =games.id
    WHERE place.placing=1
    GROUP BY person.id
    ORDER BY COUNT(person.id) DESC
    LIMIT 10;";
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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <script
    src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
    crossorigin="anonymous"></script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <link href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.css">
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.js"></script>

    

</head>
<body>
    <div class="container">
        <H1>TOP10</H1>
        <table id="table" class="table">
            <thead><tr><td>Meno</td><td>Priezvisko</td><td>Počet zlatých medailí</td></tr></thead>
            <tbody>
            <?php
                foreach($results as $row){
                    echo "<tr><td><a href='showPerson.php?id=".$row["id"]."'>".$row['name']."</td><td>".$row['surname']."</td><td>".$row["COUNT(person.id)"]."</td></tr>";
                }
            ?>
            </tbody>
        </table>
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