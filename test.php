<?php
function getServerTZOffset () {
  $tz = date_default_timezone_get();
  $t = new DateTimeZone("$tz");
  return $t->getOffset(new DateTime('now'));
}
?>
