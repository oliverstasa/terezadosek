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

    // nothing is set
    } else {
      lang('[NEVYPLNĚNO]', '[NOT SET]');
    }

  }

}



/*
json to html:
 - paragraph
 - header
 - table
*/
function json2html($data) {

  $data = json_decode($data, true);
  $ress = '';

    foreach ($data['blocks'] as $key => $value) {

      switch ($value['type']) {


        // paragraph
        case 'paragraph':
          $content = preg_replace('/([a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})/', '<a href="mailto:$1">$1</a>', $value['data']['text']);
          $ress .= '<p>'.$content.'</p>';
        break;


        // header
        case 'header':
          $ress .= '<h1>'.$value['data']['text'].'</h1>';
        break;


        // table
        case 'table':
          $ress .= '<table>';
          $tabulka = $value['data']['content'];
          for ($t = 0; $t < sizeof($tabulka); $t++) {
            $ress .= '<tr>';
            for ($d = 0; $d < sizeof($tabulka[$t]); $d++) {
              $ress .= '<td>'.$tabulka[$t][$d].'</td>';
            }
            $ress .= '</tr>';
          }
          $ress .= '</table>';
        break;



      }

    }

    return $ress;

}
