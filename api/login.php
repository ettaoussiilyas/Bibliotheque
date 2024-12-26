<?php 

session_start();
include '../classes/User.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $user = new User(null, $data->email, $data->password);
    $logged = $user->login();
    if($logged['success']){
        $_SESSION['name'] =$logged['name'];
        $_SESSION['id'] = $logged['id'];
        $_SESSION['role'] = $logged['role'];
        $_SESSION['email'] = $logged['email'];
        echo $logged['role'] === 'authenticated' ? json_encode(['success' => true, 'destination' => 'dashboard.php']) : json_encode(['success' => true, 'destination' => 'admin/dashboard.php']);
        
    }else{
        echo json_encode([
            'success'=> 'false'
        ]);
        exit;
    }

}

?>