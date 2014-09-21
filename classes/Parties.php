<?php
class Alliance
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;

    public $alliance_id = "";

    public $alliance_name = "";

    public $alliance_short_name = "";

    public $alliance_logo = "";
	
	public $year_established='';
	
	public $alliance_found=false;
	
	


    public function __construct($id = NULL)
    {
		if(isset($id)){
            $this->alliance_id= $this->check_existance($id);	
		}
		
    }

	function check_existance($id){
		$alliance_id=intval($id);
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM party_alliance WHERE alliance_id = :alliance_id");
            $sth->bindValue(':alliance_id', $alliance_id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();
            if (isset($result_row->alliance_id)) {
				$this->alliance_found=true;
				$this->alliance_name=$result_row->alliance_name;
				$this->alliance_short_name=$result_row->alliance_short_name;
				$this->alliance_logo=$result_row->alliance_logo;
				$this->alliance_id=$result_row->alliance_id;
				return $result_row->alliance_id;
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


class Party
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;

    public $party_id = "";

    public $alliance_id = "";

    public $party_name = "";

    public $party_ideology = "";

    public $party_short_name = "";
	
	public $year_established="";

	public $no_of_followers=0; 

    public $party_logo = "";
	
	public $total_mla='';
	
	public $total_mp_loksabha='';

	public $total_mp_rajyasabha='';
	
	public $party_found=false;
	
	


    public function __construct($id = NULL)
    {
		if(isset($id)){
            $this->party_id= $this->check_existance($id);	
    		if(isset($_POST['party_name'])){
				if($this->party_logo==''){
				$rand=round(rand()*10000,4);
    			$this->party_logo=str_replace(' ','_',$_POST['party_name']).$rand;
    			$this->party_logo=str_replace("'",'_',$this->party_logo);
				}
				if(isset($_FILES['party_logo']) && $_FILES['party_logo']['name']!=''){
			   	move_uploaded_file($_FILES["party_logo"]["tmp_name"],"images/party/party/".$this->party_logo.'.jpg');
				
				$document_root=$_SERVER['DOCUMENT_ROOT'];
			   	$resize = new ResizeImage("images/party/party/".$this->party_logo.'.jpg');
				$resize->resizeTo(200, 200, 'exact');
				$resize->saveImage("images/party/party/".$this->party_logo.'_200.jpg');
				$resize->resizeTo(100, 100, 'exact');
				$resize->saveImage("images/party/party/".$this->party_logo.'_100.jpg');
				
				copy("images/party/party/".$this->party_logo.'_200.jpg',$document_root.MAIN_SITE_FOLDER."/images/party/party/".$this->party_logo.'_200.jpg');
				copy("images/party/party/".$this->party_logo.'_100.jpg',$document_root.MAIN_SITE_FOLDER."/images/party/party/".$this->party_logo.'_100.jpg');
				}
			   $this->party_id= $this->update_party();	
	    	}
			
            $this->party_id= $this->check_existance($id);	
		}else{
    		if(isset($_POST['party_name'])){
				if($this->party_logo==''){
				$rand=round(rand()*10000,4);
    			$this->party_logo=str_replace(' ','_',$_POST['party_name']).$rand;
    			$this->party_logo=str_replace("'",'_',$this->party_logo);
				}
				if(isset($_FILES['party_logo'])){
			   	move_uploaded_file($_FILES["party_logo"]["tmp_name"],"images/party/party/".$this->party_logo.'.jpg');
				
				$document_root=$_SERVER['DOCUMENT_ROOT'];
			   	$resize = new ResizeImage("images/party/party/".$this->party_logo.'.jpg');
				$resize->resizeTo(200, 200, 'exact');
				$resize->saveImage("images/party/party/".$this->party_logo.'_200.jpg');
				$resize->resizeTo(100, 100, 'exact');
				$resize->saveImage("images/party/party/".$this->party_logo.'_100.jpg');
				
				copy("images/party/party/".$this->party_logo.'_200.jpg',$document_root.MAIN_SITE_FOLDER."/images/party/party/".$this->party_logo.'_200.jpg');
				copy("images/party/party/".$this->party_logo.'_100.jpg',$document_root.MAIN_SITE_FOLDER."/images/party/party/".$this->party_logo.'_100.jpg');
				}
                $this->party_id= $this->add_party();	
	    	}
		}
		
    }

	function check_existance($id){
		$party_id=intval($id);
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM political_party WHERE party_id = :party_id");
            $sth->bindValue(':party_id', $party_id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->party_id)) {
				$this->party_found=true;
				$this->party_name=$result_row->party_name;
				$this->party_ideology=$result_row->party_ideology;
				$this->party_short_name=$result_row->party_short_name;
				$this->year_established=$result_row->party_established_on;
				$this->party_logo=$result_row->party_logo;
				$this->alliance_id=$result_row->alliance_id;
				$this->total_mla=$result_row->mla;
				$this->no_of_followers=$result_row->no_of_followers;
				$this->total_mp_loksabha=$result_row->mp_loksabha;
				$this->total_mp_rajyasabha=$result_row->mp_rajyasabha;
				return $result_row->party_id;
			}		
		}
		
	}
	
	function is_user_following($user_name){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM follow_table WHERE user_name=:user_name AND 
			                                                                      target_type=:target_type AND
																				  target_id=:target_id");
            $sth->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $sth->bindValue(':target_type', 'party', PDO::PARAM_STR);
            $sth->bindValue(':target_id', $this->party_id, PDO::PARAM_INT);
            $sth->execute();
			$result=$sth->fetchObject();
			if($result!==FALSE && ($result->follow_id)>0){
				return true;
			}else{
				return false;
			}
		}else{
			$this->party_id='0';
			return false;
		}
	}
	function follow_party($user_name){
		if($this->databaseConnection() && $this->is_user_following($user_name)==false){
            $sth = $this->db_connection->prepare("UPDATE political_party SET no_of_followers=no_of_followers+1
																	WHERE party_id=:party_id 
																	");
            $sth->bindValue(':party_id', $this->party_id, PDO::PARAM_INT);
            $sth->execute();
            $sth = $this->db_connection->prepare("INSERT INTO follow_table(user_name,target_type,target_id) 
			                                                       VALUES(:user_name,:target_type,:target_id)");
            $sth->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $sth->bindValue(':target_type', 'party', PDO::PARAM_STR);
            $sth->bindValue(':target_id', $this->party_id, PDO::PARAM_INT);
            $sth->execute();
		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	
	function unfollow_party($user_name){
		if($this->databaseConnection() && $this->is_user_following($user_name)!==false){
            $sth = $this->db_connection->prepare("UPDATE political_party SET no_of_followers=no_of_followers-1
																	WHERE party_id=:party_id 
																	");
            $sth->bindValue(':party_id', $this->party_id, PDO::PARAM_INT);
            $sth->execute();
            $sth = $this->db_connection->prepare("DELETE FROM follow_table WHERE user_name=:user_name AND 
			                                                                      target_type=:target_type AND
																				  target_id=:target_id");
            $sth->bindValue(':user_name',$user_name, PDO::PARAM_STR);
            $sth->bindValue(':target_type', 'party', PDO::PARAM_STR);
            $sth->bindValue(':target_id', $this->party_id, PDO::PARAM_INT);
            $sth->execute();
		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	function add_party(){
		$party_name=$_POST['party_name'];
		$party_ideology=$_POST['party_ideology'];
		$party_short_name=$_POST['party_short_name'];
		$alliance_id=$_POST['party_alliance'];
		$party_logo=$this->party_logo;
		$year_established=$_POST['year_established'];
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("INSERT INTO political_party(
			                                                        party_name,
																	party_short_name,
																	party_ideology,
																	alliance_id,
																	party_logo,
																	party_established_on) 
			                                                         
																	VALUES(
			                                                        :party_name,
																	:party_short_name,
																	:party_ideology,
																	:alliance_id,
																	:party_logo,
																	:party_established_on 
																	 )");
            $sth->bindValue(':party_name', $party_name, PDO::PARAM_STR);
            $sth->bindValue(':party_short_name', $party_short_name, PDO::PARAM_STR);
            $sth->bindValue(':party_ideology', $party_ideology, PDO::PARAM_STR);
            $sth->bindValue(':alliance_id', $alliance_id, PDO::PARAM_INT);
            $sth->bindValue(':party_logo',$party_logo, PDO::PARAM_STR);
            $sth->bindValue(':party_established_on',$year_established, PDO::PARAM_STR);
            $sth->execute();
			$sth->errorInfo();
			$ins= $this->db_connection->lastInsertId();
			return $ins;

		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	
	public function delete_party($party_id){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("DELETE FROM political_party 
																	WHERE party_id=:party_id 
																	");
            $sth->bindValue(':party_id', $party_id, PDO::PARAM_INT);
            $sth->execute();
		}
		
	}
	function update_party(){
		$party_name=$_POST['party_name'];
		$party_ideology=$_POST['party_ideology'];
		$party_short_name=$_POST['party_short_name'];
		$alliance_id=$_POST['party_alliance'];
		$year_established=$_POST['year_established'];
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("UPDATE political_party SET party_name=:party_name,
			                                                        party_short_name=:party_short_name,
																	alliance_id=:alliance_id,
																	party_ideology=:party_ideology,
																	party_logo=:party_logo,
																	party_established_on=:party_established_on
																	
																	WHERE party_id=:party_id 
																	");
            $sth->bindValue(':party_name', $party_name, PDO::PARAM_STR);
            $sth->bindValue(':party_short_name', $party_short_name, PDO::PARAM_STR);
            $sth->bindValue(':party_ideology', $party_ideology, PDO::PARAM_STR);
            $sth->bindValue(':alliance_id', $alliance_id, PDO::PARAM_INT);
            $sth->bindValue(':party_logo',$this->party_logo, PDO::PARAM_STR);
            $sth->bindValue(':party_id', $this->party_id, PDO::PARAM_INT);
            $sth->bindValue(':party_established_on',$year_established, PDO::PARAM_STR);
            $sth->execute();
			$sth->errorInfo();
			return $this->db_connection->lastInsertId();

		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	
	public function edit_mp_mla_details(){
		if($this->databaseConnection() && $this->party_found!=false){
			$party_id=$this->party_id;
            $update_total = $this->db_connection->prepare("UPDATE political_party SET mla=:total_mla,
			                                                                          mp_loksabha=:total_mp_loksabha,
																					  mp_rajyasabha=:total_mp_rajyasabha 
																					  WHERE party_id=:party_id");
            $update_total->bindValue(':total_mla', $_POST['mla_total'], PDO::PARAM_INT);
            $update_total->bindValue(':total_mp_loksabha', $_POST['mploksabha_total'], PDO::PARAM_INT);
            $update_total->bindValue(':total_mp_rajyasabha', $_POST['mprajyasabha_total'], PDO::PARAM_INT);
            $update_total->bindValue(':party_id', $party_id, PDO::PARAM_INT);
            $update_total->execute();
            $states = $this->db_connection->prepare("SELECT * FROM states WHERE 1");
            $states->execute();
			while($state = $states->fetchObject()){
				if(isset($_POST['exist_'.$state->state_id])){
				    $state_id=$state->state_id;
                    $no_of_mla=$_POST['mla_'.$state_id];
                    $no_of_mp_loksabha=$_POST['mploksabha_'.$state_id];
                    $no_of_mp_rajyasabha=$_POST['mprajyasabha_'.$state_id];
                    $sth = $this->db_connection->prepare("SELECT * FROM state_party WHERE party_id=:party_id  AND
																	state_id=:state_id 
																	");
                    $sth->bindValue(':party_id', $party_id, PDO::PARAM_INT);
                    $sth->bindValue(':state_id', $state_id, PDO::PARAM_INT);
                    $sth->execute();
                    $result_row = $sth->fetchObject();

                    if (isset($result_row->state_party_id)) {
				       //update
                         $sth = $this->db_connection->prepare("UPDATE state_party SET party_id=:party_id,
						                                                              state_id=:state_id,
																					  no_of_mla=:no_of_mla,
																					  no_of_mp_loksabha=:no_of_mp_loksabha,
																					  no_of_mp_rajyasabha=:no_of_mp_rajyasabha
																					  
																				  WHERE state_party_id=:state_party_id");
                         $sth->bindValue(':party_id', $party_id, PDO::PARAM_INT);
                         $sth->bindValue(':state_id', $state_id, PDO::PARAM_INT);
                         $sth->bindValue(':no_of_mla', $no_of_mla, PDO::PARAM_INT);
                         $sth->bindValue(':no_of_mp_loksabha', $no_of_mp_loksabha, PDO::PARAM_INT);
                         $sth->bindValue(':no_of_mp_rajyasabha', $no_of_mp_rajyasabha, PDO::PARAM_INT);
                         $sth->bindValue(':state_party_id', $result_row->state_party_id, PDO::PARAM_INT);
                         $sth->execute();
			        }else{
				       //insert
                         $sth = $this->db_connection->prepare("INSERT INTO state_party(party_id,
						                                                               state_id,
																					   no_of_mla,
																					   no_of_mp_loksabha,
																					   no_of_mp_rajyasabha)
						                                                        VALUES(:party_id,
																				       :state_id,
																					   :no_of_mla,
																					   :no_of_mp_loksabha,
																					   :no_of_mp_rajyasabha)");
                         $sth->bindValue(':party_id', $party_id, PDO::PARAM_INT);
                         $sth->bindValue(':state_id', $state_id, PDO::PARAM_INT);
                         $sth->bindValue(':no_of_mla', $no_of_mla, PDO::PARAM_INT);
                         $sth->bindValue(':no_of_mp_loksabha', $no_of_mp_loksabha, PDO::PARAM_INT);
                         $sth->bindValue(':no_of_mp_rajyasabha', $no_of_mp_rajyasabha, PDO::PARAM_INT);
                         $sth->execute();
				    } 

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
class Leader_profile
{
    /** @var object $db_connection The database connection */
    private $db_connection = null;
	
	public $profile_id="";

    public $party_id = "";

    public $leader_name = "";
	
	public $leader_type="";

    public $current_position = "";
	
	public $leader_picture="";

    public $date_of_birth = "";
	
	public $home_town='';
	
	public $no_of_followers=0; 
	
	public $active='';
	
	public $rank_in_party=0;
	
	public $profile_found=false;
	
	


    public function __construct($id = NULL)
    {
		if(isset($id)){
            $this->profile_id= $this->check_existance($id);	
    		if(isset($_POST['leader_name'])){
				if($this->leader_picture==''){
				   $rand=round(rand()*10000,4);
    		 	   $this->leader_picture=str_replace(' ','_',$_POST['leader_name']).$rand;
				}
				if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']["name"]!=''){
			   	    move_uploaded_file($_FILES['profile_picture']["tmp_name"],"images/party/profile/".$this->leader_picture.'.jpg');
				
				    $document_root=$_SERVER['DOCUMENT_ROOT'];
			   	    $resize = new ResizeImage("images/party/profile/".$this->leader_picture.'.jpg');
				    $resize->resizeTo(200, 200, 'exact');
				    $resize->saveImage("images/party/profile/".$this->leader_picture.'_200.jpg');
				    $resize->resizeTo(100, 100, 'exact');
				    $resize->saveImage("images/party/profile/".$this->leader_picture.'_100.jpg');
				    $resize->resizeTo(50, 50, 'exact');
				    $resize->saveImage("images/party/profile/".$this->leader_picture.'_50.jpg');
				
				    copy("images/party/profile/".$this->leader_picture.'_200.jpg',$document_root.MAIN_SITE_FOLDER."/images/party/profile/".$this->leader_picture.'_200.jpg');
					copy("images/party/profile/".$this->leader_picture.'_100.jpg',$document_root.MAIN_SITE_FOLDER."/images/party/profile/".$this->leader_picture.'_100.jpg');
					copy("images/party/profile/".$this->leader_picture.'_50.jpg',$document_root.MAIN_SITE_FOLDER."/images/party/profile/".$this->leader_picture.'_50.jpg');
				}
				$this->update_profile();
	    	}
			
            $this->profile_id= $this->check_existance($id);	
		}else{
    		if(isset($_POST['leader_name'])){
				if($this->leader_picture==''){
				   $rand=round(rand()*10000,4);
    		 	   $this->leader_picture=str_replace(' ','_',$_POST['leader_name']).$rand;
				}
				if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']["name"]!=''){
			   	    move_uploaded_file($_FILES['profile_picture']["tmp_name"],"images/party/profile/".$this->leader_picture.'.jpg');
				
				    $document_root=$_SERVER['DOCUMENT_ROOT'];
			   	    $resize = new ResizeImage("images/party/profile/".$this->leader_picture.'.jpg');
				    $resize->resizeTo(200, 200, 'exact');
				    $resize->saveImage("images/party/profile/".$this->leader_picture.'_200.jpg');
				    $resize->resizeTo(100, 100, 'exact');
				    $resize->saveImage("images/party/profile/".$this->leader_picture.'_100.jpg');
				    $resize->resizeTo(50, 50, 'exact');
				    $resize->saveImage("images/party/profile/".$this->leader_picture.'_50.jpg');
				
				    copy("images/party/profile/".$this->leader_picture.'_200.jpg',$document_root.MAIN_SITE_FOLDER."/images/party/profile/".$this->leader_picture.'_200.jpg');
					copy("images/party/profile/".$this->leader_picture.'_100.jpg',$document_root.MAIN_SITE_FOLDER."/images/party/profile/".$this->leader_picture.'_100.jpg');
					copy("images/party/profile/".$this->leader_picture.'_50.jpg',$document_root.MAIN_SITE_FOLDER."/images/party/profile/".$this->leader_picture.'_50.jpg');
				}
				$this->profile_id=$this->add_profile();
	    	}
		}
		
    }

	function check_existance($id){
		$leader_id=intval($id);
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM political_leader WHERE leader_id = :leader_id");
            $sth->bindValue(':leader_id', $leader_id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->leader_id)) {
				$this->profile_found=true;
				$this->party_id=$result_row->party_id;
				$this->leader_name=$result_row->leader_name;
				$this->leader_type=$result_row->leader_type;
				$this->date_of_birth=$result_row->date_of_birth;
				$this->leader_picture=$result_row->leader_picture;
				$this->current_position=$result_row->current_position;
				$this->no_of_followers=$result_row->no_of_followers;
				$this->rank_in_party=$result_row->rank_in_party;
				$this->home_town=$result_row->home_town;
				return $result_row->leader_id;
			}		
		}
		
	}
	
	function add_profile(){
		$leader_name=$_POST['leader_name'];
		$leader_type=$_POST['leader_type'];
		$current_position=$_POST['current_position'];
		$date_of_birth=$_POST['date_of_birth'];
		$home_town=$_POST['home_town'];
		$party_id=$_POST['party'];
		$leader_picture=$this->leader_picture;
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("INSERT INTO political_leader(
			                                                        party_id,
			                                                        leader_name,
			                                                        leader_type,
																	current_position,
																	leader_picture,
																	date_of_birth,
																	home_town,
																	rank_in_party) 
			                                                         
																	VALUES(
			                                                        :party_id,
			                                                        :leader_name,
			                                                        :leader_type,
																	:current_position,
																	:leader_picture,
																	:date_of_birth,
																	:home_town,
																	'10000')");
            $sth->bindValue(':party_id', $party_id, PDO::PARAM_INT);
            $sth->bindValue(':leader_name', $leader_name, PDO::PARAM_STR);
            $sth->bindValue(':leader_type', $leader_type, PDO::PARAM_STR);
            $sth->bindValue(':current_position', $current_position, PDO::PARAM_STR);
            $sth->bindValue(':leader_picture',$leader_picture, PDO::PARAM_STR);
            $sth->bindValue(':date_of_birth',$date_of_birth, PDO::PARAM_STR);
            $sth->bindValue(':home_town',$home_town, PDO::PARAM_STR);
            $sth->execute();
			$sth->errorInfo();
			$ins= $this->db_connection->lastInsertId();
			return $ins;

		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	function update_profile(){
		$leader_name=$_POST['leader_name'];
		$leader_type=$_POST['leader_type'];
		$current_position=$_POST['current_position'];
		$date_of_birth=$_POST['date_of_birth'];
		$home_town=$_POST['home_town'];
		$party_id=$_POST['party'];
		$leader_picture=$this->leader_picture;
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("UPDATE political_leader SET leader_name=:leader_name,
			                                                        current_position=:current_position,
																	leader_type=:leader_type,
																	date_of_birth=:date_of_birth,
																	home_town=:home_town,
																	party_id=:party_id,
																	leader_picture=:leader_picture
																	
																	WHERE leader_id=:leader_id 
																	");
            $sth->bindValue(':party_id', $party_id, PDO::PARAM_INT);
            $sth->bindValue(':leader_name', $leader_name, PDO::PARAM_STR);
            $sth->bindValue(':leader_type', $leader_type, PDO::PARAM_STR);
            $sth->bindValue(':current_position', $current_position, PDO::PARAM_STR);
            $sth->bindValue(':leader_picture',$leader_picture, PDO::PARAM_STR);
            $sth->bindValue(':date_of_birth',$date_of_birth, PDO::PARAM_STR);
            $sth->bindValue(':home_town',$home_town, PDO::PARAM_STR);
            $sth->bindValue(':leader_id', $this->profile_id, PDO::PARAM_INT);
            $sth->execute();

		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	function is_user_following($user_name){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM follow_table WHERE user_name=:user_name AND 
			                                                                      target_type=:target_type AND
																				  target_id=:target_id");
            $sth->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $sth->bindValue(':target_type', 'leader', PDO::PARAM_STR);
            $sth->bindValue(':target_id', $this->profile_id, PDO::PARAM_INT);
            $sth->execute();
			$result=$sth->fetchObject();
			if($result!==FALSE && ($result->follow_id)>0){
				return true;
			}else{
				return false;
			}
		}else{
			$this->party_id='0';
			return false;
		}
	}
	function is_user_given_review($user_name){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM leader_review WHERE user_name=:user_name AND
			                                                                      leader_id=:leader_id");
            $sth->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $sth->bindValue(':leader_id', $this->profile_id, PDO::PARAM_INT);
            $sth->execute();
			$result=$sth->fetchObject();
			if($result!==FALSE && ($result->review_id)>0){
				return true;
			}else{
				return false;
			}
		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	function follow_user($user_name){
		if($this->databaseConnection() && $this->is_user_following($user_name)==false){
			$this->no_of_followers=$this->no_of_followers+1;
            $sth = $this->db_connection->prepare("UPDATE political_leader SET no_of_followers=:no_of_followers
																	WHERE leader_id=:leader_id 
																	");
            $sth->bindValue(':no_of_followers', $this->no_of_followers, PDO::PARAM_INT);
            $sth->bindValue(':leader_id', $this->profile_id, PDO::PARAM_INT);
            $sth->execute();
            $sth = $this->db_connection->prepare("INSERT INTO follow_table(user_name,target_type,target_id) 
			                                                       VALUES(:user_name,:target_type,:target_id)");
            $sth->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $sth->bindValue(':target_type', 'leader', PDO::PARAM_STR);
            $sth->bindValue(':target_id', $this->profile_id, PDO::PARAM_INT);
            $sth->execute();
		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	
	function find_rating(){
		$rating_array=array('industrial'=>0,
		                    'educational'=>0,
							'infrastructure'=>0,
							'healthcare'=>0,
							'agricultural'=>0,
							'total'=>0);
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM leader_review WHERE leader_id = :leader_id");
            $sth->bindValue(':leader_id', $this->profile_id, PDO::PARAM_INT);
            $sth->execute();
            while($result_row = $sth->fetchObject()){
				
				$rating_array['industrial']=$rating_array['industrial']*$rating_array['total']+$result_row->industrial_point;
				$rating_array['educational']=$rating_array['educational']*$rating_array['total']+$result_row->educational_point;
				$rating_array['infrastructure']=$rating_array['infrastructure']*$rating_array['total']+$result_row->infrastructure_point;
				$rating_array['healthcare']=$rating_array['healthcare']*$rating_array['total']+$result_row->healthcare_point;
				$rating_array['agricultural']=$rating_array['agricultural']*$rating_array['total']+$result_row->agricultural_point;
				$rating_array['total']++;
				$rating_array['industrial']=$rating_array['industrial']/$rating_array['total'];				
				$rating_array['educational']=$rating_array['educational']/$rating_array['total'];				
				$rating_array['infrastructure']=$rating_array['infrastructure']/$rating_array['total'];				
				$rating_array['healthcare']=$rating_array['healthcare']/$rating_array['total'];				
				$rating_array['agricultural']=$rating_array['agricultural']/$rating_array['total'];				
			}
		}
		
		return $rating_array;
	}
	
	function add_review($user_name,$industrial,$educational,$infrastructure,$healthcare,$agricultural){
		if($this->databaseConnection() && $this->is_user_given_review($user_name)==false){
			$this->no_of_followers=$this->no_of_followers+1;
            $sth = $this->db_connection->prepare("INSERT INTO leader_review(user_name,leader_id,industrial_point,educational_point,infrastructure_point,healthcare_point,agricultural_point) 
			                                                       VALUES(:user_name, :leader_id, :industrial_point, :educational_point, :infrastructure_point, :healthcare_point,:agricultural_point)");
            $sth->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $sth->bindValue(':leader_id', $this->profile_id, PDO::PARAM_INT);
            $sth->bindValue(':industrial_point', $industrial, PDO::PARAM_STR);
            $sth->bindValue(':educational_point', $educational, PDO::PARAM_STR);
            $sth->bindValue(':infrastructure_point', $infrastructure, PDO::PARAM_STR);
            $sth->bindValue(':healthcare_point', $healthcare, PDO::PARAM_STR);
            $sth->bindValue(':agricultural_point', $agricultural, PDO::PARAM_STR);
            $sth->execute();

/*
            $sth = $this->db_connection->prepare("UPDATE political_leader SET no_of_followers=:no_of_followers
																	WHERE leader_id=:leader_id 
																	");
            $sth->bindValue(':no_of_followers', $this->no_of_followers, PDO::PARAM_INT);
            $sth->bindValue(':leader_id', $this->profile_id, PDO::PARAM_INT);
            $sth->execute();*/
		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	function unfollow_user($user_name){
		if($this->databaseConnection() && $this->is_user_following($user_name)!==false){
			$this->no_of_followers=$this->no_of_followers-1;
            $sth = $this->db_connection->prepare("UPDATE political_leader SET no_of_followers=:no_of_followers
																	WHERE leader_id=:leader_id 
																	");
            $sth->bindValue(':no_of_followers', $this->no_of_followers, PDO::PARAM_INT);
            $sth->bindValue(':leader_id', $this->profile_id, PDO::PARAM_INT);
            $sth->execute();
            $sth = $this->db_connection->prepare("DELETE FROM follow_table WHERE user_name=:user_name AND 
			                                                                      target_type=:target_type AND
																				  target_id=:target_id");
            $sth->bindValue(':user_name',$user_name, PDO::PARAM_STR);
            $sth->bindValue(':target_type', 'leader', PDO::PARAM_STR);
            $sth->bindValue(':target_id', $this->profile_id, PDO::PARAM_INT);
            $sth->execute();
		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	function add_career(){
		$career_term=$_POST['political_term'];
		$career_position=$_POST['political_position'];
		$career_type=$_POST['career_type'];
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("INSERT INTO political_career_details(
			                                                        leader_id,
			                                                        career_term,
			                                                        career_position,
																	career_type) 
			                                                         
																	VALUES(
			                                                        :leader_id,
			                                                        :career_term,
			                                                        :career_position,
																	:career_type)");
            $sth->bindValue(':leader_id', $this->profile_id, PDO::PARAM_INT);
            $sth->bindValue(':career_term', $career_term, PDO::PARAM_STR);
            $sth->bindValue(':career_position', $career_position, PDO::PARAM_STR);
            $sth->bindValue(':career_type', $career_type, PDO::PARAM_STR);
            $sth->execute();
			$ins= $this->db_connection->lastInsertId();
			return $ins;

		}else{
			$this->party_id='0';
			return false;
		}
		
	}
	function delete_career($career_id){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("DELETE FROM political_career_details 
																	WHERE career_id=:career_id 
																	");
            $sth->bindValue(':career_id', $career_id, PDO::PARAM_INT);
            $sth->execute();
		}
		
	}
	
	public function get_profile_by_rank($rank,$party_id){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM political_leader WHERE party_id = :party_id AND rank_in_party=:rank");
            $sth->bindValue(':party_id', $party_id, PDO::PARAM_INT);
            $sth->bindValue(':rank', $rank, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->leader_id)) {
				$this->profile_found=true;
				$this->profile_id=$result_row->leader_id;
				$this->party_id=$result_row->party_id;
				$this->leader_name=$result_row->leader_name;
				$this->leader_type=$result_row->leader_type;
				$this->date_of_birth=$result_row->date_of_birth;
				$this->leader_picture=$result_row->leader_picture;
				$this->current_position=$result_row->current_position;
				$this->no_of_followers=$result_row->no_of_followers;
				$this->rank_in_party=$result_row->rank_in_party;
				$this->home_town=$result_row->home_town;
				return $result_row->leader_id;
			}		
		}
		
	}

	function update_rank($rank){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("UPDATE political_leader SET rank_in_party=:rank_in_party
																	WHERE leader_id=:leader_id 
																	");
            $sth->bindValue(':rank_in_party', $rank, PDO::PARAM_INT);
            $sth->bindValue(':leader_id', $this->profile_id, PDO::PARAM_INT);
            $sth->execute();

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