<?php 

//echo $_GET['callback'].'('.json_encode($results).')';

if(isset($_GET['callback'])){ // Si ha llegado callback

         echo $_GET['callback'].'('.json_encode($data).')';    
}
else{
        echo json_encode($data);
}