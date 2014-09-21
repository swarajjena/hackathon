
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

<div id="login_box">
<h2><span style="color:#f00">Login</span> / <span id="open_register"  style="cursor:pointer">Register</span></h2> <br />

<form method="post" action="#login" name="loginform">
    <label for="login_input_username">Username/Email</label><br/>
    <input id="login_input_username" class="login_input" type="text" name="user_name" required placeholder=""  size="30"/><br/><br/>
    <label for="login_input_password">Password</label><br/>
    <input id="login_input_password" class="login_input" type="password" name="user_password" autocomplete="off" required  size="30"/><br/><br/>
    <input type="checkbox" id="login_input_rememberme" name="user_rememberme" value="1" /> Keep me logged in<br/><br/>
    <input type="submit"  name="login" value="Log in" width="100px" /><br/><br/>
</form>
<a href="password_reset.php">I forgot my password</a>
</div>

