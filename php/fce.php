<?php
/*
my function, your function, our function
gogogo lady go gaga
*/
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}



/*
MySQL
*/
function sqlConn() {

    $server = "localhost";
    $login = "c10terezadosek";
    $pass = "hS5iW!SzucFZ";
    $db = "c10terezadosek";

    $conn = new mysqli($server, $login, $pass, $db);

    if ($conn->connect_error) {
      return false;
    } else {
      return $conn;
    }

}



/*
language switcher
*/
function lang($cs, $en) {

  // if both langs are set
  if (!empty($cs) && !empty($en)) {

    switch($_SESSION['lang']) {
      default: case 'en':
        return $en;
      break;
      case 'cs':
        return $cs;
      break;
    }

  // if at least one is set
  } else {

    if (empty($cs)) {
      return $en;
    } else if (empty($en)) {
      return $cs;
    } else {
      lang('[NEVYPLNĚNO]', '[NOT SET]');
    }

  }

}
