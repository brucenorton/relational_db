<?php
  session_start();
  //session_destroy();
  unset($_SESSION['verify']);
  $results[] = ["verify"=>false];
  echo json_encode($results);

?>
