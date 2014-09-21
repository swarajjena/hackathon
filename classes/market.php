<?php
class Market{
	public $market_id="";
	public $market_name="";
	public $market_details="";
	public $market_exists=false;
	private $db_connection=null;
	


    public function __construct($id=NULL)
    {
		$this->market_id=$id;
		$this->check_market_existance($id);
	}

    public function find_market($id)
    {
		$this->market_id=$id;
		$this->check_market_existance($id);
	}
	
	
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
    public function getCollection()
    {
		$collection=array();
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM market WHERE 1");
            $sth->execute();
            while($result_row = $sth->fetchObject()){
            if (isset($result_row->market_id)) {
				$prod=new Market($result_row->market_id);
				$collection[]=$prod;
			}
					
			}

		}else{
		}
		
		return $collection;
	}

	public function add_market(){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("INSERT INTO market(market_name,market_details) 
			                                      VALUES(:market_name,:market_details)");
            $sth->bindValue(':market_name', $_POST['market_name'], PDO::PARAM_STR);
            $sth->bindValue(':market_details', $_POST['market_details'], PDO::PARAM_STR);
            $sth->execute();
//            print_r($sth->errorInfo());
			return $this->db_connection->lastInsertId();

		}else{
			$this->market_id='0';
			return false;
		}
	}
	public function update_market(){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("UPDATE market SET
																	market_name=:market_name,
																	market_details=:market_details,
																	
																WHERE
																    market_id=:market_id
																	");
            $sth->bindValue(':market_name', $_POST['market_name'], PDO::PARAM_STR);
            $sth->bindValue(':market_details', $_POST['market_details'], PDO::PARAM_STR);
            $sth->bindValue(':market_id',$this->market_id, PDO::PARAM_INT);
$sth->execute();$sth->errorInfo();

		}else{
			$this->market_id='0';
			return false;
		}
	}
	public function check_market_existance($id){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM market WHERE market_id = :market_id");
            $sth->bindValue(':market_id', $id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->market_id)) {
				$this->market_exists=true;
				$this->market_name=$result_row->market_name;
				$this->market_details=$result_row->market_details;
			}		
		}else{
			$this->market_id='0';
		}
	}


}
?>