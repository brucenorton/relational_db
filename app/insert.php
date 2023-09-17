<?php
  require_once "_includes/db_connect.php";

  // ** based on demo_db app/insert_v3.php **


  $results = [];
  $insertedRows = 0;

  //3 functions abstracted from main code
  function movieExists($link){
    //need to pass db $link to the function due to scope
    $query = "SELECT * FROM movies WHERE movieTitle = ?";

    if($stmt = mysqli_prepare($link, $query)){
      mysqli_stmt_bind_param($stmt, "s", $_REQUEST["movie"]);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      //$results[] = ["mysqli_num_rows" => mysqli_num_rows($result)];
       //should only be 1 record... but I'm lazy so still "looping"
      while($row = mysqli_fetch_assoc($result)){
        $results[] = $row;
      }
      return $results;
      //return mysqli_num_rows($result) > 0;

    }else{
      throw new Exception("No user was found");
    }
  }

  function updateData($link){
    $query = "UPDATE demo SET tvshow = ? WHERE email = ?";

    if($stmt = mysqli_prepare($link, $query)){
      mysqli_stmt_bind_param($stmt, "ss", $_REQUEST["tvshow"], $_REQUEST["email"]);
      mysqli_stmt_execute($stmt);
      
      if (mysqli_stmt_affected_rows($stmt) <= 0) {
        throw new Exception("Error updating data: " . mysqli_stmt_error($stmt));
      }
      $results[] = ["updatedData() affected_rows man" => mysqli_stmt_affected_rows($stmt)];
      return mysqli_stmt_affected_rows($stmt);
    }
  }

  function insertData($link){
    $query = "INSERT INTO demo (name, email, tvshow) VALUES (?, ?, ?)";

    if($stmt = mysqli_prepare($link, $query)){
      mysqli_stmt_bind_param($stmt, "sss", $_REQUEST["full_name"], $_REQUEST["email"], $_REQUEST["tvshow"]);
      mysqli_stmt_execute($stmt);
      $insertedRows = mysqli_stmt_affected_rows($stmt);

      if($insertedRows > 0){
        $results[] = [
          "insertedRows"=>$insertedRows,
          "id" => $link->insert_id,
          "full_name" => $_REQUEST["full_name"],
          "tvshow" => $_REQUEST["tvshow"]
        ];
      }else{
        throw new Exception("No rows were inserted");
      }
      //removed the echo from here
      //echo json_encode($results);
    }
  }

  //main logic of the application is in this try{} block of code.
  try{
    //see if user has entered data
    if(!isset($_REQUEST["movie"]) || !isset($_REQUEST["genre"])){
      throw new Exception('Required data is missing i.e. movie, genre');
    }else{
      //get userID from $_SESSION
      $userID = $_SESSION['userID'];
      //first see if movieTitle is already in the db.
      if(!movieExists($link)){
        $results[] = ["movieExists()" => false];
       // then insert new movie & favourite. See if genre exists
       // or do this all in the front end!!
      }else{
        $results[] = ["movieExists()" => true];

        //if user does not exist, insert the data
        //$results[] = ["insertData()" => "called insertData()"];
        //$results[] = ["insertData() affected_rows" => insertData($link)];
      }
    }
      
  }catch(Exception $error){
    //add to results array rather than echoing out errors
    $results[] = ["error"=>$error->getMessage()];
  }finally{
    //echo out results
    echo json_encode($results);
  }
 
?>