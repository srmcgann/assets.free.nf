<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  require_once('db.php');
  require_once('functions.php');
  $userID = '';
  $passhash = '';
  $data = json_decode(file_get_contents('php://input'));
  $preval = $data->{'userID'};
  if($preval) $userID = mysqli_real_escape_string($link, $preval);
  $preval = $data->{'passhash'};
  if($preval) $passhash = mysqli_real_escape_string($link, $preval);
  $collectionID = mysqli_real_escape_string($link, $data->{'collectionID'});
  
  $anon = $success = false;

  $enabled = 0;
  if($passhash){
    $sql = "SELECT * FROM imjurUsers WHERE id = $userID AND passhash LIKE BINARY\"$passhash\"";
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    $admin = $row['admin'];
    $enabled = $row['enabled'];
  }else{
    $anon = true;
  }

  if(!$anon && !$enabled) $userID = $false;

  if($userID){
    $success = true;
    $sql = "SELECT * FROM immurUploads WHERE userID = $userID AND ($enabled OR NOT private)";
    $res = mysqli_query($link, $sql);
    $ret = [];
    $assets = [];
    for($i=0; $i<mysqli_num_rows($res); ++$i){
      $row = mysqli_fetch_assoc($res);
      $obj = [
        'id'              => $row['id'],
        'slug'            => $row['slug'],
        'views'           => $row['views'],
        'originalSlug'    => $row['originalSlug'],
        'date'            => $row['date'],
        'hash'            => $row['hash'],
        'upvotes'         => $row['upvotes'],
        'downvotes'       => $row['downvotes'],
        'filetype'        => $row['filetype'],
        'private'         => $row['private'],
        'meta'            => $row['meta'],
      ];
      $assets[] = $obj;
    }
    echo json_encode([$success, $assets]);
  }else{
    echo json_encode([$success]);
  }
?>