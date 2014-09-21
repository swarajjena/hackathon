<?php

/**
 * class Login
 * handles the admin login/logout/session
 * 
 * @author Panique <panique@web.de>
 */
class Admin
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;
    /** @var int $admin_id The admin's id */
    public $admin_id = null;
    /** @var string $admin_name The admin's name */
    public $admin_name = "";
    /** @var string $admin_rank The admin's rank */
    public $admin_fullname = "";
    /** @var string $admin_email The admin's mail */
    public $admin_email = "";
    /** @var string $admin_password_hash The admin's hashed and salted password */
    public $admin_active = "";

    public $admin_role = "";
    public $admin_head = "";

    public $admin_threshold_limit = "";

    public $total_approves = "";

    public $total_money_approved = "";
    
    public $admin_found=false;


    public $post_success=false;

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */    
    public function __construct($admin_id=NULL)
    {
        // create/read session

        if (isset($admin_id)) {
			
			    $this->admin_id=$admin_id;

                $this->find_admin();

        }
    }

    /**
     * Checks if database connection is opened and open it if not
     */
    private function databaseConnection()
    {
        // connection already opened
        if ($this->db_connection != null) {
            return true;
        } else {
            // create a database connection, using the constants from config/config.php
            try {
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME, DB_USER, DB_PASS);
                return true;

                // If an error is catched, database connection failed
            } catch (PDOException $e) {
                return false;
            }
        }
    }

    private function find_admin()
    {
                if ($this->databaseConnection()) {

                    // get real token from database (and all other data)
                    $sth = $this->db_connection->prepare("SELECT * FROM admins WHERE admin_id = :admin_id");
                    $sth->bindValue(':admin_id', $this->admin_id, PDO::PARAM_INT);
                    $sth->execute();
                    // get result row (as an object)
                    $result_row = $sth->fetchObject();

                    if (isset($result_row->admin_id)) {

                        // declare admin id, set the login status to true
                        $this->admin_id = $result_row->admin_id;
                        $this->admin_active = $result_row->admin_active;
                        $this->admin_name = $result_row->admin_name;
                        $this->admin_fullname = $result_row->admin_fullname;
                        $this->admin_email = $result_row->admin_email;
			$this->admin_role = $result_row->admin_role;
                        $this->admin_threshold_limit = $result_row->threshold_limit;
                        $this->admin_head = $result_row->admin_head;
                        

                        $this->admin_found = true;

                        return true;
                    }
                }
        return false;
    }
    public function find_admin_by_admin_name($admin_name){
                if ($this->databaseConnection()) {

                    // get real token from database (and all other data)
                    $sth = $this->db_connection->prepare("SELECT * FROM admins WHERE admin_name = :admin_name");
                    $sth->bindValue(':admin_name', $admin_name, PDO::PARAM_STR);
                    $sth->execute();
                    // get result row (as an object)
                    $result_row = $sth->fetchObject();

                    if (isset($result_row->admin_id)) {

                        // declare admin id, set the login status to true
                        $this->admin_id = $result_row->admin_id;
                        $this->admin_active = $result_row->admin_active;
                        $this->admin_name = $result_row->admin_name;
                        $this->admin_fullname = $result_row->admin_fullname;
                        $this->admin_email = $result_row->admin_email;
			$this->admin_role = $result_row->admin_role;
                        $this->admin_threshold_limit = $result_row->threshold_limit;
                        $this->admin_head = $result_row->admin_head;
                        

                        $this->admin_found = true;

                        return true;
                    }
                }
        return false;
    }
    public function adminList() {
        $collection=array();
        if ($this->databaseConnection()) {
                $sth = $this->db_connection->prepare("SELECT * FROM admins WHERE 1");
                $sth->execute();
            while ($result_row = $sth->fetchObject()) {

                $collection[] = $result_row;
            }
        }
        return $collection;
    }
    
    public function adminListRM() {
        $collection = array();
        if ($this->databaseConnection()) {
                $sth = $this->db_connection->prepare("SELECT * FROM admins WHERE admin_role = :admin_role");
                $sth->bindValue(':admin_role', 'RM', PDO::PARAM_STR);
                $sth->execute();
            while ($result_row = $sth->fetchObject()) {

                $collection[] = $result_row;
            }
        }
        return $collection;
    }
    public function adminListSM() {
        $collection = array();
        if ($this->databaseConnection()) {
                $sth = $this->db_connection->prepare("SELECT * FROM admins WHERE admin_role = :admin_role");
                $sth->bindValue(':admin_role', 'SM', PDO::PARAM_STR);
                $sth->execute();
            while ($result_row = $sth->fetchObject()) {

                $collection[] = $result_row;
            }
        }
        return $collection;
    }

    public function setData($field,$data,$datatype='STR'){
                if ($this->databaseConnection()) {
					

                    // get real token from database (and all other data)
                    $sth = $this->db_connection->prepare("UPDATE admins SET ".$field."=:data WHERE admin_id = :admin_id");
                    if($datatype=='INT'){$sth->bindValue(':data', $data, PDO::PARAM_INT);}
                    else{ $sth->bindValue(':data', $data, PDO::PARAM_STR);}
                    $sth->bindValue(':admin_id', $this->admin_id, PDO::PARAM_INT);
					
                    $sth->execute();
                    return TRUE;
                }
        return false;
	}
    public function registerNewUser($full_name, $admin_email, $admin_password,$admin_role)
    {
        // we just remove extra space on adminname and email
        $full_name  = trim($full_name);
        $admin_email = trim($admin_email);
		$admin_name=str_replace(' ','_',$full_name).rand(1000,1000000);

        // check provided data validity
        if (empty($full_name)) {

            $this->errors[] = "Empty Full name";

        } elseif (strlen($admin_password) < 6) {

            $this->errors[] = "Password has a minimum length of 6 characters";

        } elseif (empty($admin_email)) {

            $this->errors[] = "Email cannot be empty";

        } elseif (strlen($admin_email) > 64) {

            $this->errors[] = "Email cannot be longer than 64 characters";

        } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {

            $this->errors[] = "Your email address is not in a valid email format";

        // finally if all the above checks are ok
        } else {

            // if database connection opened
            if ($this->databaseConnection()) {

                // now it gets a little bit crazy: check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
                $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                // crypt the admin's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                // want the parameter: as an array with, currently only used with 'cost' => XX.
                $admin_password_hash = password_hash($admin_password);

                // check if admin already exists
                $query_check_admin_name = $this->db_connection->prepare('SELECT admin_name FROM admins WHERE admin_name=:admin_name');
                $query_check_admin_name->bindValue(':admin_name', $admin_name, PDO::PARAM_STR);
                $query_check_admin_name->execute();
                
				$query_check_email = $this->db_connection->prepare('SELECT admin_email FROM admins WHERE admin_email =:email');
                $query_check_email->bindValue(':email', $admin_email, PDO::PARAM_STR);
                $query_check_email->execute();

                if ($query_check_admin_name->fetchColumn() != false) {

                    $this->errors[] = "Sorry, that adminname is already taken. Please choose another one.";

                }elseif ($query_check_email->fetchColumn() != false) {

                    $this->errors[] = "Sorry, that email is already taken. Please choose another one.";

                } else {

                    // generate random hash for email verification (40 char string)
                    $admin_activation_hash = sha1(uniqid(mt_rand(), true));

                    // write new admins data into database
                    $query_new_admin_insert = $this->db_connection->prepare('INSERT INTO admins (admin_name,admin_role,admin_fullname, admin_password_hash, admin_email, admin_activation_hash, admin_registration_ip, admin_registration_datetime,threshold_limit) VALUES(:admin_name,:admin_role,:admin_fullname, :admin_password_hash, :admin_email, :admin_activation_hash, :admin_registration_ip, now(),:threshold_limit)');
                    
                    $query_new_admin_insert->bindValue(':admin_name', $admin_name, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_fullname', $full_name, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_password_hash', $admin_password_hash, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_email', $admin_email, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_role', $admin_role, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_activation_hash', $admin_activation_hash, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_registration_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                    if($admin_role=='NH'){$th='100';}
                    elseif($admin_role=='RM'){$th='30';}
                    elseif($admin_role=='SM'){$th='15';}
                    $query_new_admin_insert->bindValue(':threshold_limit', $th, PDO::PARAM_INT);
                    
                    
                    $query_new_admin_insert->execute();
 //                   print_r($query_new_admin_insert->errorInfo());

                    // id of new admin
                    $admin_id = $this->db_connection->lastInsertId();
                    return $admin_id;

                    if ($query_new_admin_insert) {

                        // send a verification email
                            $this->messages[] = "Your account has been created successfully,Please login to continue";
                        if ($this->sendVerificationEmail($admin_id, $admin_email, $admin_activation_hash)) {

                            // when mail has been send successfully
                            $this->registration_successful = true;

                        } else {
                        }

                    } else {

                        $this->errors[] = "Sorry, your registration failed. Please go back and try again.";

                    }
                }
            }
        }
    }
    public function updateUser($admin_id,$full_name, $admin_email, $admin_password,$admin_role,$admin_head)
    {
            if ($this->databaseConnection()) {

                // now it gets a little bit crazy: check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
                $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                // crypt the admin's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                // want the parameter: as an array with, currently only used with 'cost' => XX.
                $admin_password_hash = password_hash($admin_password);



                    // generate random hash for email verification (40 char string)
                    $admin_activation_hash = sha1(uniqid(mt_rand(), true));

                    // write new admins data into database
                    $query_new_admin_insert = $this->db_connection->prepare('UPDATE admins SET admin_role=:admin_role,admin_fullname=:admin_fullname, admin_password_hash=:admin_password_hash, admin_email=:admin_email,admin_head=:admin_head WHERE admin_id=:admin_id');
                    $query_new_admin_insert->bindValue(':admin_fullname', $full_name, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_password_hash', $admin_password_hash, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_email', $admin_email, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_role', $admin_role, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_head', $admin_head, PDO::PARAM_INT);
                    $query_new_admin_insert->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
                    $query_new_admin_insert->execute();

                    // id of new admin
            }
    }
    
    public function resetPassword($password){
            if ($this->databaseConnection()) {

                // now it gets a little bit crazy: check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
                $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                // crypt the admin's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                // want the parameter: as an array with, currently only used with 'cost' => XX.
                $admin_password_hash = password_hash($password);



                    // write new admins data into database
                    $query_new_admin_insert = $this->db_connection->prepare('UPDATE admins SET admin_password_hash=:admin_password_hash  WHERE admin_id=:admin_id');
                    $query_new_admin_insert->bindValue(':admin_password_hash', $admin_password_hash, PDO::PARAM_STR);
                    $query_new_admin_insert->bindValue(':admin_id', $this->admin_id, PDO::PARAM_INT);
                    $query_new_admin_insert->execute();
                    

                    // id of new admin
            }
    }
}
?>