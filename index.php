<?php
$uri = 'http://'; 
$uri .= $_SERVER['HTTP_HOST'];
header('Location: '.$uri.'/public/index.php');
exit;
?>