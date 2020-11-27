<?php
session_start();

if ($_SESSION['admin'] == 1) {



include './fce.php';
$conn = sqlConn();



  function norm($json) {
    //$json = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    $json = str_replace('"', '\"', $json);
    // $json = str_replace('\\\"', '\\\\"', $json);
    return $json;
  }



  $pId = isset($_POST['pId'])?$_POST['pId']:false;
  $active = isset($_POST['active'])?$_POST['active']:false;
  $type = isset($_POST['type'])?$_POST['type']:false;
  $link = isset($_POST['link'])?$_POST['link']:false;
  $title = isset($_POST['title'])?$_POST['title']:false;
  $title_en = isset($_POST['title_en'])?$_POST['title_en']:false;
  $content = isset($_POST['content'])?norm($_POST['content']):false;
  $content_en = isset($_POST['content_en'])?norm($_POST['content_en']):false;

  // if editing => pId == editing
  if ($pId) {

    $dotaz = 'UPDATE page
              SET active = "'.$active.'",
                  type = "'.$type.'",
                  link = "'.$link.'",
                  title = "'.$title.'",
                  title_en = "'.$title_en.'",
                  content = "'.$content.'",
                  content_en = "'.$content_en.'"
              WHERE id = '.$pId;

  // not editing => adding
  } else {

    $dotaz = 'INSERT INTO page (active, type, link, title, title_en, content, content_en)
              VALUES ("'.$active.'", "'.$type.'", "'.$link.'", "'.$title.'", "'.$title_en.'", "'.$content.'", "'.$content_en.'")';

  }


  // send query
  if (mysqli_query($conn, $dotaz)) {
    echo 1;
  } else {
    echo 'error: '.mysqli_error($conn);
  }



// /admin session
}
