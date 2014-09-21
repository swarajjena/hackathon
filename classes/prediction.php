<?php
class Prediction
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;

    public $prediction_id = "";

    public $prediction_title = "";
	
	public $prediction_found=false;

    // activated=0 : not activated,1 : running ,2: closed, 3: result declared
	public $prediction_activated=0;
	
	public $status='open';

	public $short_details="";
	
	public $full_details="";
	
	public $prediction_open_date="";

	public $time_limit="";
	
	public $result_out_date="";

	public $correct_option="";
	
	public $prediction_winner="";

    public function __construct($id = NULL)
    {
		if(isset($id)){
            $this->prediction_id= $this->check_existance($id);	
    		if(isset($_POST['prediction_title'])){
               $this->prediction_id= $this->update_prediction();	
	    	}
			if(isset($_GET['put_online'])){
		       if($this->databaseConnection()){
        		   $date=date('j-M-Y G:i');
                   $sth = $this->db_connection->prepare("UPDATE prediction SET activated = 1 ,activate_on=:activate_on
																 WHERE prediction_id = :prediction_id
																 	 ");
                   $sth->bindValue(':activate_on',$date, PDO::PARAM_STR);
                   $sth->bindValue(':prediction_id',$this->prediction_id, PDO::PARAM_INT);
                   $sth->execute();
			       }
			}
			if(isset($_GET['put_offline'])){
		       if($this->databaseConnection()){
                   $sth = $this->db_connection->prepare("UPDATE prediction SET activated = 0 
																 WHERE prediction_id = :prediction_id
																 	 ");
                   $sth->bindValue(':prediction_id',$this->prediction_id, PDO::PARAM_INT);
                   $sth->execute();
			       }
			}
            $this->prediction_id= $this->check_existance($id);	
			$this->status=$this->check_status();
		}else{
    		if(isset($_POST['add_prediction'])){
                $this->prediction_id= $this->add_prediction();	
	    	}
		}
		
    }
	function check_existance($id){
		$prediction_id=intval($id);
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM prediction WHERE prediction_id = :prediction_id");
            $sth->bindValue(':prediction_id', $prediction_id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->prediction_id)) {
				$this->prediction_found=true;
				$this->prediction_title=$result_row->prediction_title;
				$this->prediction_activated=$result_row->activated;
				$this->short_details=$result_row->short_details;
				$this->full_details=$result_row->full_details;
				$this->prediction_open_date=$result_row->activate_on;
				$this->time_limit=$result_row->time_limit;
				$this->result_out_date=$result_row->result_out_date;
				$this->correct_option=$result_row->correct_option_id;
				$this->prediction_winner=$result_row->lucky_draw_winner;
				return $result_row->prediction_id;
			}		
		}
		
	}
	
	function add_prediction(){
		$prediction_title=$_POST['prediction_title'];
		$short_details=$_POST['short_details'];
		$full_details=$_POST['full_details'];
		$time_limit=$_POST['time_limit'];
		$result_out_date=$_POST['result_out_date'];
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("INSERT INTO prediction(
			                                                             
																	prediction_title,
																	short_details,
																	full_details,	
																	time_limit,
																	result_out_date) 
			                                                         
																	VALUES(
																	:prediction_title,
																	:short_details,
																	:full_details,
																	:time_limit,
																	:result_out_date
																	 )");
            $sth->bindValue(':prediction_title', $prediction_title, PDO::PARAM_STR);
            $sth->bindValue(':short_details', $short_details, PDO::PARAM_STR);
            $sth->bindValue(':full_details',$full_details, PDO::PARAM_STR);
            $sth->bindValue(':time_limit',$time_limit, PDO::PARAM_STR);
            $sth->bindValue(':result_out_date',$result_out_date, PDO::PARAM_STR);
            $sth->execute();
			$sth->errorInfo();
			return $this->db_connection->lastInsertId();

		}else{
			$this->prediction_id='0';
			return false;
		}
		
	}
	function update_prediction(){
		$prediction_title=$_POST['prediction_title'];
		$short_details=$_POST['short_details'];
		$full_details=$_POST['full_details'];
		$time_limit=$_POST['time_limit'];
		$result_out_date=$_POST['result_out_date'];
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("UPDATE prediction SET prediction_title = :prediction_title ,
																	short_details = :short_details ,
																	full_details = :full_details ,	
																	time_limit = :time_limit ,
																	result_out_date = :result_out_date  
																 WHERE prediction_id = :prediction_id
																 	 ");
            $sth->bindValue(':prediction_title', $prediction_title, PDO::PARAM_STR);
            $sth->bindValue(':short_details', $short_details, PDO::PARAM_STR);
            $sth->bindValue(':full_details',$full_details, PDO::PARAM_STR);
            $sth->bindValue(':time_limit',$time_limit, PDO::PARAM_STR);
            $sth->bindValue(':result_out_date',$result_out_date, PDO::PARAM_STR);
            $sth->bindValue(':prediction_id',$this->prediction_id, PDO::PARAM_INT);
            $sth->execute();
			$sth->errorInfo();
			return $this->prediction_id;

		}else{
			$this->prediction_id='0';
			return false;
		}
		
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
                return false;
            }
        }
    }
	
	function check_status(){
	   $activate_on=$this->prediction_open_date;
	   $time_limit=$this->time_limit;
	   $activate_on_date=explode(' ', $activate_on);
	   $activate_on_date=$activate_on_date[0];
	   $activate_on_days=date_to_days($activate_on_date);
	   $closing_days=$activate_on_days+$time_limit;
	   $closing_date=day_to_date($closing_days);
	   $current_date=date('j-m-Y');
	   $current_days=date_to_days($current_date);
	   if($closing_days<$current_days){
		   return 'closed';
	   }else{
		   return 'open';
	   }
	}
	
}
class Option
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;

    public $prediction_id = "";

    public $option_id = "";

    public $option_title = "";
	
	public $option_found=false;

	public $option_image="";
	
	public $option_weightage="";

	public $total_amount_invested="";

	public $total_people_invested="";
	
	public $option_correct=0;
	
    public function __construct($id = NULL)
    {
		if(isset($id)){
            $this->option_id= $this->check_existance($id);	
    		if(isset($_POST['option_title'])){
				if($this->option_image==''){
				$rand=round(rand()*10000,4);
    				$this->option_image='predictions/options/'.str_replace(' ','_',$_POST['option_title']).$_GET['pred_id'].$rand.'.jpg';
				}
			   	move_uploaded_file($_FILES["option_image"]["tmp_name"],"images/".$this->option_image);
			   	copy("images/".$this->option_image,"../".MAIN_SITE_FOLDER."/images/".$this->option_image);
               $this->option_id= $this->update_option();
	
	    	}
            $this->option_id= $this->check_existance($id);	
		}else{
    		if(isset($_POST['add_option'])){
				$rand=round(rand()*10000,4);
    			$this->option_image='predictions/options/'.str_replace(' ','_',$_POST['option_title']).$_GET['pred_id'].$rand.'.jpg';
			   	move_uploaded_file($_FILES["option_image"]["tmp_name"],"images/".$this->option_image);
			   	copy("images/".$this->option_image,"../".MAIN_SITE_FOLDER."/images/".$this->option_image);
				
                $this->option_id= $this->add_option();	
	    	}
		}
		
    }
	function check_existance($id){
		$option_id=intval($id);
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM prediction_options WHERE option_id = :option_id");
            $sth->bindValue(':option_id', $option_id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->prediction_id)) {
				$this->option_found=true;
				$this->prediction_id=$result_row->prediction_id;
				$this->option_title=$result_row->option_title;
				$this->option_image=$result_row->option_image;
				$this->option_correct=$result_row->correct;
				$this->option_weightage=$result_row->option_weightage;
				$this->total_amount_invested=$result_row->total_amount_invested;
				$this->total_people_invested=$result_row->total_people_invested;
				return $result_row->option_id;
			}		
		}
		
	}
	
	function add_option(){
		if(isset($_GET['pred_id']) && $this->databaseConnection()){
    		$option_title=$_POST['option_title'];
	    	$option_image=$this->option_image;
		    $option_weightage=$_POST['option_weightage'];
            $sth = $this->db_connection->prepare("INSERT INTO prediction_options(
			                                                             
																	prediction_id,
																	option_title,
																	option_image,	
																	option_weightage) 
			                                                         
																	VALUES(
																	:prediction_id,
																	:option_title,
																	:option_image,
																	:option_weightage
																	 )");
            $sth->bindValue(':prediction_id', $_GET['pred_id'], PDO::PARAM_INT);
            $sth->bindValue(':option_title', $option_title, PDO::PARAM_STR);
            $sth->bindValue(':option_image',$option_image, PDO::PARAM_STR);
            $sth->bindValue(':option_weightage',$option_weightage, PDO::PARAM_STR);
            $sth->execute();
			$sth->errorInfo();
			return $this->db_connection->lastInsertId();

        }else{
			$this->option_id=0;
		}
		
	}
	function update_option(){
    		$option_title=$_POST['option_title'];
	    	$option_image=$this->option_image;
		    $option_weightage=$_POST['option_weightage'];
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("UPDATE prediction_options SET option_title = :option_title ,
																	option_image = :option_image ,
																	option_weightage = :option_weightage
																 WHERE option_id = :option_id
																 	 ");
            $sth->bindValue(':option_title', $option_title, PDO::PARAM_STR);
            $sth->bindValue(':option_image',$option_image, PDO::PARAM_STR);
            $sth->bindValue(':option_weightage',$option_weightage, PDO::PARAM_STR);
            $sth->bindValue(':option_id',$this->option_id, PDO::PARAM_INT);
            $sth->execute();
			$sth->errorInfo();
			return $this->option_id;

		}else{
			$this->option_id='0';
			return false;
		}
		
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
                return false;
            }
        }
    }


}
class Investment
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;

    public $investment_id = "";

    public $prediction_id = "";

    public $option_id = "";

    public $invested_amount = "";

    public $winning_amount = "";

    public $no_of_tickets = "";

    public $ticket_ids = array();

    public $investor_id = "";

    public $invested_on = "";
	
	public $investment_found=false;

	
    public function __construct($id = NULL)
    {
		if(isset($id)){
            $this->investment_id= $this->check_existance($id);	
		}
    }

	function check_existance($id){
		$investment_id=intval($id);
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM prediction_investments WHERE investment_id = :investment_id");
            $sth->bindValue(':investment_id', $investment_id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->prediction_id)) {
				$this->investment_found=true;
				$this->prediction_id=$result_row->prediction_id;
				$this->option_id=$result_row->option_id;
				$this->invested_amount=$result_row->invested_amount;
				$this->winning_amount=$result_row->winning_amount;
				$this->no_of_tickets=$result_row->no_of_tickets;
				$this->ticket_ids=explode(',',$result_row->ticket_ids);
				return $result_row->investment_id;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
		
	}
	function check_from_user_id(){
		if(isset($_SESSION['user_id'])){
		$investor_id=intval($_SESSION['user_id']);
		}else{
			$investor_id=0;
		}
		if(isset($_GET['pred_id'])){
		$prediction_id=intval($_GET['pred_id']);
		}else{
			$prediction_id=0;
		}
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM prediction_investments WHERE prediction_id = :prediction_id AND investor_id=:investor_id");
            $sth->bindValue(':prediction_id', $prediction_id, PDO::PARAM_INT);
            $sth->bindValue(':investor_id', $investor_id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->prediction_id)) {
				$this->investment_found=true;
				$this->prediction_id=$result_row->prediction_id;
				$this->option_id=$result_row->option_id;
				$this->invested_amount=$result_row->invested_amount;
				$this->winning_amount=$result_row->winning_amount;
				$this->no_of_tickets=$result_row->no_of_tickets;
				$this->ticket_ids=explode(',',$result_row->ticket_ids);
				return $result_row->investment_id;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
		
	}
	function add_update_investment($choosen_option_id,$invested_points){
		$investor_id=$_SESSION['user_id'];
		$prediction=new Prediction( $_GET['pred_id']);
		if($this->databaseConnection() && $prediction->status!='closed'){
            $invested_on=date('j-M-Y G:i');
                 $sth = $this->db_connection->prepare("INSERT INTO prediction_investments
				                                      (option_id,prediction_id,invested_amount,invested_on,investor_id) 
													  VALUES
													  (:option_id,:prediction_id,:invested_amount,:invested_on,:investor_id)
");
                 $sth->bindValue(':option_id', $choosen_option_id, PDO::PARAM_INT);
                 $sth->bindValue(':invested_amount', $invested_points, PDO::PARAM_STR);
                 $sth->bindValue(':invested_on', $invested_on, PDO::PARAM_STR);
                 $sth->bindValue(':prediction_id', $_GET['pred_id'], PDO::PARAM_INT);
                 $sth->bindValue(':investor_id', $investor_id, PDO::PARAM_INT);
                 $sth->execute();
				 $last_inserted_id=$this->db_connection->lastInsertId();
			     $change=round($invested_points,0);
			     $remaining_points=$this->update_user_points($change);
				 return $last_inserted_id;
		}
	}
	
	public function update_user_points($points){
		$final_points=0;
		if($this->databaseConnection()){
			if(isset($_SESSION['user_id'])){
				$user_changed=new user($_SESSION['user_id']);
				$final_points=($user_changed->user_points)-$points;
                 $sth = $this->db_connection->prepare("UPDATE users SET user_points=:user_points
																						 WHERE user_id = :user_id");
                 $sth->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                 $sth->bindValue(':user_points',$final_points, PDO::PARAM_STR);
                 $sth->execute();
				 $_SESSION['user_points']=$final_points;
                 $sth = $this->db_connection->prepare("INSERT INTO  `points_history` (
                                                                                     `details_text` ,
																					 `points_changed` ,
																					 `change_type` ,
																					 `user_id` ,
																					 `target_type` ,
																					 `target_id`
																					 )
																					 
																					 VALUES (
																					 'Prediction investment updated',  
																					 :points_changed,  
																					 :change_type,  
																					 :user_id,  
																					 'prediction',  
																					 :prediction_id);
																					 ");
                 $sth->bindValue(':points_changed', $points, PDO::PARAM_STR);
					 $change_type='negative';
                 $sth->bindValue(':change_type', $change_type, PDO::PARAM_STR);
                 $sth->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                 $sth->bindValue(':prediction_id', $this->prediction_id, PDO::PARAM_INT);
                 $sth->execute();
				 
			}
		}
		return $final_points;
	}

   public function generate_ticket($no_of_tickets){
		$investor_id=$_SESSION['user_id'];
		$ticket_array=$this->ticket_ids;
		if(sizeof($ticket_array)<=1){
		if($this->databaseConnection()){
	        for($ticket_no=1;$ticket_no<=$no_of_tickets;$ticket_no++){
                 $sth = $this->db_connection->prepare("INSERT INTO lucky_draw_tickets(
				                                                   investment_id,investor_id,prediction_id) 
				                                                   VALUES(
																   :investment_id,:investor_id,:prediction_id)");
                 $sth->bindValue(':investment_id', $this->investment_id, PDO::PARAM_INT);
                 $sth->bindValue(':prediction_id', $this->prediction_id, PDO::PARAM_INT);
                 $sth->bindValue(':investor_id', $investor_id, PDO::PARAM_INT);
                 $sth->execute();
			     $ticket_id=$this->db_connection->lastInsertId();
				 $ticket_code='P'. $this->prediction_id.'I'.$ticket_id;
				 $ticket_array[]=$ticket_code;
                 $sth = $this->db_connection->prepare("UPDATE lucky_draw_tickets SET ticket_code=:ticket_code
				                                       WHERE ticket_id=:ticket_id");
                 $sth->bindValue(':ticket_code', $ticket_code, PDO::PARAM_STR);
                 $sth->bindValue(':ticket_id', $ticket_id, PDO::PARAM_INT);
                 $sth->execute();
	        }
			$ticket_ids=implode(',',$ticket_array);
			$sth = $this->db_connection->prepare("UPDATE prediction_investments
			                                                       SET ticket_ids=:ticket_ids
																   WHERE investment_id=:investment_id");
            $sth->bindValue(':investment_id', $this->investment_id, PDO::PARAM_INT);
            $sth->bindValue(':ticket_ids', $ticket_ids, PDO::PARAM_STR);
            $sth->execute();
			$this->ticket_ids=$ticket_array;

		}
		}
		return $ticket_array;
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
                return false;
            }
        }
    }


}
class Ticket_array
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;

    public $ticket_array = array();

    public $user_id = '';
	
	public $ticket_found=false;

	public $post_fun=false;
    public function __construct()
    {
		if(isset($_SESSION['user_id'])){
		    $this->user_id=$_SESSION['user_id'];
		   if(isset($_POST['add_tickets'])){
            $this->add_tickets($_POST['add_tickets']);	
			$this->post_fun=true;

		   }
		   $ticket_codes=$this->find_tickets();
		   $this->ticket_array=array();
		   foreach($ticket_codes as $ticket_code){
			   $ticket=new Ticket($ticket_code);
			   if($ticket->ticket_active==1){
			   $this->ticket_array[]=$ticket_code;
			   }
		   }
		}
    }

	function find_tickets(){
		$tickets_array=array();
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM lucky_draw_tickets WHERE investor_id = :investor_id AND active='1'");
            $sth->bindValue(':investor_id', $this->user_id, PDO::PARAM_INT);
            $sth->execute();
            while($result_row = $sth->fetchObject()){

            if (isset($result_row->ticket_id)) {
				$this->ticket_found=true;
				$created_on=$result_row->created_on;
				$created_on=explode(' ',$created_on);
				$c_days=date_to_days($created_on[0]);
				$c_hrs=explode('-',$created_on[1]);
				$c_total_secs=$c_days*24*3600+$c_hrs[0]*3600+$c_hrs[1]*60+$c_hrs[2];
				$now=date('d-m-Y H-i-s');
				$now=explode(' ',$now);
				$days=date_to_days($now[0]);
				$hrs=explode('-',$now[1]);
				$total_secs=$days*24*3600+$hrs[0]*3600+$hrs[1]*60+$hrs[2];
				$diff=$total_secs-$c_total_secs;
				if($diff<259200){$tickets_array[]=$result_row->ticket_code;}
				else{
                   $sa = $this->db_connection->prepare("UPDATE lucky_draw_tickets SET active = '0' WHERE ticket_id=:ticket_id");
                   $sa->bindValue(':ticket_id', $result_row->ticket_id, PDO::PARAM_INT);
                   $sa->execute();
				}
			}

		    }//end of while
		}else{
		}
		return $tickets_array;
	}
   public function add_tickets($no_of_tickets){
		$investor_id=$this->user_id;
		$ticket_ids='';
		$gen_tickets=array();
		$created_on=date('d-m-Y H-i-s');
		if($this->databaseConnection()){
	        for($ticket_no=1;$ticket_no<=$no_of_tickets;$ticket_no++){
                 $sth = $this->db_connection->prepare("INSERT INTO lucky_draw_tickets(
				                                                   investor_id,created_on,active) 
				                                                   VALUES(
																   :investor_id,:created_on,'1')");
                 $sth->bindValue(':created_on', $created_on, PDO::PARAM_STR);
                 $sth->bindValue(':investor_id', $investor_id, PDO::PARAM_INT);
                 $sth->execute();
			     $ticket_id=$this->db_connection->lastInsertId();
				 if($ticket_id<10){$ticket_code='00000'.$ticket_id;}
				 elseif($ticket_id<100){$ticket_code='0000'.$ticket_id;}
				 elseif($ticket_id<1000){$ticket_code='000'.$ticket_id;}
				 elseif($ticket_id<10000){$ticket_code='00'.$ticket_id;}
				 elseif($ticket_id<100000){$ticket_code='0'.$ticket_id;}
				 $ticket_code='LD'.$ticket_code;
				 $gen_tickets[]=$ticket_code;
                 $sth = $this->db_connection->prepare("UPDATE lucky_draw_tickets SET ticket_code=:ticket_code
				                                       WHERE ticket_id=:ticket_id");
                 $sth->bindValue(':ticket_code', $ticket_code, PDO::PARAM_STR);
                 $sth->bindValue(':ticket_id', $ticket_id, PDO::PARAM_INT);
                 $sth->execute();
	        }

			$user=new User($this->user_id);
			$total_points=intval(100*$_POST['add_tickets']);
	        $user->update_user_points('bought lucky draw tickets',$total_points,'negative','lucky_draw_ticket','');
		}
		return $gen_tickets;
   }

	public function update_user_points($points){
		$final_points=0;
		if($this->databaseConnection()){
			if(isset($_SESSION['user_id'])){
				$final_points=($_SESSION['user_points'])-$points;
                 $sth = $this->db_connection->prepare("UPDATE users SET user_points=:user_points
																						 WHERE user_id = :user_id");
                 $sth->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                 $sth->bindValue(':user_points',$final_points, PDO::PARAM_STR);
                 $sth->execute();
				 $_SESSION['user_points']=$final_points;
                 $sth = $this->db_connection->prepare("INSERT INTO  `points_history` (
                                                                                     `details_text` ,
																					 `points_changed` ,
																					 `change_type` ,
																					 `user_id` ,
																					 `target_type` ,
																					 `target_id`
																					 )
																					 
																					 VALUES (
																					 'Invested in lucky draw',  
																					 :points_changed,  
																					 :change_type,  
																					 :user_id,  
																					 'lucky_draw',  
																					 '0');
																					 ");
                 $sth->bindValue(':points_changed', $points, PDO::PARAM_STR);
				 if($points>0){
					 $change_type='positive';
				 }else{
					 $change_type='negative';
				 }
                 $sth->bindValue(':change_type', $change_type, PDO::PARAM_STR);
                 $sth->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                 $sth->execute();
				 
			}
		}
		return $final_points;
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
                return false;
            }
        }
    }


}
class Ticket
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;

    public $ticket_id = '';

    public $ticket_code = '';

    public $ticket_active = 0;

    public $user_id = '';
	
	public $ticket_found=false;

	
    public function __construct($ticket_code=NULL)
    {
		if(isset($ticket_code)){
			$this->ticket_code=$ticket_code;
			$this->verify_ticket_code();
		}
    }

	function verify_ticket_code(){
		$tickets_array=array();
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM lucky_draw_tickets WHERE ticket_code=:ticket_code");
            $sth->bindValue(':ticket_code', $this->ticket_code, PDO::PARAM_STR);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->ticket_id)) {
				$this->user_id=$result_row->investor_id;
				$this->ticket_found=true;
				$created_on=$result_row->created_on;
				$created_on=explode(' ',$created_on);
				$c_days=date_to_days($created_on[0]);
				$c_hrs=explode('-',$created_on[1]);
				$c_total_secs=$c_days*24*3600+$c_hrs[0]*3600+$c_hrs[1]*60+$c_hrs[2];
				$now=date('d-m-Y H-i-s');
				$now=explode(' ',$now);
				$days=date_to_days($now[0]);
				$hrs=explode('-',$now[1]);
				$total_secs=$days*24*3600+$hrs[0]*3600+$hrs[1]*60+$hrs[2];
				$diff=$total_secs-$c_total_secs;
				if($diff<259200){$this->ticket_active=1;}
				else{
                   $sa = $this->db_connection->prepare("UPDATE lucky_draw_tickets SET active = '0' WHERE ticket_id=:ticket_id");
                   $sa->bindValue(':ticket_id', $result_row->ticket_id, PDO::PARAM_INT);
                   $sa->execute();
				}
			}

		}
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
                return false;
            }
        }
    }


}
?>