<?php
  //Peter O's chat app: https://github.com/peteee/single-file-chat-app/tree/main
  // login.php based select.php
  session_start();
  require_once '_includes/db_connect.php';
  //check_login from js? in external file or here? 
  //require_once 'login_check.php';

  function selectUser($link){
    $stmt = mysqli_prepare($link, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $_REQUEST["email"]);
    mysqli_stmt_execute($stmt);
    //get results
    $result = mysqli_stmt_get_result($stmt);

    //should only be 1 record... but I'm lazy so still "looping"
    while($row = mysqli_fetch_assoc($result)){
      $results[] = $row;
    }
    if (!mysqli_num_rows($result)) {
      $results[]= ['verify'=>true];
      return $results;
      throw new Exception('user_does_not_exist' . mysqli_stmt_error($stmt));
    }else{
      if(password_verify($_REQUEST['password'], $results[0]['password'])){
        $results[]= ['verify'=>true];
        $results[]= ['updated'=>'20/09/2023'];//just for testing... can be deleted
        return $results;
        
      }else{
        $results[] = ["verify"=>false];
        return $results;
        
      }
    }
  }

  //updated 20/09/2023
  //abstracted out session info
  function setSession($results){
    
    $_SESSION['verify'] = true;
    $_SESSION['userID'] = $results[0]['userID'];
    $_SESSION['full_name'] = $results[0]['full_name'];
    $_SESSION['email'] = $results[0]['email'];

    //Boolean switch type variable
    $_SESSION['logged_in'] = true;
  }

  function insertNewUser($link){
    $query = "INSERT INTO users (email, password) VALUES (?, ?)";

    if($stmt = mysqli_prepare($link, $query)){
      //cost slows server, but makes it more secure.
      $options = [
        'cost' => 12,
      ];
      $password = password_hash($_REQUEST["password"], PASSWORD_DEFAULT, $options);
      mysqli_stmt_bind_param($stmt, "ss", $_REQUEST["email"], $password);
      mysqli_stmt_execute($stmt);
      $insertedRows = mysqli_stmt_affected_rows($stmt);

      if($insertedRows > 0){
        $results[] = [
          "insertedRows"=>$insertedRows,
          "id" => $link->insert_id,
          "email" => $_REQUEST["email"],
          "verify"=> true
        ];
        setSession($results);
      }else{
        throw new Exception("No rows were inserted");
      }
      return $results;
    }
  }


 //main logic of the application is in this try{} block of code.
  try{
   
    //see if user has entered data **removed full_name & email
    if(!isset($_REQUEST["email"]) || !isset($_REQUEST["password"])){
      throw new Exception('Required data is missing i.e. demoID or tvshow');
    }else{
      $results = selectUser($link);
      if($results[1]['verify']){
        //abstract out adding session info?
        setSession($results);
      }else{
        //if $results[1]['verify']=false
        $results[] = insertNewUser($link);
      }
      
    }
  }catch(Exception $error){
    //add to results array rather than echoing out errors
    $results[] = ["error"=>$error->getMessage()];
  }finally{
    //echo out results
    echo json_encode($results);
  }

  //close the link to the db
  mysqli_close($link);

?>