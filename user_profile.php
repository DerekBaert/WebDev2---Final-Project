<?php
    session_start();
    require 'header.php';

    $isUser = false;

    if($_SESSION['user']['id'] === $_GET['user'])
    {
        $isUser = true;
    }
?>