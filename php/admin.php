<?php


// $adminHtml on the end... i know its shitty but low budget, just gets it done...
if ($_SESSION['admin'] == 1) {

  // fce.php is already set
  // $conn is already set



  // parse url
  $url = explode("/", $_POST['url']);
  $adminHtml = '';



  // switch url before select
  if ($url[2] == 'switch' && isset($url[3])) {

    $ids = explode("-", $url[3]);
    mysqli_query($conn, 'UPDATE page SET id = 0 WHERE id = '.$ids[0]);
    mysqli_query($conn, 'UPDATE page SET id = '.$ids[0].' WHERE id = '.$ids[1]);
    mysqli_query($conn, 'UPDATE page SET id = '.$ids[1].' WHERE id = 0');

  // if eracing
  } else if ($url[2] == "delete" && isset($url[3])) {

    if ($url[3]) {
      $sqlAdmin = 'DELETE FROM page WHERE id = '.$url[3];
      mysqli_query($conn, $sqlAdmin);
    }

  }



  /*
  main switch
  */
  switch ($url[2]) {



    case 'add': case 'edit':

      $edit = ($url[2] == 'edit' && isset($url[3]))?true:false;
      $headding = $edit?'Edit':'Add';

      if ($edit) {

        $sqlAdmin = 'SELECT id, active, type, link, title, title_en, content, content_en, videoUrl FROM page WHERE id = '.$url[3];

        $ress = mysqli_query($conn, $sqlAdmin);
        $e = mysqli_fetch_array($ress);

        $data = array($e['content_en'], $e['content']);

      }

      $adminHtml .=  '<script>';
      for ($i = 1; $i <= 4; $i++) {

        $adminHtml .= 'var editor_'.$i.' = new EditorJS({
                       holder: \'editor_'.$i.'\',
                       tools: {
                         header: {class: Header, config: {levels: [1], defaultLevel: 1} },
                         table: {class: Table, inlineToolbar: true, config: {rows: 1, cols: 2} }
                       }';

        if ($edit) {
          switch ($e['type']) {
            case 'textContent':
              if ($i <= 2 && $data[$i%2]) {
                $adminHtml .= ', data: '.$data[$i%2];
              }
            break;
            case 'scriptToScreen':
              if ($i >= 3 && $data[$i%2]) {
                $adminHtml .= ', data: '.$data[$i%2];
              }
            break;
          }
        }

        $adminHtml .= '});';

      }

      $adminHtml .=  '</script>';

      $adminHtml .= '<a href="/admin">↶</a> '.$headding.' page';
      $adminHtml .= '<div class="row">';
      $adminHtml .= '<form method="post" id="adminPageForm"'.($edit?' pId="'.$e['id'].'"':'').'>';
      $adminHtml .= '<div class="popisek">PUBLIC</div>';
      $adminHtml .= '<input type="checkbox" name="active"'.(($edit && $e['active'] == 1)?' checked':'').'>';
      $adminHtml .= '<div class="popisek">PAGE TYPE</div>';
      $adminHtml .= '<select name="type">';
      $adminHtml .= '<option value="textContent"'.(($edit && $e['type'] == 'textContent')?' selected':'').'>TEXT</option>';
      $adminHtml .= '<option value="scriptToScreen"'.(($edit && $e['type'] == 'scriptToScreen')?' selected':'').'>VIDEO</option>';
      $adminHtml .= '</select>';
      $adminHtml .= '<br>';
      $adminHtml .= '<div class="popisek">LINK</div>';
      $adminHtml .= '<input type="text" name="link" placeholder="terezadosek.com/[LINK]" maxlength="30" value="'.(($edit)?$e['link']:'').'">';
      $adminHtml .= '<br>';
      $adminHtml .= '<div class="popisek">TITLE CZ</div>';
      $adminHtml .= '<input type="text" name="title" placeholder="Malý experiment [cz]" maxlength="30" value="'.(($edit)?$e['title']:'').'">';
      $adminHtml .= '<br>';
      $adminHtml .= '<div class="popisek">TITLE EN</div>';
      $adminHtml .= '<input type="text" name="title_en" placeholder="Lil experiment [en]" maxlength="30" value="'.(($edit)?$e['title_en']:'').'">';
      $adminHtml .= '<br>';

      $adminHtml .= '<div id="textContent">';
      $adminHtml .= '<div class="popisek">TEXT CZ</div>';
      $adminHtml .= '<div id="editor_1" class="editorjs" name="textContent"></div>';
      $adminHtml .= '<br>';
      $adminHtml .= '<div class="popisek">TEXT EN</div>';
      $adminHtml .= '<div id="editor_2" class="editorjs" name="textContent_en"></div>';
      $adminHtml .= '</div>';

      $adminHtml .= '<div id="scriptToScreen">';
      $adminHtml .= '<div class="popisek">SCRIPT CZ</div>';
      $adminHtml .= '<div id="editor_3" class="editorjs" name="scriptContent"></div>';
      $adminHtml .= '<br>';
      $adminHtml .= '<div class="popisek">SCRIPT EN</div>';
      $adminHtml .= '<div id="editor_4" class="editorjs" name="scriptContent_en"></div>';
      $adminHtml .= '</div>';

      $adminHtml .= '<br>';
      $adminHtml .= '<input type="submit" value="'.$headding.'">';
      $adminHtml .= '</form>';
      $adminHtml .= '</div>';

    break;



    case 'logout':

      $_SESSION['admin'] = 0;

      $adminHtml .= 'Logout ✓';

    break;



    // show all pages
    default:


      // sql
      $sqlAdmin = 'SELECT id, active, type, link, title, title_en, content, content_en, videoUrl FROM page ORDER BY id';

      $ress = mysqli_query($conn, $sqlAdmin);
      // if there is any result
      if (mysqli_num_rows($ress) > 0) {


        $num = 1;
        $adminHtml .= 'Admin';
        $adminHtml .= '<div class="row">';
        $adminHtml .= '<a href="/admin/add">[Add page]</a>&emsp;';
        $adminHtml .= '<a href="/admin/logout">[🔒]</a>&emsp;';
        $adminHtml .= '<span class="title" style="cursor: help;" dataTitle="codec: h264<br>format: .mp4<br>optimal size: 30mb<br>max size: 100mb<br>optimal bitrate: 8mbps<br>max bitrate: 12mbps<br>keyframe-distance: 5 (!)">[?] Video format</span>';
        $adminHtml .= '<br></div>';


        // fetch data to object
        while ($pg = mysqli_fetch_array($ress)) {

          // row
          $adminHtml .= '<div class="row">';
          $adminHtml .= '<span class="rNum">'.sprintf("%02d", $num).'.</span>';
          $adminHtml .= '<span class="rPos">';

          // move
          $switcher = mysqli_query($conn, 'SELECT id, (SELECT id FROM page WHERE id > '.$pg['id'].' ORDER BY id LIMIT 1) AS id_prev, (SELECT id FROM page WHERE id < '.$pg['id'].' ORDER BY id DESC LIMIT 1) AS id_next FROM page WHERE id = '.$pg['id'].' LIMIT 1');
          $id = mysqli_fetch_assoc($switcher);

          if ($id['id_next']) {
            $adminHtml .= '<a href="/admin/switch/'.$pg['id'].'-'.$id['id_next'].'">[↑]</a>';
          }
          if ($id['id_prev']) {
            $adminHtml .= '<a href="/admin/switch/'.$id['id_prev'].'-'.$pg['id'].'">[↓]</a>';
          }

          $adminHtml .= '</span><span class="rType">';
          $adminHtml .= ($pg['active'] == 1)?'[👁]':'[×]';
          $adminHtml .= ' ';
          $adminHtml .= ($pg['type'] == 'scriptToScreen')?'[🎥]':'[Text]';
          $adminHtml .= '</span>';
          $adminHtml .= '<a href="/'.$pg['link'].'">🗁</a>';
          $adminHtml .= ' → ';
          $adminHtml .= '<span class="theTitle">'.lang($pg['title'], $pg['title_en']).'</span>';

          if ($pg['type'] == 'scriptToScreen') {
            $adminHtml .= ' ← ';
            $adminHtml .= '<form method="post" enctype="multipart/form-data" class="uploadVideoForm" pId="'.$pg['id'].'"><input type="file" accept="video/mp4" name="video"></form>';
            if ($pg['videoUrl'] == '') {
              $adminHtml .= '<a class="videoUp videoUrl">[Upload clip]</a>';
            } else {
              $adminHtml .= '<a target="_blank" href="/data/video/'.$pg['videoUrl'].'" class="videoUrl">'.$pg['videoUrl'].'</a> ↔ <a class="videoUp">[Change clip]</a>';
            }
          }

          $adminHtml .= ' ← ';
          $adminHtml .= '<a href="/admin/edit/'.$pg['id'].'">[Edit]</a>';
          $adminHtml .= ' ← ';
          $adminHtml .= '<a href="/admin/delete/'.$pg['id'].'" class="remove">[Remove]</a>';

          $adminHtml .= '</div>';

          $num++;

        }


      // if there is no ressult
      } else {

        $adminHtml = 'no pages';

      }


    break;

  }




// if not logged => login page
} else {

  $adminHtml = 'Admin<br><form method="post" id="loginForm"><input type="password" name="login" placeholder="sezame, otevři se" style="margin-bottom: 2vh;"><br><input type="submit" value="log in"></form>';

}
