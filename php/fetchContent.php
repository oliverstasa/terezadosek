<?php
session_start();
/*
page generator
*/


include './fce.php';



// vars
$json = array();
$error = array();

$conn = sqlConn();



// if url is posted = get the link
if (isset($_POST['url']) && $_POST['url'] != '/' && $_POST['url'] != '') {

  $sql = 'SELECT id AS mainId, type, '.lang('title', 'title_en').' AS title, '.lang('content', 'content_en').' AS content, videoUrl, (SELECT link FROM page WHERE id > mainId AND active = 1 LIMIT 1) AS next, (SELECT link FROM page WHERE active = 1 ORDER BY id LIMIT 1) AS first FROM page WHERE link = "'.substr($_POST['url'], 1).'" AND active = 1 LIMIT 1';

// if url is not posted, get first active page
} else {

  $sql = 'SELECT id AS mainId, type, '.lang('title', 'title_en').' AS title, '.lang('content', 'content_en').' AS content, videoUrl, (SELECT link FROM page WHERE id > mainId AND active = 1 LIMIT 1) AS next FROM page WHERE active = 1 ORDER BY id LIMIT 1';

}



/*
db
*/
// run sql
$ress = mysqli_query($conn, $sql);
// if there is any result
if (mysqli_num_rows($ress) > 0) {

  // fetch data to object
  $pg = mysqli_fetch_array($ress);

  // setup main vars
  $pageType = $pg['type'];
  $pageTitle = $pg['title'];
  $html = $pg['content'];

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
  $pageType = 'textContent';
  $pageTitle = 'problem';
  $hrefTo = '';
  $html = '<b>we\'ve got an error, this url is not active!</b><br>run or continue to next page';

}




// switch html construction based on page type
switch ($pageType) {



  // text-only page
  case 'textContent':

    $contentHtml = '
    <div class="pg fadeIn">
      <div class="text">
        <span style="color: #ddd;">Tereza Dosek:</span><br>
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
          <div id="mute" class="title" dataTitle="'.lang('ZTLUMIT <i>[M]</i>', 'MUTE <i>[M]</i>').'">🕨</div>
          <div id="sound" class="title" dataTitle="'.lang('PŘEHRÁT ZVUK <i>[M]</i>', 'SOUND ON <i>[M]</i>').'">🕪</div>
        </div>
        <div class="halfScreen bot">
          <span class="loadingText">'.lang('stahuje se video', 'fetching content').'<span class="wait"><span>.</span><span>.</span><span>.</span></span></span>
          <video muted preload="auto" class="fadeOut">
          </video>
        </div>
      </div>
      ';

  break;
}




/*
build the JSON object
*/
// page headder
array_push($json, '"headder": "page"');
// page type
array_push($json, '"pageType": "'.$pageType.'"');
// link to next page
array_push($json, '"hrefTo": "/'.$hrefTo.'"');
// page title (for title)
array_push($json, '"pageTitle": "'.$pageTitle.'"');

// video URL
if (isset($videoUrl)) {
  array_push($json, '"videoUrl": "/data/'.$videoUrl.'"');
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
