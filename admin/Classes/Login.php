<?php

/**
 * class Login
 * handles the admin login/logout/session
 * 
 * @author Panique <panique@web.de>
 */
class Login {

    public $total_visits;

    /** @var object $db_connection The database connection */
    private $db_connection = null;

    /** @var int $hash_cost_factor The (optional) cost factor for the hash calculation */
    private $hash_cost_factor = null;

    /** @var int $admin_id The admin's id */
    public $admin_id = null;

    /** @var string $admin_name The admin's name */
    public $admin_name = "";

    /** @var string $admin_name The admin's name */
    public $admin_fullname = "";

    /** @var string $admin_rank The admin's rank */
    public $admin_role = "";

    /** @var string $admin_email The admin's mail */
    private $admin_email = "";

    /** @var string $admin_password_hash The admin's hashed and salted password */
    private $admin_password_hash = "";

    /** @var boolean $admin_is_logged_in The admin's login status */
    private $admin_is_logged_in = false;

    /** @var string $admin_password_reset_hash The admin's password reset hash */
    private $admin_password_reset_hash = "";

    /** @var string $admin_image_url The admin's profile pic url (or a default one) */
    public $admin_image_url = "";
    public $post_success = false;

    /** @var boolean $password_reset_link_is_valid Marker for view handling */
    private $password_reset_link_is_valid = false;

    /** @var boolean $password_reset_was_successful Marker for view handling */
    private $password_reset_was_successful = false;

    /** @var array $errors Collection of error messages */
    public $errors = array();

    /** @var array $messages Collection of success / neutral messages */
    public $messages = array();
                        public $admin_active = "";
                        public $admin_threshold_limit = "";

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct() {
        // create/read session
        // check the possible login actions:
        // 1. logout (happen when admin clicks logout button)
        // 2. login via session data (happens each time admin opens a page on your php project AFTER he has successfully logged in via the login form)
        // 3. login via cookie
        // 4. login via post data, which means simply logging in via the login form. after the admin has submit his login/password successfully, his
        //    logged-in-status is written into his session data on the server. this is the typical behaviour of common login scripts.
        // if admin tried to log out

        if (isset($_GET["logout"])) {

            $this->doLogout();

            // if admin has an active session on the server
        } elseif (!empty($_SESSION['admin_name']) && ($_SESSION['admin_logged_in'] == 1)) {

            $this->loginWithSessionData();

            // checking for form submit from editing screen
            if (isset($_POST["admin_edit_submit_name"])) {

                $this->editAdminName();
            } elseif (isset($_POST["admin_edit_submit_email"])) {

                $this->editAdminEmail();
            } elseif (isset($_POST["admin_edit_submit_password"])) {

                $this->editAdminPassword();
            }


            // login with cookie
        } elseif (isset($_COOKIE['rememberme'])) {

            $this->loginWithCookieData();

            // if admin just submitted a login form
        } elseif (isset($_POST["login"])) {

            $this->loginWithPostData();
            $this->post_success = true;
        }

        // checking if admin requested a password reset mail
        if (isset($_POST["request_password_reset"])) {

            $this->setPasswordResetDatabaseTokenAndSendMail(); // maybe a little bit cheesy
        } elseif (isset($_GET["admin_name"]) && isset($_GET["verification_code"])) {

            $this->checkIfEmailVerificationCodeIsValid();
        } elseif (isset($_POST["submit_new_password"])) {

            $this->editNewPassword();
        }
    }

    /**
     * Checks if database connection is opened and open it if not
     */
    private function databaseConnection() {
        // connection already opened
        if ($this->db_connection != null) {
            return true;
        } else {
            // create a database connection, using the constants from config/config.php
            try {
                $this->db_connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
                return true;

                // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = "Database connection problem.";
                return false;
            }
        }
    }

    /**
     * Search into database for the admin data of admin_name specified as parameter
     * @return admin data as an object if existing admin
     * @return false if admin_name is not found in the database
     */
    private function getAdminData($admin_name) {
        // if database connection opened
        if ($this->databaseConnection()) {

            // database query, getting all the info of the selected admin
            $query_admin = $this->db_connection->prepare('SELECT * FROM admins WHERE admin_name = :admin_name OR admin_email=:admin_name');
            $query_admin->bindValue(':admin_name', $admin_name, PDO::PARAM_STR);
            $query_admin->execute();
            // get result row (as an object)
            return $query_admin->fetchObject();
        } else {

            return false;
        }
    }

    private function loginWithSessionData() {
        $this->admin_name = $_SESSION['admin_name'];
        $this->admin_fullname = $_SESSION['admin_fullname'];
        $this->admin_email = $_SESSION['admin_email'];
        $this->admin_id = $_SESSION['admin_id'];
        $this->admin_role=$_SESSION['admin_role'];
 
        // set logged in status to true, because we just checked for this:
        // !empty($_SESSION['admin_name']) && ($_SESSION['admin_logged_in'] == 1)
        // when we called this method (in the constructor)
        $this->admin_is_logged_in = true;
    }

    private function loginWithCookieData() {
        if (isset($_COOKIE['rememberme'])) {

            list ($admin_id, $token, $hash) = explode(':', $_COOKIE['rememberme']);

            if ($hash == hash('sha256', $admin_id . ':' . $token . COOKIE_SECRET_KEY) && !empty($token)) {

                if ($this->databaseConnection()) {

                    // get real token from database (and all other data)
                    $sth = $this->db_connection->prepare("SELECT admin_id, admin_name, admin_email FROM admins WHERE admin_id = :admin_id
                                                      AND admin_rememberme_token = :admin_rememberme_token AND admin_rememberme_token IS NOT NULL");
                    $sth->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
                    $sth->bindValue(':admin_rememberme_token', $token, PDO::PARAM_STR);
                    $sth->execute();
                    // get result row (as an object)
                    $result_row = $sth->fetchObject();

                    if (isset($result_row->admin_id)) {

                        // write admin data into PHP SESSION [a file on your server]
                        $_SESSION['admin_id'] = $result_row->admin_id;
                        $_SESSION['admin_name'] = $result_row->admin_name;
                        $_SESSION['admin_email'] = $result_row->admin_email;
                        $_SESSION['admin_fullname'] = $result_row->admin_fullname;
                        $_SESSION['admin_role'] = $result_row->admin_role;
                        $_SESSION['admin_logged_in'] = 1;

                        // declare admin id, set the login status to true
                        $this->admin_id = $result_row->admin_id;
                        $this->admin_name = $result_row->admin_name;
                        $this->admin_email = $result_row->admin_email;
                        $this->admin_role = $result_row->admin_role;
                        $this->admin_fullname = $result_row->admin_fullname;
                        $this->admin_is_logged_in = true;

                        // Cookie token usable only once
                        $this->newRememberMeCookie();
                        return true;
                    } else {
                        $this->messages[] = "Wrong adminname/password";
                    }
                }
            }

            // A cookie has been used but is not valid... we delete it
            $this->deleteRememberMeCookie();
            $this->errors[] = "Invalid cookie";
        }
        return false;
    }

    private function loginWithPostData() {
        // if POST data (from login form) contains non-empty admin_name and non-empty admin_password
        if (!empty($_POST['admin_name']) && !empty($_POST['admin_password'])) {

            // database query, getting all the info of the selected admin
            $result_row = $this->getAdminData(trim($_POST['admin_name']));

            // if this admin exists
            if (isset($result_row->admin_id)) {

                // using PHP 5.5's password_verify() function to check if the provided passwords fits to the hash of that admin's password

                if (password_verify($_POST['admin_password'], $result_row->admin_password_hash)) {

                    if ($result_row->admin_active == 1) {

                        // write admin data into PHP SESSION [a file on your server]
                        $_SESSION['admin_id'] = $result_row->admin_id;
                        $_SESSION['admin_name'] = $result_row->admin_name;
                        $_SESSION['admin_email'] = $result_row->admin_email;
                        $_SESSION['admin_fullname'] = $result_row->admin_fullname;
                        $_SESSION['admin_role'] = $result_row->admin_role;
                        $_SESSION['admin_logged_in'] = 1;

                        // declare admin id, set the login status to true
                         $this->admin_id = $result_row->admin_id;
                        $this->admin_active = $result_row->admin_active;
                        $this->admin_name = $result_row->admin_name;
                        $this->admin_fullname = $result_row->admin_fullname;
                        $this->admin_email = $result_row->admin_email;
			$this->admin_role = $result_row->admin_role;
                        $this->admin_threshold_limit = $result_row->threshold_limit;
                       $this->admin_is_logged_in = true;

                        // if admin has check the "remember me" checkbox, then generate token and write cookie
                        if (isset($_POST['admin_rememberme'])) {

                            $this->newRememberMeCookie();
                        } else {

                            // Reset rememberme token
                            $this->deleteRememberMeCookie();
                        }
                        /*
                          // OPTIONAL: recalculate the admin's password hash
                          // DELETE this if-block if you like, it only exists to recalculate admins's hashes when you provide a cost factor,
                          // by default the script will use a cost factor of 10 and never change it.
                          // check if the have defined a cost factor in config/hashing.php
                          if (defined('HASH_COST_FACTOR')) {

                          // check if the hash needs to be rehashed
                          if (password_needs_rehash($result_row->admin_password_hash, PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR))) {

                          // calculate new hash with new cost factor
                          $this->admin_password_hash = password_hash($_POST['admin_password'], PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR));

                          // TODO: this should be put into another method !?
                          $query_update = $this->db_connection->prepare('UPDATE admins SET admin_password_hash = :admin_password_hash WHERE admin_id = :admin_id');
                          $query_update->bindValue(':admin_password_hash', $this->admin_password_hash, PDO::PARAM_STR);
                          $query_update->bindValue(':admin_id', $this->admin_id, PDO::PARAM_INT);
                          $query_update->execute();

                          if ($query_update->rowCount() == 0) {
                          // writing new hash was successful. you should now output this to the admin ;)
                          } else {
                          // writing new hash was NOT successful. you should now output this to the admin ;)
                          }

                          }

                          }
                         */
                        // TO CLARIFY: in future versions of the script: should we rehash every hash with standard cost factor
                        // when the HASH_COST_FACTOR in config/hashing.php is commented out ?                            
                    } else {

                        $this->errors[] = "Your account is not activated yet. Please click on the confirm link in the mail.";
                    }
                } else {

                    $this->errors[] = "Wrong password. Try again.";
                }
            } else {

                $this->errors[] = "This admin does not exist.";
            }
        } elseif (empty($_POST['admin_name'])) {

            $this->errors[] = "Adminname field was empty.";
        } elseif (empty($_POST['admin_password'])) {

            $this->errors[] = "Password field was empty.";
        }
    }

    /**
     * Create all data needed for remember me cookie connection on client and server side 
     */
    private function newRememberMeCookie() {
        // if database connection opened
        if ($this->databaseConnection()) {
            // generate 64 char random string and store it in current admin data
            $random_token_string = hash('sha256', mt_rand());
            $sth = $this->db_connection->prepare("UPDATE admins SET admin_rememberme_token = :admin_rememberme_token WHERE admin_id = :admin_id");
            $sth->execute(array(':admin_rememberme_token' => $random_token_string, ':admin_id' => $_SESSION['admin_id']));

            // generate cookie string that consists of adminid, randomstring and combined hash of both
            $cookie_string_first_part = $_SESSION['admin_id'] . ':' . $random_token_string;
            $cookie_string_hash = hash('sha256', $cookie_string_first_part . COOKIE_SECRET_KEY);
            $cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;

            // set cookie
            setcookie('rememberme', $cookie_string, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);
        }
    }

    /**
     * Delete all data needed for remember me cookie connection on client and server side 
     */
    private function deleteRememberMeCookie() {
        // if database connection opened
        if ($this->databaseConnection()) {
            // Reset rememberme token
            $sth = $this->db_connection->prepare("UPDATE admins SET admin_rememberme_token = NULL WHERE admin_id = :admin_id");
            $sth->execute(array(':admin_id' => $_SESSION['admin_id']));
        }

        // set the rememberme-cookie to ten years ago (3600sec * 365 days * 10).
        // that's obivously the best practice to kill a cookie via php
        // @see http://stackoverflow.com/a/686166/1114320
        setcookie('rememberme', false, time() - (3600 * 3650), '/', COOKIE_DOMAIN);
    }

    /**
     * perform the logout
     */
    public function doLogout() {
        $this->deleteRememberMeCookie();

        $_SESSION = array();
        session_destroy();

        $this->admin_is_logged_in = false;
        $this->messages[] = "You have been logged out.";
    }

    /**
     * simply return the current state of the admin's login
     * @return boolean admin's login status
     */
    public function isAdminLoggedIn() {
        return $this->admin_is_logged_in;
    }

    /**
     * edit the admin's name, provided in the editing form
     */
    public function editAdminName() {
        if (!empty($_POST['admin_name']) && $_POST['admin_name'] == $_SESSION["admin_name"]) {

            $this->errors[] = "Sorry, that adminname is the same as your current one. Please choose another one.";

            // adminname cannot be empty and must be azAZ09 and 2-64 characters
            // TODO: maybe this pattern should also be implemented in Registration.php (or other way round)
        } elseif (!empty($_POST['admin_name']) && preg_match("/^(?=.{2,64}$)[a-zA-Z][a-zA-Z0-9]*(?: [a-zA-Z0-9]+)*$/", $_POST['admin_name'])) {

            // escapin' this
            $this->admin_name = substr(trim($_POST['admin_name']), 0, 64);
            $this->admin_id = intval($_SESSION['admin_id']);

            // check if new adminname already exists
            $result_row = $this->getAdminData($this->admin_name);

            if (isset($result_row->admin_id)) {

                $this->errors[] = "Sorry, that adminname is already taken. Please choose another one.";
            } else {

                // write admin's new data into database
                $query_edit_admin_name = $this->db_connection->prepare('UPDATE admins SET admin_name = :admin_name WHERE admin_id = :admin_id');
                $query_edit_admin_name->bindValue(':admin_name', $this->admin_name, PDO::PARAM_STR);
                $query_edit_admin_name->bindValue(':admin_id', $this->admin_id, PDO::PARAM_INT);
                $query_edit_admin_name->execute();

                if ($query_edit_admin_name->rowCount()) {

                    $_SESSION['admin_name'] = $this->admin_name;
                    $this->messages[] = "Your adminname has been changed successfully. New adminname is " . $this->admin_name . ".";
                } else {

                    $this->errors[] = "Sorry, your chosen adminname renaming failed.";
                }
            }
        } else {

            $this->errors[] = "Sorry, your chosen adminname does not fit into the naming pattern.";
        }
    }

    /**
     * edit the admin's email, provided in the editing form
     */
    public function editAdminEmail() {
        if (!empty($_POST['admin_email']) && $_POST['admin_email'] == $_SESSION["admin_email"]) {

            $this->errors[] = "Sorry, that email address is the same as your current one. Please choose another one.";

            // admin mail cannot be empty and must be in email format
        } elseif (!empty($_POST['admin_email']) && filter_var($_POST['admin_email'], FILTER_VALIDATE_EMAIL)) {

            // if database connection opened
            if ($this->databaseConnection()) {

                // prevent database flooding
                $this->admin_email = substr(trim($_POST['admin_email']), 0, 64);
                // not really necessary, but just in case...
                $this->admin_id = intval($_SESSION['admin_id']);

                // write admins new data into database
                $query_edit_admin_email = $this->db_connection->prepare('UPDATE admins SET admin_email = :admin_email WHERE admin_id = :admin_id');
                $query_edit_admin_email->bindValue(':admin_email', $this->admin_email, PDO::PARAM_STR);
                $query_edit_admin_email->bindValue(':admin_id', $this->admin_id, PDO::PARAM_INT);
                $query_edit_admin_email->execute();

                if ($query_edit_admin_email->rowCount()) {

                    $_SESSION['admin_email'] = $this->admin_email;
                    $this->messages[] = "Your email address has been changed successfully. New email address is " . $this->admin_email . ".";
                } else {

                    $this->errors[] = "Sorry, your email changing failed.";
                }
            }
        } else {

            $this->errors[] = "Sorry, your chosen email does not fit into the naming pattern.";
        }
    }

    /**
     * edit the admin's password, provided in the editing form
     */
    public function editAdminPassword() {
        if (empty($_POST['admin_password_new']) || empty($_POST['admin_password_repeat']) || empty($_POST['admin_password_old'])) {

            $this->errors[] = "Empty Password";
        } elseif ($_POST['admin_password_new'] !== $_POST['admin_password_repeat']) {

            $this->errors[] = "Password and password repeat are not the same";
        } elseif (strlen($_POST['admin_password_new']) < 6) {

            $this->errors[] = "Password has a minimum length of 6 characters";

            // all the above tests are ok
        } else {

            // database query, getting hash of currently logged in admin (to check with just provided password)
            $result_row = $this->getAdminData($_SESSION['admin_name']);

            // if this admin exists
            if (isset($result_row->admin_password_hash)) {

                // using PHP 5.5's password_verify() function to check if the provided passwords fits to the hash of that admin's password
                if (password_verify($_POST['admin_password_old'], $result_row->admin_password_hash)) {

                    // now it gets a little bit crazy: check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                    // if so: put the value into $this->hash_cost_factor, if not, make $this->hash_cost_factor = null
                    $this->hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                    // crypt the admin's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                    // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                    // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                    // want the parameter: as an array with, currently only used with 'cost' => XX.
                    $this->admin_password_hash = password_hash($_POST['admin_password_new'], PASSWORD_DEFAULT, array('cost' => $this->hash_cost_factor));

                    // write admins new hash into database
                    $query_update = $this->db_connection->prepare('UPDATE admins SET admin_password_hash = :admin_password_hash WHERE admin_id = :admin_id');
                    $query_update->bindValue(':admin_password_hash', $this->admin_password_hash, PDO::PARAM_STR);
                    $query_update->bindValue(':admin_id', $_SESSION['admin_id'], PDO::PARAM_INT);
                    $query_update->execute();

                    // check if exactly one row was successfully changed:
                    if ($query_update->rowCount()) {

                        $this->messages[] = "Password sucessfully changed!";
                    } else {

                        $this->errors[] = "Sorry, your password changing failed.";
                    }
                } else {

                    $this->errors[] = "Your OLD password was wrong.";
                }
            } else {

                $this->errors[] = "This admin does not exist.";
            }
        }
    }

    /**
     * 
     */
    public function setPasswordResetDatabaseTokenAndSendMail() {
        // set token (= a random hash string and a timestamp) into database, to see that THIS admin really requested a password reset
        if ($this->setPasswordResetDatabaseToken() == true) {
            // send a mail to the admin, containing a link with that token hash string
            $this->sendPasswordResetMail();
        }
    }

    /**
     * 
     */
    public function setPasswordResetDatabaseToken() {
        if (empty($_POST['admin_name'])) {

            $this->errors[] = "Empty adminname";
        } else {

            // generate timestamp (to see when exactly the admin (or an attacker) requested the password reset mail)
            // btw this is an integer ;)
            $temporary_timestamp = time();

            // generate random hash for email password reset verification (40 char string)
            $this->admin_password_reset_hash = sha1(uniqid(mt_rand(), true));

            $this->admin_name = trim($_POST['admin_name']);

            // database query, getting all the info of the selected admin
            $result_row = $this->getAdminData($this->admin_name);


            // if this admin exists
            if (isset($result_row->admin_id)) {
                $this->admin_name = $result_row->admin_name;
                $this->admin_email = $result_row->admin_email;
                // database query: 
                $query_update = $this->db_connection->prepare('UPDATE admins SET admin_password_reset_hash = :admin_password_reset_hash,
                                                               admin_password_reset_timestamp = :admin_password_reset_timestamp
                                                               WHERE admin_name = :admin_name');
                $query_update->bindValue(':admin_password_reset_hash', $this->admin_password_reset_hash, PDO::PARAM_STR);
                $query_update->bindValue(':admin_password_reset_timestamp', $temporary_timestamp, PDO::PARAM_INT);
                $query_update->bindValue(':admin_name', $result_row->admin_name, PDO::PARAM_STR);
                $query_update->execute();

                // check if exactly one row was successfully changed:
                if ($query_update->rowCount() == 1) {

                    // define email
                    $this->admin_email = $result_row->admin_email;

                    return true;
                } else {

                    $this->errors[] = "Could not write token to database."; // maybe say something not that technical.
                }
            } else {

                $this->errors[] = "This adminname does not exist.";
            }
        }

        // return false (this method only returns true when the database entry has been set successfully)
        return false;
    }

    /**
     * 
     */
    public function sendPasswordResetMail() {
        $mail = new PHPMailer;

        // please look into the config/config.php for much more info on how to use this!
        // use SMTP or use mail()
        if (EMAIL_USE_SMTP) {

            // Set mailer to use SMTP
            $mail->IsSMTP();
            //useful for debugging, shows full SMTP errors
            //$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            // Enable SMTP authentication
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            // Enable encryption, usually SSL/TLS
            if (defined(EMAIL_SMTP_ENCRYPTION)) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            // Specify host server
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->Adminname = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->Port = EMAIL_SMTP_PORT;
        } else {

            $mail->IsMail();
        }

        $admin_email = $this->admin_email;
        $mail->From = EMAIL_PASSWORDRESET_FROM;
        $mail->FromName = EMAIL_PASSWORDRESET_FROM_NAME;
        $mail->AddAddress($admin_email);
        $mail->Subject = EMAIL_PASSWORDRESET_SUBJECT;

        $link = EMAIL_PASSWORDRESET_URL . '?admin_name=' . urlencode($this->admin_name) . '&verification_code=' . urlencode($this->admin_password_reset_hash);
        $mail->Body = EMAIL_PASSWORDRESET_CONTENT . ' <a href="' . $link . '">' . $link . '</a>';

        if (!$mail->Send()) {

            $this->errors[] = "Password reset mail NOT successfully sent! Error: " . $mail->ErrorInfo;
            return false;
        } else {

            $this->messages[] = "Password reset mail successfully sent!";
            return true;
        }
    }

    /**
     * 
     */
    public function checkIfEmailVerificationCodeIsValid() {
        if (!empty($_GET["admin_name"]) && !empty($_GET["verification_code"])) {

            // get adminname and password reset hash from url
            $this->admin_name = trim($_GET['admin_name']);
            $this->admin_password_reset_hash = $_GET['verification_code'];

            // database query, getting all the info of the selected admin
            $result_row = $this->getAdminData($this->admin_name);

            // if this admin exists and have the same hash in database
            if (isset($result_row->admin_id) && $result_row->admin_password_reset_hash == $this->admin_password_reset_hash) {

                $timestamp_one_hour_ago = time() - 3600; // 3600 seconds are 1 hour

                if ($result_row->admin_password_reset_timestamp > $timestamp_one_hour_ago) {

                    // set the marker to true, making it possible to show the password reset edit form view
                    $this->password_reset_link_is_valid = true;
                } else {

                    $this->errors[] = "Your reset link has expired. Please use the reset link within one hour.";
                }
            } else {

                $this->errors[] = "This adminname does not exist.";
            }
        } else {

            $this->errors[] = "Empty link parameter data.";
        }
    }

    /**
     * 
     */
    public function editNewPassword() {
        // TODO: timestamp!

        if (!empty($_POST['admin_name']) && !empty($_POST['admin_password_reset_hash']) && !empty($_POST['admin_password_new']) && !empty($_POST['admin_password_repeat'])) {

            if ($_POST['admin_password_new'] === $_POST['admin_password_repeat']) {

                if (strlen($_POST['admin_password_new']) >= 6) {

                    // if database connection opened
                    if ($this->databaseConnection()) {

                        // escapin' this, additionally removing everything that could be (html/javascript-) code
                        $this->admin_name = trim($_POST['admin_name']);
                        $this->admin_password_reset_hash = $_POST['admin_password_reset_hash'];

                        // no need to escape as this is only used in the hash function
                        $this->admin_password = $_POST['admin_password_new'];

                        // now it gets a little bit crazy: check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                        // if so: put the value into $this->hash_cost_factor, if not, make $this->hash_cost_factor = null
                        $this->hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                        // crypt the admin's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                        // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                        // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                        // want the parameter: as an array with, currently only used with 'cost' => XX.
                        $this->admin_password_hash = password_hash($this->admin_password, PASSWORD_DEFAULT, array('cost' => $this->hash_cost_factor));

                        // write admins new hash into database
                        $query_update = $this->db_connection->prepare('UPDATE admins SET admin_password_hash = :admin_password_hash, 
                                                                      admin_password_reset_hash = NULL, admin_password_reset_timestamp = NULL
                                                                      WHERE admin_name = :admin_name AND admin_password_reset_hash = :admin_password_reset_hash');
                        $query_update->bindValue(':admin_password_hash', $this->admin_password_hash, PDO::PARAM_STR);
                        $query_update->bindValue(':admin_password_reset_hash', $this->admin_password_reset_hash, PDO::PARAM_STR);
                        $query_update->bindValue(':admin_name', $this->admin_name, PDO::PARAM_STR);
                        $query_update->execute();

                        // check if exactly one row was successfully changed:
                        if ($query_update->rowCount() == 1) {

                            $this->password_reset_was_successful = true;
                            $this->messages[] = "Password sucessfully changed!";
                        } else {

                            $this->errors[] = "Sorry, your password changing failed.";
                        }
                    }
                } else {

                    $this->errors[] = "Password too short, please request a new password reset.";
                }
            } else {

                $this->errors[] = "Passwords dont match, please request a new password reset.";
            }
        }
    }

    /**
     * 
     * @return boolean
     */
    public function passwordResetLinkIsValid() {
        return $this->password_reset_link_is_valid;
    }

    /**
     * 
     * @return boolean
     */
    public function passwordResetWasSuccessful() {
        return $this->password_reset_was_successful;
    }

    /**
     * 
     */
    public function getAdminname() {
        return $this->admin_name;
    }

    /**
     * 
     */
    public function getPasswordResetHash() {
        return $this->admin_password_reset_hash;
    }

}

?>