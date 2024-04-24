<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  require_once('db.php');
  require_once('functions.php');
  $data = json_decode(file_get_contents('php://input'));
  $userID = mysqli_real_escape_string($link, $data->{'userID'});

  $success = false;

  if($userID){
    $success = true;
    $sql = "SELECT * FROM immurUploads WHERE userID = $userID";
    $res = mysqli_query($link, $sql);
    $ret = [];
    $ret['assets count'] = mysqli_num_rows($res));
    echo json_encode([$success, $ret]);
  }else{
    echo json_encode([$success]);
  }
?>