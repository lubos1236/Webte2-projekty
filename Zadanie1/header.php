<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olympic Games</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</head>
<body>

<nav class="navbar navbar-expand-md bg-dark navbar-dark sticky-top">
        <a class="navbar-brand" href="#">Olympic Games</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navb" aria-expanded="true">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navb" class="navbar-collapse collapse hide">
          <ul class="navbar-nav">
            <li class="nav-item active">
              <a class="nav-link" href="../Zadanie1/index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../Zadanie1/top.php">Top10</a>
            </li>
            
            <?php
            if(isset($_SESSION["logged"]) && $_SESSION["logged"]===true)
            {
              echo '<li class="nav-item">
              <a class="nav-link" href="../Zadanie1/manage.php">Manage Data</a>
            </li>';
            }
            ?>
          </ul>
      
          <ul class="nav navbar-nav ml-auto">
            <?php
            if(isset($_SESSION["logged"]) && $_SESSION["logged"]===true)
            {

                echo '<li class="nav-item">
                <a class="nav-link" href="../Zadanie1/MyActivity.php"><span class="fas fa-user"></span> My Activity</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="../Zadanie1/me.php"><span class="fas fa-sign-in-alt"></span> ' .$_SESSION["email"].'</a>
              </li>';

            }
            else{
                
                echo '<li class="nav-item">
                <a class="nav-link" href="../Zadanie1/register.php"><span class="fas fa-user"></span> Sign Up</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="../Zadanie1/login.php"><span class="fas fa-sign-in-alt"></span> Login</a>
              </li>';

            }


            ?>
          </ul>
        </div>
      </nav>
</body>
</html>