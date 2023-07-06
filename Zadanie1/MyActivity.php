<?php
require("../Zadanie1/header.php");
require_once("config.php");
require_once("restricted.php");

try{
    $query="SELECT * FROM `history` 
    JOIN `users` ON history.user_id =users.id 
    WHERE users.email=?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['email']]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>My Activity</title>
</head>
<body>
    <H1 class="container">My Activity</H1>
    <div class="container">
        <table id="table" class="table">
                <thead><tr><td>Activity</td><td>TIMESTAMP</td></tr></thead>
                <tbody>
                <?php
                    foreach($results as $row){
                        echo "<tr><td>".$row['activity']."</td><td>".$row['time']."</td></tr>";
                    }
                ?>
                </tbody>
            </table>
        </div>

<script>
    $(document).ready(function () {
        $('#table').DataTable({
            pagingType: 'full_numbers',
            info:false,
            searching: false,
        });
});
</script>
    
</body>
</html>