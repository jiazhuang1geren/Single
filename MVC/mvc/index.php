<?php
include 'bootstrap/Psr4AutoLoad.php';
include 'bootstrap/Start.php';
include 'bootstrap/alias.php';
session_start();
$config = include 'config/config.php';
Start::router();

