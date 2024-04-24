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
    $assets = [];
    for($i=0; $i<mysqli_num_rows($res); ++$i){
      $row = mysqli_fetch_assoc($res);
      $obj = [
        'id' => $row['id'],
        'slug' => $row['slug'],
        'views' => $row['views'],
        'originalSlug' => $row['originalSlug'],
        'date' => $row['date'],
        'hash' => $row['hash'],
        'upvotes' => $row['upvotes'],
        'downvotes' => $row['downvotes'],
        'filetype' => $row['filetype'],
        'private' => $row['private'],
        'meta' => $row['meta'],
      ];
      $assets[] = $obj;
    }
    echo json_encode([$success, $assets]);
  }else{
    echo json_encode([$success]);
  }
?>