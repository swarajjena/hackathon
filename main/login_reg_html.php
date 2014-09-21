
<div>
	<div id='login' style=' background:#fff;'>
       <div id="login_box" style="vertical-align:middle">
       <?php
	   if($login->isUserLoggedIn() == false){
	       include_once('views/not_logged_in.php');
	   }
	   ?>
       </div>
       <div id="registration_box" >
       <?php
	   if($login->isUserLoggedIn() == false){
           // so this single line handles the entire registration process.
           // showing the register view (with the registration form, and messages/errors)
           include("views/register.php");
	       
	   }
	   ?>
       </div>
    </div>
</div>
