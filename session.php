<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $loggedIn = true;
} else {
    $loggedIn = false;
}
?>
