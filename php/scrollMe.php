<?php
session_start();
/*
remove scrollme from session
*/

if ($_POST['scrolled'] == 'true') {
  echo $_SESSION['scrollme'] = true;
}
