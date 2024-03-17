<?php

 function finduser(){
    



$users = ORM::for_table('tbl_customers')
    ->where('router_id', $_GET['router'])
    ->find_many();

$data = array();

foreach ($users as $user) {
    // $username = $user->username;
    
    // Assuming $router is defined elsewhere
    // You can uncomment your code if $router is defined properly
    // $router = ORM::for_table('tbl_routers')
    //     ->where('id', $router_id)
    //     ->find_one();
    
    // Assuming $router is an instance of the router model with 'name' attribute
    // $router_name = $router->name;

    // Collecting data for each user
    $user_data = array(
        'username' => $user->username,
        'phone' => $user->phonenumber,
        'email' => $user->email,
        'id' => $user->id
    );

    // Adding user data to the data array
    $data[] = $user_data;
}










// Output the JSON response
echo json_encode($data);
    
    
    
    
}