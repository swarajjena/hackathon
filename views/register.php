<!-- this is the Simple sexy PHP Login Script. You can find it on http://www.php-login.net ! It's free and open source. -->

<!-- errors & messages --->
<?php

// show negative messages
if ($registration->errors) {
    foreach ($registration->errors as $error) {
        echo $error;    
    }
}

// show positive messages
if ($registration->messages) {
    foreach ($registration->messages as $message) {
        echo $message;
    }
}

?>   

<?php if (!$registration->registration_successful && !$registration->verification_successful) { ?>

<!-- register form -->
<div id="registration_box">
<h2><span id="open_login" style="cursor:pointer">Login</span> / <span  style="color:#f00">Register</span> </h2> 
<br/>

<form method="post" action="index.php#register" name="registerform">   
<table width="500" border="0">
  <tr>
    <td><label for="login_input_username">Full name </label></td>
    <td><input id="login_input_username" class="login_input" type="text" pattern="[a-zA-Z ]{2,100}" name="full_name" required size="30"  placeholder="Your full name"/></td>
  </tr>
  <tr>
    <td><label for="login_input_email">Email </label></td>
    <td><input id="login_input_email" class="login_input" type="email" name="user_email" required  size="30" placeholder="Your email address"/></td>
  </tr>
  <tr>
    <td><label for="login_input_password_new">Password </label></td>
    <td>    <input id="login_input_password_new" class="login_input" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" size="30"  placeholder="* min. 6 characters!"/></td>
  </tr>
  <tr>
    <td><label for="login_input_password_repeat">Repeat password</label></td>
    <td><input id="login_input_password_repeat" class="login_input" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" size="30" /></td>
  </tr>
  <tr>
    <td><img src="tools/showCaptcha.php" /></td>
    <td>    
    
    
   
    <input type="text" name="captcha" required  placeholder="" size="6"/> <br/>(Please enter the characters in the left)</td>
  </tr>
</table>

    
    
<br/><br/>
    
    <input type="submit"  name="register" value="Register Me" /><br/><br/>
    
</form>
</div>
<?php } ?>

