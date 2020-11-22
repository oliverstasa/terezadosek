<?php
/*
page generator
*/



if (isset($_POST['url'])) {



// vars
$json = array();
$error = array();
$url = $_POST['url'];



/*
json setup
*/
// headders
array_push($json, '"headder": "page"');



// mysql

  if ($url == '/text') {
    $pageType = 'textContent';
    $hrefTo = '/video';
  } else {
    $pageType = 'scriptToScreen';
    $hrefTo = '/text';
  }

// /mysql
array_push($json, '"pageType": "'.$pageType.'"');
array_push($json, '"hrefTo": "'.$hrefTo.'"');



// title
array_push($json, '"pageTitle": "'.$pageTitle.'"');




// switch json addition based on page type
// script to screen requires additional query
switch ($pageType) {



  // text-only page
  case 'textContent':

    $contentHtml = '
    <div class="pg fadeIn">
      <div class="text">
        <span style="color: #ddd;">Tereza Dosek:</span><br>Chvíli mrká, rozhlíží se a pak trochu neohrabaně vstane, vezme svou hůl – tím rozbije sluneční hodiny.
      </div>
    </div>
    ';

  break;



  // special video screen
  case 'scriptToScreen':

      $contentHtml = '
      <div class="pg fadeIn">
        <div class="halfScreen top" id="script">
          <p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p><p>TEXT</p>
        </div>
        <div class="loadingStep loading"></div>
        <div class="progress"></div>
        <div class="controls"><div id="play">▶</div><div id="stop">◼</div><div id="end">↺</div><div id="mute">🕨</div><div id="sound">🕪</div></div>
        <div class="halfScreen bot">
          <span class="loadingText">stahuje se video</span>
          <video muted preload="auto" class="fadeOut">
          </video>
        </div>
      </div>
      ';

      // add videoUrl atribute
      $videoUrl = 'bong.mp4';
      array_push($json, '"videoUrl": "/data/'.$videoUrl.'"');

  break;
}


// html
// remove the line-breaks and quotes
$contentHtml = str_replace(array("\n", "\r", '"'), array('', '', '\"'), $contentHtml);
// add html to json
array_push($json, '"contentHtml": "'.$contentHtml.'"');



// else = if url is not posted
} else {

  array_push($error, 'post method failed to read url');

}


// if any errors
array_push($json, '"error": "'.join(', ', $error).'"');



// turn array to json object
echo '{'.join(',', $json).'}';



?>
