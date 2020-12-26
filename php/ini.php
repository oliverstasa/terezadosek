<?php
/*
initial sessions setup
*/
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}



/*
set global language
*/
if (!isset($_SESSION['lang'])) {

  if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
  } else {
    $lang = 'en';
  }

    switch ($lang) {
      case "cs":
        $_SESSION['lang'] = 'cs';
      break;
      default: case "en":
        $_SESSION['lang'] = 'en';
      break;
    }

}



/*
set default user session
*/
if (!isset($_SESSION['admin'])) {
  $_SESSION['admin'] = 0;
  $_SESSION['attempts'] = 0;
}



/*
find out datetime
*/
$now = time();
$sun = date_sun_info($now, '50.07', '14.41'); // LONG, LAT => for praha

if ($now > $sun['sunrise'] && $now < $sun['sunset']) {
  $_SESSION['daytime'] = 'day';
} else {
  $_SESSION['daytime'] = 'night';
}