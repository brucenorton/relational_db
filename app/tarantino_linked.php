<?php
  //connect to db - $link
  require_once "_includes/db_connect.php";

  //prepare the statement passing the db $link and the SQL
  /* ADDED "'ORDER BY' timestamp DESC" at the end of the query for reverse, chronological order */
  $stmt = mysqli_prepare($link, 
    "SELECT tarantino_movies.movie, tarantino_movies.year,
            tarantino_actors.actorsName, tarantino_actors.birthYear, tarantino_actors.biography
    FROM tarantino_movies, tarantino_actors, tarantino_linker
    WHERE tarantino_linker.actorID = tarantino_actors.actorID
    AND tarantino_linker.movieID = tarantino_movies.movieID"
  );

  //execute the statement / query from abobe
  mysqli_stmt_execute($stmt);

  //get results
  $result = mysqli_stmt_get_result($stmt);

  //loop through
  while($row = mysqli_fetch_assoc($result)){
    $movie_title = $row['movie'];
    
    if(!isset($results[$movie_title])){
      $results[$movie_title] = [
        'movie' => $row['movie'],
        'year' => $row['year'],
        'actors' => [],
      ];
    }
    $results[$movie_title]['actors'][] = [
      'name' => $row['actorsName'],
      'birthYear' => $row['birthYear'],
      'biography' => $row['biography']
    ];
    /* this gives a row for each movie */
    //$results[]=$row;l
  }

  //encode & display json
  echo json_encode($results);

  //close the link to the db
  mysqli_close($link);

?>