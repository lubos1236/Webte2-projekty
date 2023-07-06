<?php
if(!isset($_SESSION["logged"]) || $_SESSION["logged"]===false)
header("location: index.php");
?>