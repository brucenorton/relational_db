<?php
  session_start();
  require_once "_includes/db_connect.php";
  /* notes
    -need to deal with genres
      --allow users to add or predetermine
      --similar to movies
    --need to make sure favourites don't repeat


    */

  function getMovieID($link){
    $stmt = mysqli_prepare($link, "SELECT movies.movieID FROM movies WHERE movies.movieTitle = ?");
    mysqli_stmt_bind_param($stmt, "s", trim($_REQUEST["movie"]));
    //execute the statement / query from abobe
    mysqli_stmt_execute($stmt);

    //get results
    $result = mysqli_stmt_get_result($stmt);
    mysqli_num_rows($result);

    //loop through
    while($row = mysqli_fetch_assoc($result)){
      $results[] = $row;
    }
    //echo $results[0]["movieID"];
    //return $results[0]["movieID"];
    if(mysqli_num_rows($result)){
      insertFavourite($link, $results[0]["movieID"]);
    }else{
      //insert movie and genre
      insertMovie($link);
    }
    

  }

  function insertFavourite($link, $movieID){
    $query = "INSERT INTO favourites (userID, movieID) VALUES (?, $movieID)";
    $userID = $_SESSION["userID"];
    //echo $userID;
    if($stmt = mysqli_prepare($link, $query)){
      mysqli_stmt_bind_param($stmt, "s", $userID);
      mysqli_stmt_execute($stmt);
      $insertedRows = mysqli_stmt_affected_rows($stmt);

      if($insertedRows > 0){
        $results[] = [
          "insertedRows"=>$insertedRows,
          "id" => $link->insert_id
        ];
      }else{
        throw new Exception("No rows were inserted: ".$_SESSION["userID"]);
      }
      //removed the echo from here
      echo json_encode($results);
    }
  }

  function insertMovie($link){
    $query = "INSERT INTO movies (movieTitle, otherInfo) VALUES (?,?)";
    //echo $userID;
    if($stmt = mysqli_prepare($link, $query)){
      mysqli_stmt_bind_param($stmt, "ss", $_REQUEST["movie"], $_REQUEST["info"]);
      mysqli_stmt_execute($stmt);
      $insertedRows = mysqli_stmt_affected_rows($stmt);

      if($insertedRows > 0){
        $results[] = [
          "insertedRows"=>$insertedRows,
          "id" => $link->insert_id
        ];
      }else{
        throw new Exception("No rows were inserted: ".$_SESSION["userID"]);
      }

      //now insert into "favourites table"
      insertFavourite($link, $link->insert_id);
      //removed the echo from here
      echo json_encode($results);
    }
  }

  //main logic of the application is in this try{} block of code.
  try{
    //see if user has entered data
    if(!isset($_REQUEST["movie"])|| !isset($_SESSION["userID"])){
      throw new Exception('Required data is missing i.e. movie, userID');
    }else{
      //get movieID then insert favourite
      getMovieID($link);
    }

      
  }catch(Exception $error){
    //add to results array rather than echoing out errors
    $results[] = ["error"=>$error->getMessage()];
    echo json_encode($results);
  }
 
?>