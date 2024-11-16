<?php
if (!defined("pvault_panel")){ die("Not Found");}
?>
<html lang="en" data-bs-theme="<?=$settings['bs_theme'] ?: 'light';?>">
<head>

  <meta charset="utf-8">
  <meta name="description" content="Something went wrong...">
  <meta name="author" content="perfumersvault">
  <title>Error</title>
  <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
  <script src="/js/jquery/jquery.min.js"></script>
 
  <link href="/css/sb-admin-2.css" rel="stylesheet">
  <link href="/css/vault.css" rel="stylesheet">

  <link href="/css/fontawesome-free/css/all.min.css" rel="stylesheet">

</head>
<div id="wrapper">
    <div class="container-fluid">
        <div class="text-center">
            <div class="error mx-auto" data-text="Error">Error...</div>
            <div class="alert alert-danger"><i class="fa-solid fa-bug mx-2"></i><?php echo $error_msg;?></div>
            <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
            <a href="/"><i class="fa-solid fa-arrow-left-long mx-2"></i>Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>