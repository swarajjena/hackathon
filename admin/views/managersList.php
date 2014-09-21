<u>All Managers</u>
<br><br>
<?php

$managers=new Admin();
$managers=$managers->adminList($login->admin_role, $login->admin_id);
foreach($managers as $manager){
   echo '<a href="?managers='.$manager->admin_id.'" style="font-size:16px;text-decoration:none"> >> '.$manager->admin_fullname.'('.$manager->admin_role.')</a><br>';
}

?>


<br><br>
<a href="?managers">+ Add a manager</a>