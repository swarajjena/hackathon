<?php
$user_name='';
$full_name = '';
$admin_email = '';
$admin_password = '';
$admin_role = '';
$admin_head = '';
$threshold_limit=0;


if (isset($_GET['managers']) && $_GET['managers'] != '') {

    $manager = new Admin(intval($_GET['managers']));
    if (!$manager->admin_found) {
        header('location:index.php?managers');
    }
    
    if(isset($_POST['reset_password'])){
        $manager->resetPassword($_POST['reset_password']);
        header('location:http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ;
    }
    $user_name=$manager->admin_name;
    $full_name = $manager->admin_fullname;
    $admin_head = $manager->admin_head;
    $admin_email = $manager->admin_email;
    $admin_role = $manager->admin_role;
    $threshold_limit=$manager->admin_threshold_limit;

    if (isset($_POST['admin_fullname'])) {
        if ($_POST['admin_fullname'] != $full_name) {
            $manager->setData('admin_fullname', $_POST['admin_fullname']);
        }
        if (isset($_POST['admin_head']) && $_POST['admin_head'] != $admin_head) {
            $manager->setData('admin_head', $_POST['admin_head'],'INT');
        }
        if (isset($_POST['threshold_limit']) && $_POST['threshold_limit'] != $threshold_limit) {
            $manager->setData('threshold_limit', $_POST['threshold_limit'],'INT');
        }
        if ($_POST['admin_email'] != $admin_email) {
            $manager->setData('admin_email', $_POST['admin_email']);
        }
        header('location:http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ;
    }
} elseif (isset($_POST['admin_fullname'])) {
    $manager = new Admin();
    $insert = $manager->registerNewUser($_POST['admin_fullname'], $_POST['admin_email'], $_POST['admin_password'], $_POST['admin_role']);
//    echo $insert;
    header('location:index.php?managers=' . $insert);
}
?>

<?php
if (isset($_POST['first_step'])) {
    
}
?>

<form action="<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&second'; ?>" method="post">

    <br>
    <br>
    <br>
    <table border="0">
        <tr>
            <td>User Name </td>
            <td><?php echo $user_name; ?></td>
        </tr>
        <tr>
            <td>Name </td>
            <td><input type="text" name="admin_fullname" value="<?php echo $full_name; ?>"></td>
        </tr>
        <tr>
            <td>Email </td>
            <td><input type="text" name="admin_email" value="<?php echo $admin_email; ?>"></td>
        </tr>
<?php if (isset($_GET['managers']) && $_GET['managers'] != '') {
    
} else { ?>
            <tr>
                <td>Password </td>
                <td><input type="password" name="admin_password"></td>
            </tr>
    <?php
}
?>
        <tr>
            <td>Designation </td>
            <td>
                <select name="admin_role">
                    <option value="SM" <?php if ($admin_role == 'SM') {
            echo 'selected="selected"';
        } ?>>Sales Manager</option>
                    <option value="RM" <?php if ($admin_role == 'RM') {
                        echo 'selected="selected"';
                    }?> >Regional Manager</option>
                    <option value="NH" <?php if ($admin_role == 'NH') {
            echo 'selected="selected"';
        } ?>>National Head</option>
                </select>
            </td>
        </tr>
                    <?php
                    if (isset($_GET['managers']) && $_GET['managers'] != '') {
                        if (isset($manager->admin_id) && $manager->admin_role == 'SM') {
                            ?>
                <tr>
                    <td>Work Under </td>
                    <td>
                        <select name="admin_head">
                <?php
                $rms = $manager->adminListRM();

                foreach ($rms as $rm) {
                    echo '<option value="' . $rm->admin_id . '">' . $rm->admin_fullname . '</option>';
                }
                ?>
                        </select>
                    </td>
                </tr>

        <?php
    }
}
?>
                    <?php
                    if (isset($_GET['managers']) && $_GET['managers'] != '') {
                        if (isset($manager->admin_id) && $manager->admin_role !== 'NH') {
                            ?>
                                <tr>
                                    <td>Threshold Limit</td>
                                    <td><input type="text" size="3" name="threshold_limit"  value="<?php echo $threshold_limit ?>">% </td>
                        </tr>

                <?php
                        }
                    }
                            ?>
                

                
                <tr>
            <td colspan="2"><input type="submit" value="Save" name="first_step"> </td>
        </tr>
    </table>

</form>


<form action="<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ''; ?>" method="post">
    Reset password for the user<input type="password" name="reset_password">
    <input type="submit" value="reset">  
</form>