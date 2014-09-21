<?php

/**
 * class Registration
 * handles the user registration
 * 
 * @author Panique <panique@web.de>
 * @version 1.1
 */
class Registration
{
    private $db_connection            = null;    // database connection   

    public  $registration_successful  = false;
    public  $verification_successful  = false;

    public  $errors                   = array(); // collection of error messages
    public  $messages                 = array(); // collection of success / neutral messages

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */    
    public function __construct()
    {

        // if we have such a POST request, call the registerNewUser() method
        if (isset($_POST["register"])) {

			$this->registerNewUser($_POST['full_name'], $_POST['user_email'], $_POST['user_password_new'], $_POST['user_password_repeat'], $_POST["captcha"]);

        }
        // if we have such a GET request, call the verifyNewUser() method
        if (isset($_GET["id"]) && isset($_GET["verification_code"])) {

            $this->verifyNewUser($_GET["id"], $_GET["verification_code"]);

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
                $this->errors[] = "Database connection problem.";
                return false;
            }
        }
    }

    /**
     * registerNewUser()
     * 
     * handles the entire registration process. checks all error possibilities, and creates a new user in the database if
     * everything is fine
     */
    private function registerNewUser($full_name, $user_email, $user_password, $user_password_repeat, $captcha)
    {
        // we just remove extra space on username and email
        $full_name  = trim($full_name);
        $user_email = trim($user_email);
		$user_name=str_replace(' ','_',$full_name).rand(1000,1000000);

        // check provided data validity
        if (strtolower($captcha) != strtolower($_SESSION['captcha'])) {

            $this->errors[] = "Captcha was wrong!";

        } elseif (empty($full_name)) {

            $this->errors[] = "Empty Full name";

        } elseif (empty($user_password) || empty($user_password_repeat)) {

            $this->errors[] = "Empty Password";            

        } elseif ($user_password !== $user_password_repeat) {

            $this->errors[] = "Password and password repeat are not the same";

        } elseif (strlen($user_password) < 6) {

            $this->errors[] = "Password has a minimum length of 6 characters";

        } elseif (empty($user_email)) {

            $this->errors[] = "Email cannot be empty";

        } elseif (strlen($user_email) > 64) {

            $this->errors[] = "Email cannot be longer than 64 characters";

        } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {

            $this->errors[] = "Your email address is not in a valid email format";

        // finally if all the above checks are ok
        } else {

            // if database connection opened
            if ($this->databaseConnection()) {

                // now it gets a little bit crazy: check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
                $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                // want the parameter: as an array with, currently only used with 'cost' => XX.
                $user_password_hash = password_hash($user_password);

                // check if user already exists
                $query_check_user_name = $this->db_connection->prepare('SELECT user_name FROM users WHERE user_name=:user_name');
                $query_check_user_name->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $query_check_user_name->execute();
                
				$query_check_email = $this->db_connection->prepare('SELECT user_email FROM users WHERE user_email =:email');
                $query_check_email->bindValue(':email', $user_email, PDO::PARAM_STR);
                $query_check_email->execute();

                if ($query_check_user_name->fetchColumn() != false) {

                    $this->errors[] = "Sorry, that username is already taken. Please choose another one.";

                }elseif ($query_check_email->fetchColumn() != false) {

                    $this->errors[] = "Sorry, that email is already taken. Please choose another one.";

                } else {

                    // generate random hash for email verification (40 char string)
                    $user_activation_hash = sha1(uniqid(mt_rand(), true));

                    // write new users data into database
                    $query_new_user_insert = $this->db_connection->prepare('INSERT INTO users (user_name,user_fullname, user_password_hash, user_email, user_activation_hash, user_registration_ip, user_registration_datetime) VALUES(:user_name,:user_fullname, :user_password_hash, :user_email, :user_activation_hash, :user_registration_ip, now())');
                    $query_new_user_insert->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':user_fullname', $full_name, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':user_email', $user_email, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':user_activation_hash', $user_activation_hash, PDO::PARAM_STR);
                    $query_new_user_insert->bindValue(':user_registration_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                    $query_new_user_insert->execute();

                    // id of new user
                    $user_id = $this->db_connection->lastInsertId();

                    if ($query_new_user_insert) {

                        // send a verification email
                            $this->messages[] = "Your account has been created successfully,Please login to continue";
                        if ($this->sendVerificationEmail($user_id, $user_email, $user_activation_hash)) {

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

    /*
     * sendVerificationEmail()
     * sends an email to the provided email address
     * @return boolean gives back true if mail has been sent, gives back false if no mail could been sent
     */
    public function sendVerificationEmail($user_id, $user_email, $user_activation_hash)
    {
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
            $mail->Username = EMAIL_SMTP_USERNAME;                            
            $mail->Password = EMAIL_SMTP_PASSWORD;                      
            $mail->Port = EMAIL_SMTP_PORT;       

        } else {

            $mail->IsMail();            
        }

        $mail->From = EMAIL_VERIFICATION_FROM;
        $mail->FromName = EMAIL_VERIFICATION_FROM_NAME;        
        $mail->AddAddress($user_email);
        $mail->Subject = EMAIL_VERIFICATION_SUBJECT;

        $link = EMAIL_VERIFICATION_URL.'?id='.urlencode($user_id).'&verification_code='.urlencode($user_activation_hash);

        // the link to your register.php, please set this value in config/email_verification.php
        $mail->Body = EMAIL_VERIFICATION_CONTENT.' '.$link;

        if(!$mail->Send()) {

            return false;

        } else {

            return true;

        }
    }

    /**
     * verifyNewUser()
     * checks the id/verification code combination and set the user's activation status to true (=1) in the database
     */
    public function verifyNewUser($user_id, $user_activation_hash)
    {
        // if database connection opened
        if ($this->databaseConnection()) {

            // try to update user with specified information
			
            $query_update_user = $this->db_connection->prepare('UPDATE users SET user_active = 1, user_activation_hash = NULL WHERE user_id = :user_id AND user_activation_hash = :user_activation_hash');
            $query_update_user->bindValue(':user_id', intval(trim($user_id)), PDO::PARAM_INT);
            $query_update_user->bindValue(':user_activation_hash', $user_activation_hash, PDO::PARAM_STR);
            $query_update_user->execute();

                // check if user already exists
                $query_check_user_name = $this->db_connection->prepare('SELECT * FROM users WHERE user_id=:user_id');
                $query_check_user_name->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $query_check_user_name->execute();

            if ($query_update_user->rowCount() > 0) {
				$user_data=$query_check_user_name->fetchObject();
				$ref_user=$user_data->reference_user; 
				$this->update_user_points('User registered: Reference',500,'positive','not defined','',$ref_user);

                $this->verification_successful = true;
                $this->messages[] = "Activation was successful! You can now log in!<a href='index.php#login'> <u>Please click here the link to login</u></a></br></br></br></br></br></br></br></br></br></br></br></br></br>";

            }elseif($query_check_user_name->rowCount()>0){
                $this->verification_successful = true;
                $this->messages[] = "Already activated/Expired link.<a href='index.php#login'> <u>Please click here the link to login</u></a></br></br></br></br></br></br></br></br></br></br></br></br></br>";
				
			}
			 else {

                $this->errors[] = "Sorry, no such id/verification code combination here...";

            }

        }

    }
	public function update_user_points($details_text='not defined',
	                                   $points=0,
									   $change_type='positive',
									   $target_type='not defined',
									   $target_id='',
									   $user_name=''){
		$final_points=0;
		if($this->databaseConnection()){
                $check_user_name = $this->db_connection->prepare('SELECT * FROM users WHERE user_name=:user_name');
                $check_user_name->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $check_user_name->execute();
				$check_user_name=$check_user_name->fetchObject();
			if(isset($check_user_name->user_id)){
				if($change_type=='positive'){
				$final_points=($check_user_name->user_points)+$points;
				}elseif($change_type=='negative'){
				$final_points=($check_user_name->user_points)-$points;
				}else{
				$final_points=($check_user_name->user_points);
				}
				
				
				if($check_user_name->max_points_reached<$final_points){
					$check_user_name->max_points_reached=$final_points;
				}
				
                 $sth = $this->db_connection->prepare("UPDATE users SET user_points=:user_points,max_points_reached=:max_points_reached
																						 WHERE user_name = :user_name");
                 $sth->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                 $sth->bindValue(':max_points_reached', $check_user_name->max_points_reached, PDO::PARAM_STR);
                 $sth->bindValue(':user_points',$final_points, PDO::PARAM_STR);
                 $sth->execute();
                 $sth = $this->db_connection->prepare("INSERT INTO  `points_history` (
                                                                                     `details_text` ,
																					 `points_changed` ,
																					 `change_type` ,
																					 `user_id` ,
																					 `target_type` ,
																					 `target_id`
																					 )
																					 
																					 VALUES (
																					 :details_text,  
																					 :points_changed,  
																					 :change_type,  
																					 :user_id,  
																					 :target_type,  
																					 :target_id);
																					 ");
                 $sth->bindValue(':details_text', $details_text, PDO::PARAM_STR);
                 $sth->bindValue(':points_changed', $points, PDO::PARAM_STR);
                 $sth->bindValue(':target_type', $target_type, PDO::PARAM_STR);
                 $sth->bindValue(':change_type', $change_type, PDO::PARAM_STR);
                 $sth->bindValue(':user_id', $check_user_name->user_id, PDO::PARAM_INT);
                 $sth->bindValue(':target_id', $target_id, PDO::PARAM_INT);
                 $sth->execute();
				 
			}
		}
		return $final_points;
	}


}
