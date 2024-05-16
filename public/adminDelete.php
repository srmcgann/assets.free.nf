<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);

  require_once('db.php');
  require_once('functions.php');
  $data = json_decode(file_get_contents('php://input'));
  $userID = mysqli_real_escape_string($link, $data->{'userID'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $slug = mysqli_real_escape_string($link, $data->{'slug'});

  $resourceDir = "resources";

  $success = false;
  if($userID && $passhash){
    $sql = "SELECT * FROM imjurUsers WHERE id = $userID AND passhash LIKE BINARY \"$passhash\" AND admin = 1";
    $res = mysqli_query($link, $sql);
  }
  if(mysqli_num_rows($res)){
    
    $sql = "SELECT originalSlug FROM imjurUploads WHERE slug LIKE BINARY \"slug\"";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $originalSlug = $row['originalSlug'];
      $filetype = $row['filetype'];
      $originalID = alphaToDec($originalSlug);
      $sql = "SELECT * FROM imjurUploads WHERE originalSlug LIKE BINARY \"$originalSlug\"";
      $res = mysqli_query($link, $sql);
      for($i=0; $i<mysqli_num_rows($res); ++$i){
        $row = mysqli_fetch_assoc($res);
        $uid = $row['id'];
        $sql = "DELETE FROM imjurComments WHERE uploadID = $uid";
        mysqli_query($link, $sql);
        $sql = "DELETE FROM imjurVotes WHERE uploadID = $uid";
        mysqli_query($link, $sql);
        $sql = "DELETE FROM imjurUploads WHERE id = $uid";
        mysqli_query($link, $sql);
        $sql = "SELECT * FROM imjurCollections";
        $res2 = mysqli_query($link, $sql);
        for($j = 0; $j<mysqli_num_rows($res2); ++$j){
          $row2 = mysqli_fetch_assoc($res2);
          $meta = $row2['meta'];
          $cid = $row2['id'];
          $slugs = $meta->{'slugs'};
          $newSlugs = [];
          forEach($slugs as $slug_){
            if($slug != $slug_) $newSlugs[] = $slug;
          }
          $meta->{'slugs'} = $newSlugs;
          $meta = mysqli_real_escape_string($link, json_encode($meta));
          $sql = "UPDATE imjurCollections SET meta = \"$meta\" WHERE id = $cid";
          mysqli_query($link, $sql);
        }

        $sql = "SELECT * FROM imjurFeaturedItems";
        $res2 = mysqli_query($link, $sql);
        for($j = 0; $j<mysqli_num_rows($res2); ++$j){
          $row2 = mysqli_fetch_assoc($res2);
          $meta = $row2['meta'];
          $cid = $row2['id'];
          $slugs = $meta->{'slugs'};
          $newSlugs = [];
          forEach($slugs as $slug_){
            if($slug != $slug_) $newSlugs[] = $slug;
          }
          $meta->{'slugs'} = $newSlugs;
          $meta = mysqli_real_escape_string($link, json_encode($meta));
          $sql = "UPDATE imjurFeaturedItems SET meta = \"$meta\" WHERE id = $cid";
          mysqli_query($link, $sql);
        }
      }
      
      $filename = "$resourceDir/$originalSlug" . getSuffix($filetype);
      unlink($filename);
      $success = true;
    }
  }else{
    echo json_encode([$success, $sql]);
  }
?>