

<div>
    Hey, <?php echo $_SESSION['user_name']; ?>. You are logged in.<br />
    Try to close this browser tab and open it again. Still logged in! ;)<br />
    And here's your profile picture (from gravatar):<br />
    <?php //echo $login->user_gravatar_image_url; ?>
    <?php echo $login->user_gravatar_image_tag; ?>
</div>

<div>
    <a href="?logout">Logout</a>    
    <a href="edit.php">Edit user data</a>
</div>
