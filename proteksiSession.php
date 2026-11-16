<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
function wajib_login(){
    if(!isset($_SESSION['status_login']) || $_SESSION["status_login"] !== true){
        header("Location: login.php");
        exit();
    }
}
function wajib_admin(){
    wajib_login();
    if($_SESSION["role"] !== "admin"){
        header("Location: index.php"); 
        exit();
    }
}
