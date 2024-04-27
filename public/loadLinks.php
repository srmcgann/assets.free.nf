<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(0);
  require_once('db.php');
  require_once('functions.php');
  $data = json_decode(file_get_contents('php://input'));
  $slugs = $data->{'slugs'};
  $forCollection = $data->{'forCollection'};
  $collectionID = mysqli_real_escape_string($link, $data->{'collectionID'});

  $sql = "SELECT * FROM imjurUploads WHERE slug LIKE BINARY";
  $ct = sizeof($slugs);
  $ct_ = 0;
  if(!$ct) die(json_encode([false]));
  forEach($slugs as $slug){
    $sql .= " \"$slug\"" . ($ct_ == $ct-1 ? '' : ' OR slug LIKE BINARY');
    $ct_++;
  }
  
  $res = mysqli_query($link, $sql);
  $uploadDir = 'uploads';
  $links = [];
  $meta = [];
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    $slug = $row['slug'];
    $m = [
      'id'           => $row['id'],
      'slug'         => $slug,
      'hash'         => $row['hash'],
      'size'         => json_decode($row['meta'])->{'file size'},
      'name'         => $row['name'],
      'filetype'     => $row['filetype'],
      'date'         => $row['date'],
      'userID'       => $row['userID'],
      'origin'       => $row['origin'],
      'upvotes'      => $row['upvotes'],
      'private'      => $row['private'],
      'downvotes'    => $row['downvotes'],
      'views'        => $row['views'],
      'description'  => $row['description'],
      'originalSlug' => $row['originalSlug'],
      'originalDate' => $row['originalDate'],
      'serverTZO'    => getServerTZOffset(),
    ];
    $suffix = getSuffix($row['filetype']);
    $originalSlug = $row['originalSlug'];
    $links[] = "$uploadDir/$slug.$suffix";
    $meta[] = $m;
  }
  if(sizeof($links)){
    echo json_encode([true, $links, $meta]);
  }else{
    // slug(s) were not found in database. it needs to be removed from the collection.
    // this shouldn't generally occur, except during dev when strangeness happens
    // normally, deleting an asset performs this function (in delete.php)
    if($ct_ && $forCollection && $collectionID != -1){
      $sql = "SELECT * FROM imjurCollections WHERE id = $collectionID";
      $res = mysqli_query($link, $sql);
      if(mysqli_num_rows($res)){
        $row = mysqli_fetch_assoc($res);
        $meta = json_decode($row['meta']);
        $cslugs = $meta->{'slugs'};
        $newSlugs = [];
        forEach($cslugs as $cslug){
          $cull = false;
          forEach($slugs as $slug){
            if($slug == $cslug) $cull = true;
          }
          if(!$cull) $newSlugs[] = $cslug;
        }
        $meta->{'slugs'} = $newSlugs;
        $newMeta = json_encode($meta);
        $sql = "UPDATE imjurCollections SET meta = \"$newMeta\" WHERE id = $collectionID";
        mysqli_query($link, $sql);
      }
    }
    echo json_encode([false, $sql, $ct, $ct_, $slugs]);
  }
?>