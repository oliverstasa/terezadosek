<?php
session_start();



/*
uploading video
only if session admin == 1 (logged in)
*/
if ($_SESSION['admin'] == 1) {


  include './fce.php';
  $conn = sqlConn();




  // name generator
  function randName() {
      $rand = false;
      $ch = 'TEREZADOSEKterezadosek';
      $chL = strlen($ch)-1;
      for ($i = 0; $i < 8; $i++) {
          $in = rand(0, $chL);
          $rand .= $ch[$in];
      }
      return $rand;
  }




  // video in $_FILES and id in $_GET
  if (isset($_FILES['video']) && isset($_GET['id'])) {


  // get file extention
  $ext = pathinfo($_FILES['video']["name"], PATHINFO_EXTENSION);

    // file .mp4?
    if (strtolower($ext) == 'mp4') {

      // pick new name
      $newName = false;
      while (file_exists('../data/video/'.$newName.'.mp4') || !$newName) {
        $newName = randName();
      }

      // upload it
      if (move_uploaded_file($_FILES['video']['tmp_name'], '../data/video/'.$newName.'.mp4')) {

          // remove previous video file
          $prevSql = 'SELECT videoUrl FROM page WHERE id = '.$_GET['id'];  
          $prevQ = mysqli_query($conn, $prevSql);

          // if there is a result
          if (mysqli_num_rows($prevQ) > 0) {

            $prev = mysqli_fetch_array($prevQ);

            // if there is a previous file => remove the file
            if ($prev['videoUrl']) {
              $url = '../data/video/'.$prev['videoUrl'];
              chmod($url, 0750);
              unlink($url);
            }

          // previous video file is removed
          }

          // add new video file
          $dotaz = 'UPDATE page SET videoUrl = "'.$newName.'.mp4" WHERE id = '.$_GET['id'];

             if (mysqli_query($conn, $dotaz)) {
               echo $newName.'.mp4';
             } else {
               echo 'error: '.implode("<br>", error_get_last());
             }

      // unable to upload it
      } else {
        echo 'error: '.implode("<br>", error_get_last());
      }

    // is not .mp4
    } else {
      echo "invalid";
    }


  // file not in $_FILES
  } else {
    echo 'error:'.implode("<br>", error_get_last());
  }



// session not admin
}
