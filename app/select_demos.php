<?php
  //connect to db - $link
  require_once "_includes/db_connect.php";
  
  //simple select
  $stmt = mysqli_prepare($link, "SELECT * FROM movies, favourites WHERE movies.movieID = favourites.movieID");
  

  //select from 2 tables
  //$stmt = mysqli_prepare($link, "SELECT movies.movieTitle, movies.otherInfo, genres.genre FROM movies, genres");

  //simple join from 2 tables relating movies.genreID (foreign key) to genres.genreID (primary key)
  //$stmt = mysqli_prepare($link, "SELECT movies.movieTitle, movies.otherInfo, genres.genre FROM movies, genres WHERE movies.genreID = genres.genreID");

  //join movies & favourites
  //$stmt = mysqli_prepare($link, "SELECT movies.movieTitle, movies.otherInfo FROM movies, favourites WHERE favourites.movieID = movies.movieID");

  //join users & favourites
  //$stmt = mysqli_prepare($link, "SELECT users.full_name, movies.movieTitle, movies.otherInfo FROM users, movies, favourites WHERE favourites.movieID = movies.movieID AND users.userID = favourites.userID");
  //note to self... play with adding removing favourites to Peter userID=2, i.e. movieID=3 (Black Adam)

  //count number of favourites
   //$stmt = mysqli_prepare($link, "SELECT movies.movieTitle, movies.otherInfo, COUNT(favourites.movieID) AS countedFavourites  FROM movies, favourites WHERE favourites.movieID = movies.movieID");

  //count number of favourites per movie
  //$stmt = mysqli_prepare($link, "SELECT movies.movieTitle, movies.otherInfo, COUNT(favourites.movieID) AS countedFavourites  FROM movies, favourites WHERE favourites.movieID = movies.movieID GROUP BY favourites.movieID");

  //count number of favourites per movie and show in order
  //$stmt = mysqli_prepare($link, "SELECT movies.movieTitle, movies.otherInfo, COUNT(favourites.movieID) AS countedFavourites  FROM movies, favourites WHERE favourites.movieID = movies.movieID GROUP BY favourites.movieID ORDER BY countedFavourites DESC");

  //count number of favourites per movie and show in order (even if they have no votes)
  // $stmt = mysqli_prepare($link, 
  // "SELECT movies.movieTitle, movies.otherInfo, 
  // COUNT(favourites.movieID) AS countedFavourites  
  // FROM favourites
  // RIGHT JOIN movies ON movies.movieID = favourites.movieID 
  // GROUP BY movies.movieID 
  // ORDER BY countedFavourites DESC");

  //favourites for users
  //$stmt = mysqli_prepare($link, "SELECT  users.full_name, movies.movieTitle, movies.otherInfo  FROM users, movies,  favourites WHERE favourites.movieID = movies.movieID AND favourites.userID = users.userID");


  //execute the statement / query from abobe
  mysqli_stmt_execute($stmt);

  //get results
  $result = mysqli_stmt_get_result($stmt);

  //loop through
  while($row = mysqli_fetch_assoc($result)){
    $results[] = $row;
  }

  //encode & display json
  echo json_encode($results);

  //close the link to the db
  mysqli_close($link);

?>