<?php
require_once('main/main_scripts.php');
require_once('../classes/user.php');
require_once('../classes/product.php');
require_once('../classes/market.php');
require_once('../classes/quotation.php');
require_once('Classes/admin.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin Panel</title>
</head>
<body>
<?php
if($login->isAdminLoggedIn()){
    include_once('views/loggedIn.php');
}else{
    include_once('views/not_logged_in.php');
}
?>


</body>
</html>