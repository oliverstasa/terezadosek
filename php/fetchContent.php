<?php
session_start();
/*
page generator
*/


include './fce.php';



// vars
$json = array();
$error = array();
$headder = 'page';

$conn = sqlConn();



if (isset($_POST['url']) && substr($_POST['url'], 0, 6) == '/admin') {

  $headder = 'admin';
  include './admin.php';

// if url is posted = get the link
} else if (isset($_POST['url']) && $_POST['url'] != '/' && $_POST['url'] != '') {

  $sql = 'SELECT id AS mainId, type, title, title_en, content, content_en, videoUrl, (SELECT link FROM page WHERE id > mainId AND active = 1 LIMIT 1) AS next, (SELECT link FROM page WHERE active = 1 ORDER BY id LIMIT 1) AS first FROM page WHERE link = "'.substr($_POST['url'], 1).'"'.($_SESSION['admin']?'':' AND active = 1').' LIMIT 1';

// if url is not posted, get first active page
} else {

  $sql = 'SELECT id AS mainId, type, title, title_en, content, content_en, videoUrl, (SELECT link FROM page WHERE id > mainId AND active = 1 LIMIT 1) AS next FROM page '.($_SESSION['admin']?'':'WHERE active = 1').' ORDER BY id LIMIT 1';

}



/*
db
*/
// if sql = not admin
if (isset($sql)) {

// run sql
$ress = mysqli_query($conn, $sql);
// if there is any result
if (mysqli_num_rows($ress) > 0) {

  // fetch data to object
  $pg = mysqli_fetch_array($ress);

  // setup main vars
  $pageType = $pg['type'];
  $pageTitle = lang($pg['title'], $pg['title_en']);
  $html = json2html(lang($pg['content'], $pg['content_en']));

  if ($pg['videoUrl']) {
    $videoUrl = $pg['videoUrl'];
  }

  if ($pg['next']) {
    $hrefTo = $pg['next'];
  } else {
    $hrefTo = $pg['first'];
  }

// no ressult for this link
} else {

  array_push($error, 'hell of a bad sql error');
  $headder = 'error';
  $pageType = 'textContent';
  $pageTitle = 'Error';
  $hrefTo = '';
  $html = 'Sorry, this url hides no content.<br>Continue to next page!';

}

// no sql = admin
} else {

  $pageType = 'textContent';
  $pageTitle = 'Admin';
  $hrefTo = '';
  $html = $adminHtml;

}




// switch html construction based on page type
switch ($pageType) {



  // text-only page
  case 'textContent':

    $color = ($_SESSION['daytime'] == 'night')?'#161616':'#e9e9e9';

    $contentHtml = '
    <div class="pg fadeIn">
      <div class="text">
        <span style="color: '.$color.';">Tereza Dosek</span><br>
        '.$html.'
      </div>
    </div>
    ';

  break;



  // special video screen
  case 'scriptToScreen':

      $contentHtml = '
      <div class="pg fadeIn">
        <div class="halfScreen top" id="script">
          '.$html.'
        </div>
        '.(!isset($_SESSION['scrollme'])?'<div id="scrollMe"></div>':'').'
        <div class="loadingStep loading"></div>
        <div class="progress"></div>
        <div class="controls">
          <div id="play" class="title" dataTitle="'.lang('PŘEHRÁT <i>[MEZERNÍK]</i>', 'PLAY <i>[SPACE BAR]</i>').'">▶</div>
          <div id="stop" class="title" dataTitle="'.lang('ZASTAVIT <i>[MEZERNÍK]</i>', 'STOP <i>[SPACE BAR]</i>').'">◼</div>
          <div id="end" class="title" dataTitle="'.lang('OPAKOVAT <i>[MEZERNÍK]</i>', 'REPEAT <i>[SPACE BAR]</i>').'">↺</div>
          <div id="mute" class="title" dataTitle="'.lang('ZTIŠIT <i>[M]</i>', 'MUTE <i>[M]</i>').'">'.lang('T', 'M').'</div>
          <div id="sound" class="title" dataTitle="'.lang('ZAPNOUT ZVUK <i>[M]</i>', 'SOUND ON <i>[M]</i>').'">'.lang('Z', 'S').'</div>
        </div>
        <div class="halfScreen bot">
          <span class="loadingText"><span id="bearWithMe">'.lang('stahuje se obsah', 'fetching content').'</span><span class="wait"><span>.</span><span>.</span><span>.</span></span></span>
          <span id="countdown"></span>
          <video muted preload="auto" class="fadeOut">
          </video>
        </div>
      </div>
      ';
      // 🕨 🕪

  break;
}




/*
build the JSON object
*/
// page headder
array_push($json, '"headder": "'.$headder.'"');
// page type
array_push($json, '"pageType": "'.$pageType.'"');
// link to next page
array_push($json, '"hrefTo": "/'.$hrefTo.'"');
// page title (for title)
array_push($json, '"pageTitle": "'.$pageTitle.'"');

// video URL
// video SIZE in Kb
if (isset($videoUrl)) {
  array_push($json, '"videoUrl": "/data/video/'.$videoUrl.'"');
  array_push($json, '"videoSize": "'.(filesize('../data/video/'.$videoUrl)/1024).'"');
}

// html
// remove the line-breaks and quotes
$contentHtml = str_replace(array("\n", "\r", '"'), array('', '', '\"'), $contentHtml);
// html content
array_push($json, '"contentHtml": "'.$contentHtml.'"');

// if any errors
array_push($json, '"error": "'.join(', ', $error).'"');



/*
array => json
*/
// turn array to json object
echo '{'.join(',', $json).'}';



?>
