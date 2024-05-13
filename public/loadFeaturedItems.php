<?php
  require_once('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $sql = 'SELECT * FROM imjurFeaturedItems';
  $res = mysqli_query($link, $sql);
  $ret = [];
  $success = false;
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    $ret[] = $row['meta'];
    $success = true;
  }
  echo json_encode([$success, $ret]);
?>
