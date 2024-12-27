<?php 

session_start();
include '../classes/User.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $user = new User();
    if($user->register($data->name, $data->email, $data->password)){
        echo json_encode([
            'success' => 'true'
        ]);
        exit;
    }else{
        echo json_encode([
            'success' => 'false'
        ]);
        exit;
    }

}

?>