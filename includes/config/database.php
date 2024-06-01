<?php 
//Creamos una funcion en la cual utilizamos mysqli_connect para conectarnos a la base de datos
function conectarDB() : mysqli{
    $db = mysqli_connect('Localhost', 'root', 'root', 'bienesraices_crud');

   
    if(!$db){
        echo "Error, no nos conectamos";
        exit;
    }
    return $db;
}