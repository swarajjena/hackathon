<?php
class Product{
	public $product_id="";
	public $product_name="";
	public $product_short_details="";
	public $product_details="";
	public $product_image="";
	public $product_exists=false;
	private $db_connection=null;
	


    public function __construct($id=NULL)
    {
		if(isset($id)){
		    $this->product_id=$id;
		    $this->check_product_existance($id);
			
		}else{
			
		}
		    $this->check_product_existance($this->product_id);
	}

    public function find_product($id)
    {
		$this->product_id=$id;
		$this->check_product_existance($id);
	}
	
    public function getCollection()
    {
		$collection=array();
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM product WHERE 1");
            $sth->execute();
            while($result_row = $sth->fetchObject()){
            if (isset($result_row->product_id)) {
				$prod=new Product($result_row->product_id);
				$collection[]=$prod;
			}
					
			}

		}else{
		}
		
		return $collection;
	}
	
    public function getMarkets()
    {
		$collection=array();
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM product_market WHERE  product_id = :product_id");
            $sth->bindValue(':product_id', $this->product_id, PDO::PARAM_INT);
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
	
    public function getMarketPrice($market_id)
    {
		$collection=0;
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM product_market WHERE  product_id = :product_id AND market_id=:market_id");
            $sth->bindValue(':product_id', $this->product_id, PDO::PARAM_INT);
            $sth->bindValue(':market_id', $market_id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();
            if (isset($result_row->market_id)) {
				$collection=$result_row->price;
			}
					
			

		}else{
		}
		
		return $collection;
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
	public function check_product_existance($id){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM product WHERE product_id = :product_id");
            $sth->bindValue(':product_id', $id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->product_id)) {
				$this->product_exists=true;
				$this->product_name=$result_row->product_name;
				$this->product_details=$result_row->product_details;
				$this->product_short_details=$result_row->product_short_details;
				$this->product_image=$result_row->product_image;				
			}		
		}else{
			$this->product_id='0';
		}
	}

	public function add_product(){
		if($this->databaseConnection()){
			$date=date('j-M-Y G:i');
            $sth = $this->db_connection->prepare("INSERT INTO product(product_name,product_short_details,product_image) 
			                                      VALUES(:product_name,:product_short_details,:product_image)");
            $sth->bindValue(':product_name', $_POST['product_name'], PDO::PARAM_STR);
            $sth->bindValue(':product_short_details', $_POST['product_short_details'], PDO::PARAM_STR);
            $sth->bindValue(':product_image',$this->product_image, PDO::PARAM_STR);
            $sth->execute();
//            print_r($sth->errorInfo());
			return $this->db_connection->lastInsertId();

		}else{
			$this->product_id='0';
			return false;
		}
	}
	public function update_product(){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("UPDATE product SET
																	product_name=:product_name,
																	product_short_details=:product_short_details,
																	product_image=:product_image
																	
																WHERE
																    product_id=:product_id
																	");
            $sth->bindValue(':product_name', $_POST['product_name'], PDO::PARAM_STR);
            $sth->bindValue(':product_short_details', $_POST['product_short_details'], PDO::PARAM_STR);
            $sth->bindValue(':product_image',$this->product_image, PDO::PARAM_STR);
            $sth->bindValue(':product_id',$this->product_id, PDO::PARAM_INT);
$sth->execute();$sth->errorInfo();

		}else{
			$this->product_id='0';
			return false;
		}
	}
        
    public function update_market_value($market_id, $price) {
        if ($this->databaseConnection()) {
            if ($this->getMarketPrice($market_id) > 0) {
            $sth = $this->db_connection->prepare("UPDATE product_market SET price=:price WHERE product_id=:product_id AND market_id=:market_id");
            $sth->bindValue(':product_id', $this->product_id, PDO::PARAM_INT);
            $sth->bindValue(':market_id', $market_id, PDO::PARAM_INT);
            $sth->bindValue(':price', $price, PDO::PARAM_INT);
            $sth->execute();
//            print_r($sth->errorInfo());   
            } else {
            $sth = $this->db_connection->prepare("INSERT INTO product_market(product_id,market_id,price) VALUES(:product_id,:market_id,:price)");
            $sth->bindValue(':product_id', $this->product_id, PDO::PARAM_INT);
            $sth->bindValue(':market_id', $market_id, PDO::PARAM_INT);
            $sth->bindValue(':price', $price, PDO::PARAM_INT);
            $sth->execute();
//            print_r($sth->errorInfo());   
                
            }
        }
    }

}
?>