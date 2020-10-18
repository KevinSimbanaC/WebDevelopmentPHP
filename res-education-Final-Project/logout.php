<?php
session_start();
unset($_SESSION['name']);
unset($_SESSION['user_id']);
unset($_SESSION['error']);
session_destroy();
header('Location: index.php');