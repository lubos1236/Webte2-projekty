<?php
require_once("../Zadanie2/config.php");
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if(isset($_GET['day']))
            getMenuByDay($db,$_GET['day']);
        else
            getMenus($db);
        break;
    case 'POST':
        creteFood($db,$_POST);
        break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $_PUT);
        updatePrice($db,$_PUT['id'],$_PUT['price']);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        deleteMenu($db,$id);
        break;
    case "OPTIONS":
        http_response_code(200);
        break;
}

function getMenus($db){
    $query="SELECT f.id, f.data_id, f.name, f.price, f.day, f.img, data.name as restaurant  FROM food f
    JOIN data ON f.data_id=data.id";
    $stmt=$db->query($query);
    $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
    http_response_code(200);
}
function getMenuByDay($db,$day){
    if($day==null)
    {
        http_response_code(400);
        return;
    }
    $query="SELECT f.id, f.data_id, f.name, f.price, f.day, f.img, data.name as restaurant FROM food f
    JOIN data ON f.data_id=data.id
    WHERE day LIKE ?";
    $stmt=$db->prepare($query);
    $stmt->execute([$day]);
    $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
    http_response_code(200);
}
function updatePrice($db,$id,$price){
    if($id==null)
    {
        http_response_code(400);
        return;
    }
    $query="SELECT * FROM food WHERE id= ?";
    $stmt=$db->prepare($query);
    $stmt->execute([$id]);
    if(count($stmt->fetch(PDO::FETCH_ASSOC))==0){
        http_response_code(404);
        return;
    }



    $query="UPDATE food SET price=? WHERE id= ?";
    $stmt=$db->prepare($query);
    $stmt->execute([$price,$id]);
    http_response_code(200);
}
function deleteMenu($db,$id){
    if($id==null)
    {
        http_response_code(400);
        return;
    }
    $query="DELETE FROM food WHERE `data_id`=?";
    $stmt=$db->prepare($query);
    $stmt->execute([$id]);
    /*if(count($stmt->fetchAll(PDO::FETCH_ASSOC))==0){
        http_response_code(404);
        return;
    }*/
    http_response_code(200);
}
function creteFood($db,$p){
    if(!isset($p['data_id']) || !isset($p['name'])||!isset($p['day']))
    {
        http_response_code(404);
        return;
    }
    $query="INSERT INTO food (data_id,name,price,day,img) VALUES (?,?,?,?,?)";
    $stmt=$db->prepare($query);
    $stmt->execute([$p['data_id'],$p['name'],$p['price'],$p['day'],$p['img']]);
    http_response_code(201);
}
?>