
<?php

// show negative messages
if ($login->errors) {
    foreach ($login->errors as $error) {
        echo $error;    
    }
}

// show positive messages
if ($login->messages) {
    foreach ($login->messages as $message) {
        echo $message;
    }
}

?>             

<div id="login_box" align="center" style="margin: 180px">
<h2><span style="color:#f00">Login</span> </h2> <br />

<form method="post" action="#login" name="loginform">
    <label for="login_input_adminname">Adminname/Email</label><br/>
    <input id="login_input_adminname" class="login_input" type="text" name="admin_name" required placeholder=""  size="30"/><br/><br/>
    <label for="login_input_password">Password</label><br/>
    <input id="login_input_password" class="login_input" type="password" name="admin_password" autocomplete="off" required  size="30"/><br/><br/>
    <input type="submit"  name="login" value="Log in" width="100px" /><br/><br/>
</form>
</div>

