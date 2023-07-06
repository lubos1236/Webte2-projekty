<?php
require_once("../Zadanie2/config.php");
require_once("../Zadanie2/header.php");
$pages=array(
    array("name"=> "FIIT-Food","url"=>"http://www.freefood.sk/menu/#fiit-food"),
    array("name"=> "Mlynska Koliba","url"=>"https://mlynskakoliba.sk/"),
    array("name"=> "Eat&Meet","url"=>"http://eatandmeet.sk/tyzdenne-menu")
);




if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if(isset($_POST['Download']))
    {
        foreach ($pages as $page)
        {
            downloadPage($db,$page["name"],$page["url"]);
        }
        
    }
    if(isset($_POST['Parse']))
    {
        parseFiitFood($db);
        parsemlynskakoliba($db);
        parseEatAndMeet($db);
    }
    if(isset($_POST['Delete']))
    {
        $query="DELETE FROM data";
        $stmt=$db->prepare($query);
        $stmt->execute();

        $query="DELETE FROM food";
        $stmt=$db->prepare($query);
        $stmt->execute();
    }

}


function downloadPage($db, $name, $url){
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $page=curl_exec($ch);
    curl_close($ch);
    
    $query="INSERT INTO data (name, code) VALUES (?,?)";
    $stmt=$db->prepare($query);
    $stmt->execute([$name,$page]);
/*
    if ($stmt->execute([$name,$page])) {
        echo "Stranka ulozena.";
    } else {
        echo "Ups. Nieco sa pokazilo";
    }*/

    unset($stmt);
}
function parseFiitFood($db){
    $query="SELECT id, code FROM data
    WHERE name= 'FIIT-Food'
    ORDER BY id DESC
    LIMIT 1;";
    $stmt=$db->prepare($query);
    $stmt->execute();
    $data=$stmt->fetch(PDO::FETCH_ASSOC);
    
    $id=$data['id'];
    $data=$data['code'];
    $dom=new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($data);
    libxml_clear_errors();
    $menu=$dom->getElementById("fiit-food")->getElementsByTagName("ul")->item(0)->childNodes;
    for($i=1;$i<=5;$i++)
    {
        $dayMenu=$menu->item($i);
        $day=$dayMenu->childNodes->item(0)->nodeValue;
        $day=ucfirst(explode(',', $day)[0]);
        $foods=$dayMenu->childNodes->item(1)->childNodes;
        //echo $day; //save
        //echo "</br>";
        //echo "</br>";
        //echo "</br>";
        foreach($foods as $food)
        {
            if( $food->childNodes->count()<3)
                continue;
            $foodName=$food->childNodes->item(1)->nodeValue;//save
            $foodPrice=$food->childNodes->item(2)->nodeValue;//save
            //echo $foodName.' , '. $foodPrice;
            //echo $foodPrice;
            //echo "</br>";
            addFoodToDatabase($db,$id,$foodName,$foodPrice,$day,null);
        }
    }
}

function parsemlynskakoliba($db){
    $query="SELECT id, code FROM data
    WHERE name= 'Mlynska Koliba'
    ORDER BY id DESC
    LIMIT 1;";
    $stmt=$db->prepare($query);
    $stmt->execute();
    $data=$stmt->fetch(PDO::FETCH_ASSOC);
    
    $id=$data['id'];
    $data=$data['code'];
    //var_dump($data);
    $dom=new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($data);
    libxml_clear_errors();

    //var_dump($dom);

    $menu=$dom->getElementById("done-section")->childNodes;
    for($i=2;$i<=6;$i++)
    {
        $dayMenu=$menu->item($i);
        $day=$dayMenu->firstChild->firstChild->firstChild->nodeValue; //save
        //echo $day."</br>";
        $foods=$dayMenu->firstChild->firstChild->childNodes->item(1)->childNodes;

        //var_dump($foods->nodeValue);

        foreach($foods as $food)
        {
            $foodName=$food->nodeValue; //save
            //echo $foodName;
            //echo "</br>";
            addFoodToDatabase($db,$id,$foodName,null,$day,null);
        }


        //echo "</br></br>";
    }

}

function parseEatAndMeet($db){
    $query="SELECT id, code FROM data
    WHERE name= 'Eat&Meet'
    ORDER BY id DESC
    LIMIT 1;";
    $stmt=$db->prepare($query);
    $stmt->execute();
    $data=$stmt->fetch(PDO::FETCH_ASSOC);

    $id=$data['id'];
    $data=$data['code'];
    $dom=new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($data);
    libxml_clear_errors();

    $day1=$dom->getElementById("day-1");
    $day2=$dom->getElementById("day-2");
    $day3=$dom->getElementById("day-3");
    $day4=$dom->getElementById("day-4");
    $day5=$dom->getElementById("day-5");
    $day6=$dom->getElementById("day-6");
    $day7=$dom->getElementById("day-7");

    $menu=array($day1,$day2,$day3,$day4,$day5,$day6,$day7);
    $days=array("Pondelok","Utorok","Streda","Štvrtok","Piatok","Sobota","Nedeľa");
    $i=0;

    foreach($menu as $dayMenu)
    {
        $day=$days[$i];
        //echo $dayMenu->nodeValue;
        //echo "</br></br>";


        foreach($dayMenu->childNodes as $food)
        {
            if($food->childNodes->count()==0)
                continue;
            if ($food->childNodes->item(1)->childNodes->item(3)==null)
                continue;
            
            $foodPrice=$food->childNodes->item(1)->childNodes->item(3)->childNodes->item(1)->childNodes->item(3)->nodeValue; //save
            $foodPrice=explode('/',$foodPrice)[0];
            $foodName=$food->childNodes->item(1)->childNodes->item(3)->childNodes->item(1)->childNodes->item(1)->nodeValue." ".$food->childNodes->item(1)->childNodes->item(3)->childNodes->item(3)->nodeValue; //save
            $img=$food->getElementsByTagName('img')->item(0)->getAttribute('src');//save
            
            //echo $foodName;//.' '. $foodPrice;
            //echo "</br></br>";
            addFoodToDatabase($db,$id,$foodName,$foodPrice,$day,$img);
        }
        $i++;
    }

}
function addFoodToDatabase($db,$id,$name,$price,$day,$img){
    if(checkIfExist($db,$id,$name,$price,$day,$img)==true)
        return;
    $query="INSERT INTO food (data_id,name,price,day,img)
    VALUES (?,?,?,?,?);";
    $stmt=$db->prepare($query);
    $stmt->execute([$id,$name,$price,$day,$img]);
}
function checkIfExist($db,$id,$name,$price,$day,$img){
    if($price==null && $img==null)
    {
        $query="SELECT data_id,name,price,day,img FROM food 
        WHERE data_id=? AND name=? AND price IS NULL AND day=? and img IS NULL;";
        $stmt=$db->prepare($query);
        $stmt->execute([$id,$name,$day]);
    }
    else if($img==null)
    {
        $query="SELECT data_id,name,price,day,img FROM food 
        WHERE data_id=? AND name=? AND price=? AND day=? and img IS NULL;";
        $stmt=$db->prepare($query);
        $stmt->execute([$id,$name,$price,$day]);
    }
    else{
        $query="SELECT data_id,name,price,day,img FROM food 
        WHERE data_id=? AND name=? AND price=? AND day=? and img=?;";
        $stmt=$db->prepare($query);
        $stmt->execute([$id,$name,$price,$day,$img]);
    }
    if($stmt->rowCount()==0)
        return false;
    return true;

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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.4/axios.min.js"></script>

    <title>Zadanie2</title>
</head>
<body>

    <div class="card-body container">
        <form method="post">
            <input type="submit" name="Download" value="download" class="btn btn-success">
            <input type="submit" name="Parse" value="parse" class="btn btn-info" >
            <input type="submit" name="Delete" value="delete" class="btn btn-danger">
        </form>
    </div>

    <div class="card-body container">
        <select name="restaurant" id="restaurant">
            <option id="s1" value="0">FIIT-Food</option>
            <option id="s2" value="1">Mlynska Koliba</option>
            <option id="s3" value="2">Eat&Meet</option>
        </select>
        <input type="submit" value="DELETE" class="btn btn-danger" onclick="deleteRestaurantMenu()">
        <a href='#createFoodModal' class='edit' data-toggle='modal'> <button type='button' class='btn btn-success '>ADD</button></a>
    </div>
    

    
    

    <table id="table" style="width: 90%;">
        <thead><tr><td>Reštaurácia</td><td>Jedlo</td><td>Cena</td><td>Deň</td><td>Obrázok</td><td>Edit</td></tr></thead>
        <tbody id="tbody">
        </tbody>
    </table>


    <div id="editPriceModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form>
					<div class="modal-header">						
						<h4 class="modal-title">Nastaviť cenu</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">				
                    <div class="form-group">
	                	<label>Cena</label>
	                	<input id="priceField" type="text" class="form-control" name="price" required>
                        <input id="idField" type="hidden" class="form-control" name="price" readonly>
	                </div>
                    </div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-success" data-dismiss="modal" name="add" value="Edit" onclick="updatePrice()" >
					</div>
				</form>
			</div>
		</div>
	</div>



    <div id="createFoodModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form>
                <div class="modal-header">						
						<h4 class="modal-title">Pridanie nového jedla</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
                    <div class="form-group">
	                	<label>Názov</label>
	                	<input id="foodName" type="text" class="form-control" name="foodName" required>
	                </div>

                    <div class="form-group">
	                	<label>Cena</label>
	                	<input id="foodPrice" type="text" class="form-control" name="foodPrice">
	                </div>

                    <div class="form-group">
	                	<label>Deň</label>
	                	<select name="foodDay" id="foodDay" class="form-control">
                        <option value="Pondelok">Pondelok</option>
                        <option value="Utorok">Utorok</option>
                        <option value="Streda">Streda</option>
                        <option value="Štvrtok">Štvrtok</option>
                        <option value="Piatok">Piatok</option>
                        <option value="Sobota">Sobota</option>
                        <option value="Nedeľa">Nedeľa</option>
                    </select>
	                </div>

                    

					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-success" data-dismiss="modal" name="add" value="Add" onclick="createFood()" >
					</div>
				</form>
			</div>
		</div>
	</div>

<script>
    $(document).ready(function(){
        $('#table').DataTable({
            language : {
            "zeroRecords": " "             
        },
            paging: false,
            ordering:false,
            searching:false,
            info:false
        })
    })

    fillTable()

    async function addValueToPriceField(e){
        let row=e.target.parentElement.parentElement.parentElement;
        let priceField=document.getElementById("priceField");
        let idField=document.getElementById("idField");
        priceField.value=row.children.item(3).innerText;
        idField.value=row.children.item(0).value;
    }
    async function updatePrice(){
        let price=document.getElementById("priceField").value;
        let id=document.getElementById("idField").value;
        console.log(price);
        console.log(id);

        try{
            const params=new URLSearchParams();
            params.append('id',id);
            params.append('price',price);

            res=await axios.put("https://site238.webte.fei.stuba.sk/H5NS32KL12/Zadanie2/api.php",params);
        }
        catch(e){
            alert(e);
        }
        fillTable();
    }
    async function deleteRestaurantMenu(){
        let restaurant=document.getElementById('restaurant');
        console.log(restaurant.value);

        let res;
        try{
            res=await axios.delete("https://site238.webte.fei.stuba.sk/H5NS32KL12/Zadanie2/api.php",{params:{id: restaurant.value}});
        }
        catch(e){
            alert(e);
        }
        fillTable();
    }
    async function createFood(){
        let restaurant=document.getElementById('restaurant').value;
        let foodName=document.getElementById('foodName').value;
        let foodPrice=document.getElementById('foodPrice').value;
        let foodDay=document.getElementById('foodDay').value;

        console.log(restaurant.value);
        console.log(foodName);
        console.log(foodPrice);
        console.log(foodDay);

        let res;
        try{
            const params=new URLSearchParams();
            params.append('data_id',restaurant)
            params.append('name',foodName)
            params.append('price',foodPrice)
            params.append('day',foodDay)
            params.append('img',null)
            res=await axios.post("https://site238.webte.fei.stuba.sk/H5NS32KL12/Zadanie2/api.php",params);
        }
        catch(e){
            alert(e);
        }
        fillTable();
    }
        

    

















    async function fillTable(){

        let r1="FIIT-Food";
        let r2="Mlynska Koliba";
        let r3="Eat&Meet";

        let body=document.getElementById("tbody");
        body.innerHTML='';
        data=await getData();
        data.forEach(element => {
            if(element.price==null)
                element.price='';
            if(element.img==null)
                element.img='';
            

            let row="<tr>"+
            "<input type='hidden' value='"+element.id+"'>"+
            "<td>"+element.restaurant+"</td>"+
            "<td>"+element.name+"</td>"+
            "<td>"+element.price+"</td>"+
            "<td>"+element.day+"</td>"+
            (element.img==''?"<td></td>" :"<td><img src='"+element.img+"'></td>")+
            "<td> <a href='#editPriceModal' class='edit' data-toggle='modal'> <button type='button' class='btn btn-info '>Edit</button></a> </td>"+
            "</tr>"
            body.innerHTML+=row;



            let s1=document.getElementById('s1');
            let s2=document.getElementById('s2');
            let s3=document.getElementById('s3');
            if(r1.localeCompare(element.restaurant)==0)
                s1.value=element.data_id;
            else if(r2.localeCompare(element.restaurant)==0)
                s2.value=element.data_id;
            else if(r3.localeCompare(element.restaurant)==0)
                s3.value=element.data_id;



            

        });

        let bList=document.getElementsByClassName('edit');
        for(let button of bList){
            button.addEventListener('click', addValueToPriceField);
        }
        



    }    



    async function getData(){
        let res;
        try{
            res=await axios.get("https://site238.webte.fei.stuba.sk/H5NS32KL12/Zadanie2/api.php");
        }
        catch(e){
            alert(e);
        }
        return res.data;

    }
</script>
<style>
    img{
        height: 50px;
        width: 50px;
    }
</style>
</body>
</html>