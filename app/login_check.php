<?php
  session_start();
  function checkLogin(){
    if($_SESSION['verify']){
      $results[] = [
        'verify'=>$_SESSION['verify'],
        'userID'=>$_SESSION["userID"]
      ];
    }else{
      $results[] = ['verify'=>false];
    }
    echo json_encode($results);
  }
  checkLogin();
?>