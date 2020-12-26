<?php


/*
set initial sessions
*/
include './php/ini.php';


?>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>

  <meta charset="utf-8">

  <title>Tereza Dosek</title>
  <meta name="keywords" content="screenplay, scénáristka, Tereza Dosek, FAMU">
  <meta name="description" content="gets text rekt">

  <meta name="robots" content="all">
  <meta name="author" content="Oliver Staša">
  <meta name="viewport" content="width=device-width">

  <link rel="icon" href="/data/fav.png">

  <link href="/css/main.css?v=0.9" rel="stylesheet">
  <?php if ($_SESSION['daytime'] == 'night') {echo '<link href="/css/night.css" rel="stylesheet">';} ?>
  <script src="/js/jq.js" type="text/javascript"></script>
  <script src="/js/fce.js?v=0.9" type="module"></script>

  <?php

  if (strpos($_SERVER['REQUEST_URI'], "admin")) {
    echo '
    <script src="https://famufest.cz/js/editor_json/dist/editor.js"></script>
    <script src="https://famufest.cz/js/editor_json/dist/table.js"></script>
    <script src="https://famufest.cz/js/editor_json/dist/header.js"></script>
    <script src="/js/fce.admin.js?v=0.alfa" type="module"></script>';
  }

  ?>

</head>
<body>

<a id="next"><div>→</div></a>

</body>
</html>
