<?php
class Quotation{
	public $quotation_id="";
	public $user_created="";
	public $created_at="";
	public $details="";
	public $assignee="";
	public $status="created";
	public $approved_date;
	public $approved_by;
	public $expire_date;
	public $discount;
	public $quotation_exists=false;
	private $db_connection=null;
	


    public function __construct($id=NULL)
    {
		$this->quotation_id=$id;
		$this->check_quotation_existance($id);
	}

    public function find_quotation($id)
    {
		$this->quotation_id=$id;
		$this->check_quotation_existance($id);
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

	public function check_quotation_existance($id){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM quotation WHERE quote_id = :quotation_id");
            $sth->bindValue(':quotation_id', $id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->quotation_id)) {
				$this->quotation_exists=true;
				$this->user_created=$result_row->user_created;
				$this->created_at=$result_row->created_at;
				$this->details=unserialize($result_row->details);
				$this->assignee=$result_row->assignee;
				$this->status=$result_row->status;				
				$this->approved_date=$result_row->approved_date;
				$this->approved_by=$result_row->approved_by;
				$this->expire_date=$result_row->expire_date;
				$this->discount=$result_row->discount;
			}		
		}else{
			$this->quotation_id='0';
		}
	}
	public function getCollection($user_id=NULL,$admin_type=NULL){
		$collection=array();
		if($this->databaseConnection()){
			if($admin_type==NULL){
				if($user_id==NULL){
                $sth = $this->db_connection->prepare("SELECT * FROM quotation WHERE 1");
				}else{
				$user_id=intval($user_id);
                $sth = $this->db_connection->prepare("SELECT * FROM quotation WHERE user_created = :user_created");            
			    $sth->bindValue(':user_created',$user_id , PDO::PARAM_INT);				
				}
			}else{
				$user_id=intval($user_id);
                $sth = $this->db_connection->prepare("SELECT * FROM quotation WHERE assignee = :assignee");
			    $sth->bindValue(':assignee',$user_id , PDO::PARAM_INT);				
			}
            $sth->execute();
            while($result_row = $sth->fetchObject()){
            if (isset($result_row->quote_id)) {
				$collection[]=$result_row;
			}
			}
		}else{
		}
		return $collection;
	}
	
	public function generateQuotation($discount){
		if($this->databaseConnection()){
			$details='';
			if(isset($_SESSION['pending_cart'])){
				$details=serialize($_SESSION['pending_cart']);
			}
            $sth = $this->db_connection->prepare("INSERT INTO `quotation` (`user_created`, `created_at`, `details`, `assignee`, `status`, `approved_date`, `approved_by`, `expire_date`, `discount`) VALUES (:user_created, :created_at,:details, :assignee, 'pending', :approved_date, '0',:expire_date, :discount);");
            $sth->bindValue(':user_created', $_SESSION['user_id'] , PDO::PARAM_INT);
            $sth->bindValue(':created_at',date('Y-m-d H-i-s') , PDO::PARAM_STR);
            $sth->bindValue(':details', $details, PDO::PARAM_STR);
            $sth->bindValue(':approved_date', date('Y-m-d H-i-s'), PDO::PARAM_STR);
            $sth->bindValue(':expire_date', date('Y-m-d H-i-s'), PDO::PARAM_STR);
            $user=new User($_SESSION['user_id']);
            if($user->assigned_to!=1){
            $sth->bindValue(':assignee',$user->assigned_to , PDO::PARAM_INT);
            }else{
            $sth->bindValue(':assignee','1' , PDO::PARAM_INT);
            }
            $sth->bindValue(':discount', $discount, PDO::PARAM_INT);
            $sth->execute();
			unset($_SESSION['pending_cart']);
			return true;
			
		}
		return false;
	}

      public function approve(){
          $admin=new Admin($_SESSION['admin_id']);
          if($admin->admin_threshold_limit>$this->discount){
            $sth = $this->db_connection->prepare("UPDATE quotation SET status='approved',approved_by=:approved_by WHERE quote_id=:request_id");
            $sth->bindValue(':approved_by', $_SESSION['admin_id'], PDO::PARAM_INT);
            $sth->bindValue(':request_id', $this->quotation_id, PDO::PARAM_INT);
            $sth->execute();
              
          }
      }
      public function cancel(){
          $admin=new Admin($_SESSION['admin_id']);
            $sth = $this->db_connection->prepare("UPDATE quotation SET status='cancelled',approved_by=:approved_by WHERE quote_id=:request_id");
            $sth->bindValue(':approved_by', $_SESSION['admin_id'], PDO::PARAM_INT);
            $sth->bindValue(':request_id', $this->quotation_id, PDO::PARAM_INT);
            $sth->execute();
      }
      public function reconsider(){
          $admin=new Admin($_SESSION['admin_id']);
            $sth = $this->db_connection->prepare("UPDATE quotation SET status='pending',approved_by=:approved_by WHERE quote_id=:request_id");
            $sth->bindValue(':approved_by', $_SESSION['admin_id'], PDO::PARAM_INT);
            $sth->bindValue(':request_id', $this->quotation_id, PDO::PARAM_INT);
            $sth->execute();
      }
      public function requestApproval(){
          $admin=new Admin($_SESSION['admin_id']);
          if($admin->admin_role=='SM'){$new_assignee=$admin->admin_head;}
          elseif($admin->admin_role=='RM'){$new_assignee=1;}
            $sth = $this->db_connection->prepare("UPDATE quotation SET assignee=:new_assignee,approved_by=:approved_by WHERE quote_id=:request_id");
            $sth->bindValue(':new_assignee', $new_assignee, PDO::PARAM_INT);
            $sth->bindValue(':approved_by', $_SESSION['admin_id'], PDO::PARAM_INT);
            $sth->bindValue(':request_id', $this->quotation_id, PDO::PARAM_INT);
            $sth->execute();
      }
}
?>