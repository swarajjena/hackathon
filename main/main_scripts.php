<?php
session_start();                                        
ob_start();
if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("libraries/password_compatibility_library.php");
}
        

date_default_timezone_set('Asia/Calcutta');
require_once("config/config.php");
require_once("function.php");

// load the login class
require_once("classes/Login.php");
// include the PHPMailer library
require_once("libraries/PHPMailer.php");

//load the registration class
require_once("classes/Registration.php");

$login = new Login();

$registration = new Registration();


if(isset($_GET['logout'])){
	header('location:index.php');
}
if($login->post_success==true && $login->isUserLoggedIn() == false){
	header('location:'.$_SERVER['REQUEST_URI']."#login");
}if($login->post_success==true && $login->isUserLoggedIn() !== false){
	header('location:'.$_SERVER['REQUEST_URI'].'#success');
}

?>