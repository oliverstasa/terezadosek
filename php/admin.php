<?php



/*
this file is requied in other file
save result to var $adminHtml => gets handled elsewhere
i know its shitty but low budget, just gets it done...
*/
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


    /*
    add or edit page
    form
    */
    case 'add': case 'edit':

      $edit = ($url[2] == 'edit' && isset($url[3]))?true:false;
      $headding = $edit?'Edit':'Add';

      if ($edit) {

        $sqlAdmin = 'SELECT id, active, type, link, title, title_en, content, content_en, videoUrl FROM page WHERE id = '.$url[3];

        $ress = mysqli_query($conn, $sqlAdmin);
        $e = mysqli_fetch_array($ress);

        // they're switched because i need them to show different % at row 85 & 90
        $data = array($e['content_en'], $e['content']);

      }

      $adminHtml .=  '<script>';
      for ($i = 1; $i <= 4; $i++) {

        $adminHtml .= 'var editor_'.$i.' = new EditorJS({
                       holder: \'editor_'.$i.'\'';
                       if ($i >= 3) {$adminHtml .= ',
                       tools: {
                         header: {class: Header, config: {levels: [1], defaultLevel: 1} },
                         table: {class: Table, inlineToolbar: true, config: {rows: 2, cols: 1} }
                       }';
                       }

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

      $adminHtml .= '<a href="/admin">↶</a> '.$headding.' page'.(($edit)?' <i>'.lang($e['title'], $e['title_en']).'</i>':'');
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
      $adminHtml .= '<div class="popisek">LINK <span class="title" style="cursor: help;" dataTitle="https://terezadosek.com/[LINK]<br><br><center>example: experiment<br>=><br>https://terezadosek.com/experiment</center>">[?] What\'s link</span></div>';
      $adminHtml .= '<input type="text" name="link" placeholder="terezadosek.com/[LINK]" maxlength="30" value="'.(($edit)?$e['link']:'').'">';
      $adminHtml .= '<br>';
      $adminHtml .= '<div class="popisek">TITLE CZ</div>';
      $adminHtml .= '<input type="text" name="title" placeholder="Malý experiment [cz]" maxlength="30" value="'.(($edit)?$e['title']:'').'">';
      $adminHtml .= '<br>';
      $adminHtml .= '<div class="popisek">TITLE EN</div>';
      $adminHtml .= '<input type="text" name="title_en" placeholder="Lil experiment [en]" maxlength="30" value="'.(($edit)?$e['title_en']:'').'">';
      $adminHtml .= '<br>';

      $adminHtml .= '<div id="textContent">';
      $adminHtml .= '<div class="popisek">';
      $adminHtml .= 'TEXT CZ <span class="title" style="cursor: help;" dataTitle="no headings, no tables<br>just text in one paragraph<br>tips:<br>*new paragraph: [enter] (don\'t do this here)<br>*new line inside paragraph: [shift]+[enter] (do this!)">[?] How to text page</span>';
      $adminHtml .= '</div>';
      $adminHtml .= '<div id="editor_1" class="editorjs" name="textContent"></div>';
      $adminHtml .= '<br>';
      $adminHtml .= '<div class="popisek">TEXT EN</div>';
      $adminHtml .= '<div id="editor_2" class="editorjs" name="textContent_en"></div>';
      $adminHtml .= '</div>';

      $adminHtml .= '<div id="scriptToScreen">';
      $adminHtml .= '<div class="popisek">';
      $adminHtml .= 'SCRIPT CZ <span class="title" style="cursor: help;" dataTitle="HEADING (movie title)<br><i>(new paragraph)</i><br>TABLE (Director, Camera, Screenplay)<br><i>(new paragraph)</i><br>scene 1 (<b>bold</b>)<br><i>(new paragraph)</i><br>text<br><i>(new paragraph)<br>scene 2 (<b>bold</b>)<br><i>(new paragraph)</i><br>text<br><i>(new paragraph)<br>scene 3 (<b>bold</b>)<br>...<br>tips:<br>*new paragraph: [enter]<br>*new line inside paragraph: [shift]+[enter]">[?] How to write script</span>';
      $adminHtml .= '</div>';
      $adminHtml .= '<div id="editor_3" class="editorjs" name="scriptContent"></div>';
      $adminHtml .= '<br>';
      $adminHtml .= '<div class="popisek">SCRIPT EN <span class="title" style="cursor: help;" dataTitle="<li>click into CZ textarea<li>push [ctrl/cmd]+[a] <b>twice</b><li>push [ctrl/cmd]+[c]<li>click into EN textarea<li>push [ctrl/cmd]+[v]">[?] Duplicate content from CZ</span></div>';
      $adminHtml .= '<div id="editor_4" class="editorjs" name="scriptContent_en"></div>';
      $adminHtml .= '</div>';

      $adminHtml .= '<br>';
      $adminHtml .= '<input type="submit" value="'.$headding.'">';
      $adminHtml .= '<br>';
      $adminHtml .= '</form>';
      $adminHtml .= '</div>';

    break;


    /*
    logout
    */
    case 'logout':

      // unset admin session
      $_SESSION['admin'] = 0;
      $_SESSION['attempts'] = 0;

      $adminHtml .= 'Logged out ✓<br>click the arrow';

    break;



    /*
    default: show all pages
    */
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
        $adminHtml .= '<span class="title" style="cursor: help;" dataTitle="codec: h264<br>format: .mp4<br>optimal size: 10mb<br>max size: 100mb<br>optimal resolution: 720p<br>max resolution: 1080p<br>optimal bitrate: 3mbps<br>max bitrate: 10mbps<br>keyframe-distance: 5 (!)">[?] Video format</span>';
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




/*
if not logged in => login page
*/
} else {

  $adminHtml = 'Admin<br><form method="post" id="loginForm"><input type="password" name="login" placeholder="sezame, otevři se" style="margin-bottom: 2vh;"><br><input type="submit" value="log in"></form>';

}
