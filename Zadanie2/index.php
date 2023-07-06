<?php
require_once("../Zadanie2/config.php");
require_once("../Zadanie2/header.php");
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
    <form method="post">
        

        <select style="width: 200px;" name="days" id="days" class="form-select" aria-label="Default select example">
            <option value="All">Všetky dni</option>
            <option value="Pondelok">Pondelok</option>
            <option value="Utorok">Utorok</option>
            <option value="Streda">Streda</option>
            <option value="Štvrtok">Štvrtok</option>
            <option value="Piatok">Piatok</option>
            <option value="Sobota">Sobota</option>
            <option value="Nedeľa">Nedeľa</option>
        </select>
    </form>

    
    

    <table id="table" style="width: 90%;">
        <thead><tr><td>Reštaurácia</td><td>Jedlo</td><td>Cena</td><td>Deň</td><td>Obrázok</td></tr></thead>
        <tbody id="tbody">
        </tbody>
    </table>
    
<script>
    $(document).ready(function(){
        $('#table').DataTable({
            language : {
            "zeroRecords": " "             
        },
            paging: false,
            ordering:false,
            searching:false,
        })
    })

    let table=document.getElementById("days")
    table.addEventListener("change",GetDayData);

    
    setup();
    
    
    async function GetDayData(e){
        let res;
        if(e.target.value=='All')
            setup();
        try{
            res=await axios.get("https://site238.webte.fei.stuba.sk/H5NS32KL12/Zadanie2/api.php",{params:{day: e.target.value}});
        }
        catch(e){
            alert(e);
        }
        fillTable(res.data)

    }
    async function setup(){
        fillTable(await getData());
    }

    async function fillTable(data){
        let body=document.getElementById("tbody");
        body.innerHTML='';
        data.forEach(element => {
            let row=document.createElement("tr");
            let e=document.createElement('td');
            e.innerText=element.restaurant;
            row.appendChild(e);
            e=document.createElement('td');
            e.innerText=element.name;
            row.appendChild(e);
            e=document.createElement('td');
            e.innerText=element.price;
            row.appendChild(e);
            e=document.createElement('td');
            e.innerText=element.day;
            row.appendChild(e);
            if(element.img!=null)
            {
                e=document.createElement('img');
                e.setAttribute('src', element.img);
                row.appendChild(e);
            }
            
            body.append(row);

        });
        
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