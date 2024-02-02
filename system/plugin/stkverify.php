<?php



function stkverify()
{
    
    
    
    
    
 $username=$_GET['username'];
    
    
    
                  $user = ORM::for_table('tbl_payment_gateway')
                  ->where('username', $username)
                  ->order_by_desc('id')
                   ->find_one();


                   $userid = ORM::for_table('tbl_customers')
                   ->where('username', $username)
                   ->order_by_desc('id')
                    ->find_one();

                   $UserId=$userid->id;
                   
                   
                  $mpesacode=$user->gateway_trx_id;
                  
                 $res=$user->pg_paid_response;
                   
                   
                   
               //    echo $res;
                   
                   
    
    if($user){
        
        
        
      $status=$user->status;
        
        
     // Check if the transaction is successful and mpesacode is present
     if ($status == 2 && !empty($mpesacode)) {
        // Recharge only the user who initiated the transaction
       
          
            $data = array(
                "Resultcode" => "2",
                "Redirect" => "home",
                "Message" => "<script>toastr.success('Transaction Complete ');</script>",
                "Message1" => "Transaction Complete ",
                "Status" => "success"
            );
      
        echo json_encode($data);
        exit();
    }

        
        if($res=="User failed to enter pin"){
            
         
            $data = array(
           "Resultcode" => "0",
           "Message" => " <script>toastr.error(' You failed to enter Mpesa pin');</script>",
           "Message1" => " Failed to Enter Mpesa pin>",
           "Status" => "danger"
     
       );
       
   echo    $message = json_encode($data);
         
            exit();
            
            
            
            
            
        }
        
        
        
          if($res=="Not enough balance"){
            
         
            $data = array(
           "Resultcode" => "0",
           "Message" => " <script>toastr.error('Not enough mpesa balance');</script>",
           "Message1" => "Insuficient Balance for the transaction",
           "Redirect" => "https://smarthostingkenya.com/ipn/flutterwave/error",
           "Status" => "danger"
     
       );
       
   echo    $message = json_encode($data);
         
            exit();
            
            
            
            
            
        }
        
        
          if($res=="Wrong Mpesa pin"){
            
         
            $data = array(
           "Resultcode" => "0",
           "Message" => " <script>toastr.error('Wrong Mpesa pin');</script>",
           "Message1" => "Wrong Mpesa pin",
           "Redirect" => "https://smarthostingkenya.com/ipn/flutterwave/error",
           "Status" => "danger"
     
       );
       
   echo    $message = json_encode($data);
         
            exit();
            
            
            
            
            
        }
        
        
        if($status==4){
            
            
               $data = array(
           "Resultcode" => "0",
           "Message" => " <script>toastr.error(' Transaction Cancelled');</script>",
           "Message1" => " Transaction Cancelled",
           "Redirect" => "https://smarthostingkenya.com/ipn/flutterwave/error",
           "Status" => "danger"
     
       );
       
   echo    $message = json_encode($data);
            
            exit();
            
        }
        
        
        
           if(empty($mpesacode)){
                      
                     
                         $data = array(
           "Resultcode" => "1",
           "Message" => "",
           "Message1" => "Enter Pin to continue",
           "Redirect" => "https://smarthostingkenya.com/ipn/flutterwave/error",
           "Status" => "primary"
     
       );
       
   echo    $message = json_encode($data);
                      
                      exit();
                  }
        
        
        
        
        
        
    }
    
    
    
}
