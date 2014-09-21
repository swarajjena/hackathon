<?php

/**
 * class Login
 * handles the user login/logout/session
 * 
 * @author Panique <panique@web.de>
 */
class User
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;
    /** @var int $user_id The user's id */
    public $user_id = null;
    /** @var string $user_name The user's name */
    public $user_name = "";
    /** @var string $user_rank The user's rank */
    public $user_rank = "";
    /** @var string $user_email The user's mail */
    public $user_email = "";
    /** @var string $user_password_hash The user's hashed and salted password */
    public $user_found = "";

    public $user_image_url = "";


    public $user_fullname = "";
    /** @var string $user_name The user's name */
    public $user_address = "";
    /** @var string $user_name The user's name */
    public $user_contact_number = "";
    /** @var string $user_name The user's name */
    public $user_state= "";
    /** @var string $user_name The user's name */
    public $user_constituency= "";


    public $user_about= "";	
    
    public $assigned_to= "";	


    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */    
    public function __construct($user_id=NULL)
    {
        // create/read session

        if (isset($user_id)) {
			
			    $this->user_id=$user_id;

                $this->find_user();

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

    private function find_user()
    {
                if ($this->databaseConnection()) {

                    // get real token from database (and all other data)
                    $sth = $this->db_connection->prepare("SELECT * FROM users WHERE user_id = :user_id");
                    $sth->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
                    $sth->execute();
                    // get result row (as an object)
                    $result_row = $sth->fetchObject();

                    if (isset($result_row->user_id)) {

                        // declare user id, set the login status to true
                        $this->user_id = $result_row->user_id;
                        $this->user_active = $result_row->user_active;
                        $this->user_name = $result_row->user_name;
                        $this->user_fullname = $result_row->user_fullname;
                        $this->user_email = $result_row->user_email;
                        $this->assigned_to = $result_row->assigned_to;
                        $this->user_found = true;

                        return true;
                    }
                }
        return false;
    }
    public function getUsers($from=null,$num=null)
    {
        $collection=array();
                if ($this->databaseConnection()) {

                    // get real token from database (and all other data)
                    $sth = $this->db_connection->prepare("SELECT * FROM users WHERE 1 ORDER BY assigned_to ASC LIMIT ".$from.",".$num." ");
                    $sth->execute();
                    // get result row (as an object)
                    while($result_row = $sth->fetchObject()){

                    if (isset($result_row->user_id)) {

                        // declare user id, set the login status to true
                        $usr=new User($result_row->user_id);
                        $collection[]=$usr;
                    }
                    }
                }
        return $collection;
    }
	public function update_assigned_to($assign_to){
		$this->assigned_to=trim($assign_to);
		if($this->databaseConnection()){

			if(isset($this->user_id)){
                 $sth = $this->db_connection->prepare("UPDATE users SET assigned_to=:assigned_to WHERE user_id = :user_id");
                 $sth->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
                 $sth->bindValue(':assigned_to', $this->assigned_to, PDO::PARAM_INT);
                 $sth->execute();
                 $sth = $this->db_connection->prepare("UPDATE quotation SET assignee=:assigned_to WHERE user_created = :user_id");
                 $sth->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
                 $sth->bindValue(':assigned_to', $this->assigned_to, PDO::PARAM_INT);
                 $sth->execute();
			}
		}
	}
	
}
?>