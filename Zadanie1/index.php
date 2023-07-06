<?php
require("../Zadanie1/header.php");
require_once("config.php");

try{
    $query="SELECT person.id, person.name,person.surname,games.year, games.city,games.type, place.placing FROM `place` 
    JOIN `person` ON place.person_id =person.id 
    JOIN `games` ON place.game_id =games.id 
    WHERE place.placing=1;";
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

    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>


    <link href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.css">
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.js"></script> 

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script  src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script defer src="../Zadanie1/toast.js"></script>
</head>
<body>



    <div class="container">
        <H1>Zadanie 1</H1>
        <table id="table" class="table">
            <thead><tr><td>Meno</td><td>Priezvisko</td><td>Rok</td><td>Mesto</td><td>Typ</td><td>Umiestnenie</td></tr></thead>
            <tbody>
            <?php
                foreach($results as $row){
                    echo "<tr><td><a href='showPerson.php?id=".$row["id"]."'>".$row['name']."</td><td>".$row['surname']."</td><td>".$row['year']."</td><td>".$row['city']."</td><td>".$row['type']."</td><td>".$row['placing']."</td></tr>";
                }
            ?>
            </tbody>
        </table>
    </div>

<script>
    $(document).ready(function () {
        $('#table').DataTable({
            pagingType: 'full_numbers',
            columnDefs: [
            {
                targets: [4],
                orderData: [4,2],
            },
        ],
        });
});
</script>

</body>
</html>