<?php

 function findme(){
    




 $User= ORM::for_table('tbl_customers')
    ->where('id', $_GET['router'])
    ->find_one();
    
    $router_id=$User->router_id;
    
    
    
     $router= ORM::for_table('tbl_routers')
    ->where('id', $router_id)
    ->find_one();
    


$data = array(
    'router_id' => $router_id,
    'router_name' => $router->name
);












// Output the JSON response
echo json_encode($data);
    
    
    
    
}