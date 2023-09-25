<?php
  //connect to db - $link
  require_once "_includes/db_connect.php";

  //prepare the statement passing the db $link and the SQL
  /* ADDED "'ORDER BY' timestamp DESC" at the end of the query for reverse, chronological order */
  $query = "SELECT 
  tarantino_movies.movie,
  tarantino_movies.year,
  GROUP_CONCAT(
      JSON_OBJECT(
          'name', tarantino_actors.actorsName,
          'birthYear', tarantino_actors.birthYear,
          'biography', tarantino_actors.biography
      )ORDER BY tarantino_actors.actorsName ASC SEPARATOR ',') AS actors
      FROM tarantino_movies 
      JOIN tarantino_linker ON tarantino_linker.movieID = tarantino_movies.movieID
      JOIN tarantino_actors ON tarantino_linker.actorID = tarantino_actors.actorID
      GROUP BY tarantino_movies.movie, tarantino_movies.year";

  /* $query =  "SELECT tarantino_movies.movie, tarantino_movies.year,
            tarantino_actors.actorsName, tarantino_actors.birthYear, tarantino_actors.biography
            FROM tarantino_movies, tarantino_actors, tarantino_linker
            WHERE tarantino_linker.actorID = tarantino_actors.actorID
            AND tarantino_linker.movieID = tarantino_movies.movieID";
  */

  $stmt = mysqli_prepare($link, $query);

  //execute the statement / query from abobe
  mysqli_stmt_execute($stmt);

  //get results
  $result = mysqli_stmt_get_result($stmt);

  //loop through
  while($row = mysqli_fetch_assoc($result)){
    $row['actors'] = json_decode('[' . $row['actors'] . ']');
    $results[]= $row;
  }

  //encode & display json
  echo json_encode($results);

  //close the link to the db
  mysqli_close($link);

?>