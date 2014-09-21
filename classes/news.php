<?php
class news{
	public $news_id="";
	public $news_heading="";
	public $news_content="";
	public $news_image="";
	public $news_source="";
	public $news_parent_id=0;
	public $news_date_time="";
	public $news_posted_by="";
	public $news_importance="";
	public $number_of_views="";
	public $news_tags=array();
	public $news_exists=false;
	public $news_active=0;
	private $db_connection=null;
	


    public function __construct($id=NULL)
    {    
	    if(isset($id)){
		    $this->news_id=$id;
		    $this->check_news_existance($id);
			if(isset($_POST['edit_news'])){
			    if(isset($_FILES['news_image']) && $_FILES['news_image']['name']!='' && ($_FILES["news_image"]["type"] == "image/jpeg") ){
			    	$rand=round(rand()*10000,4);
    			    $this->news_image= preg_replace('/[^a-zA-Z0-9\']/','_',$_POST['news_heading']).$rand;    			    
					move_uploaded_file($_FILES["news_image"]["tmp_name"],"images/news/".$this->news_image.'.jpg');
			   	    $resize = new ResizeImage("images/news/".$this->news_image.'.jpg');
				    $resize->resizeTo(400, 300,'exact' );
				    $resize->saveImage("images/news/".$this->news_image.'_300.jpg');

			   	    $resize = new ResizeImage("images/news/".$this->news_image.'.jpg');
				    $resize->resizeTo(130, 100,'exact');
				    $resize->saveImage("images/news/".$this->news_image.'_100.jpg');
			    }			    
				
			}
		}else{
	    	if(isset($_POST['add_news'])){
		    	if($this->news_image==''){
			    	$rand=round(rand()*10000,4);
    			    $this->news_image= preg_replace('/[^a-zA-Z0-9\']/','_',$_POST['news_heading']).$rand;    			    
			    }
			    if(isset($_FILES['news_image']) && $_FILES['news_image']['name']!='' 
			    && ($_FILES["news_image"]["type"] == "image/jpeg") ){
			   	    move_uploaded_file($_FILES["news_image"]["tmp_name"],"images/news/".$this->news_image.'.jpg');
			   	$resize = new ResizeImage("images/news/".$this->news_image.'.jpg');
				$resize->resizeTo(400, 300,'exact' );
				$resize->saveImage("images/news/".$this->news_image.'_300.jpg');

			   	$resize = new ResizeImage("images/news/".$this->news_image.'.jpg');
				$resize->resizeTo(130, 100,'exact');
				$resize->saveImage("images/news/".$this->news_image.'_100.jpg');
			    }
			    
				$this->news_id=$this->add_news();
    		}
		}
	}

    public function find_news($id)
    {
		$this->news_id=$id;
		$this->check_news_existance($id);
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

	public function check_news_existance($id){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM political_news WHERE news_id = :news_id");
            $sth->bindValue(':news_id', $id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->news_id)) {
				$this->news_exists=true;
				$this->news_heading=$result_row->news_heading;
				$this->news_content=$result_row->news_content;
				$this->news_image=$result_row->news_image;
				$this->news_source=$result_row->news_source;
				$this->news_parent_id=$result_row->news_parent_id;
				$this->news_date_time=$result_row->news_date_time;
				$this->news_posted_by=$result_row->news_posted_by;
				$this->news_importance=$result_row->news_importance ;
				$this->news_tags=explode('::',$result_row->news_tags);				
				$this->number_of_views=$result_row->number_of_views;
				$this->news_active=$result_row->news_active;				
			}		
		}else{
			$this->news_id='0';
		}
	}

	public function child_news(){
		$child_array=array();
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM political_news WHERE news_parent_id = :news_parent_id");
            $sth->bindValue(':news_parent_id', $this->news_id, PDO::PARAM_INT);
            $sth->execute();
            while($result_row = $sth->fetchObject()){
				$child_array[]=$result_row->news_id;
			}
		}
		return $child_array;
	}

	public function add_news(){
		if($this->databaseConnection()){
			$date=date('j-M-Y G:i');
            $sth = $this->db_connection->prepare("INSERT INTO political_news(
			                                                             
																	news_heading,
																	news_content,
																	news_image,
																	news_source,
																	news_active,
																	news_parent_id,
																	news_date_time,
																	news_posted_by,
																	news_importance,
																	news_tags) 
			                                                         
																	VALUES(
			                                                             
																	:news_heading,
																	:news_content,
																	:news_image,
																	:news_source,
																	:news_active,
																	:news_parent_id,
																	:news_date_time,
																	:news_posted_by,
																	:news_importance,
																	:news_tags)");
            $sth->bindValue(':news_heading', $_POST['news_heading'], PDO::PARAM_STR);
            $sth->bindValue(':news_content', $_POST['news_content'], PDO::PARAM_STR);
            $sth->bindValue(':news_image', $this->news_image, PDO::PARAM_STR);
            $sth->bindValue(':news_source',$_POST['news_source'], PDO::PARAM_STR);
            $sth->bindValue(':news_active',0, PDO::PARAM_INT);
            $sth->bindValue(':news_parent_id',$_POST['news_parent_id'], PDO::PARAM_STR);
            $sth->bindValue(':news_date_time',date('j-M-Y G:i'), PDO::PARAM_STR);
            $sth->bindValue(':news_posted_by',$_SESSION['user_id'], PDO::PARAM_STR);
            $sth->bindValue(':news_importance',$_POST['news_importance'], PDO::PARAM_STR);
            $sth->bindValue(':news_tags','', PDO::PARAM_STR);
            $sth->execute();
			$sth->errorInfo();
			return $this->db_connection->lastInsertId();

		}else{
			$this->news_id='0';
			return false;
		}
	}
	public function update_news(){
		if($this->databaseConnection()){
			$date=date('j-M-Y G:i');
            $sth = $this->db_connection->prepare("UPDATE political_news SET
			                                                             
																	news_heading=:news_heading,
																	news_content=:news_content,
																	news_image=:news_image,
																	news_source=:news_source,
																	news_active=:news_active,
																	news_parent_id=:news_parent_id,
																	news_date_time=:news_date_time,
																	news_posted_by=:news_posted_by,
																	news_importance=:news_importance,
																	news_tags=:news_tags
																	
																	WHERE news_id=:news_id");
            $sth->bindValue(':news_heading', $_POST['news_heading'], PDO::PARAM_STR);
            $sth->bindValue(':news_content', $_POST['news_content'], PDO::PARAM_STR);
            $sth->bindValue(':news_image', $this->news_image, PDO::PARAM_STR);
            $sth->bindValue(':news_source',$_POST['news_source'], PDO::PARAM_STR);
            $sth->bindValue(':news_active',0, PDO::PARAM_INT);
            $sth->bindValue(':news_parent_id',$_POST['news_parent_id'], PDO::PARAM_STR);
            $sth->bindValue(':news_date_time',date('j-M-Y G:i'), PDO::PARAM_STR);
            $sth->bindValue(':news_posted_by',$_SESSION['user_id'], PDO::PARAM_STR);
            $sth->bindValue(':news_importance',$_POST['news_importance'], PDO::PARAM_STR);
            $sth->bindValue(':news_tags','', PDO::PARAM_STR);
            $sth->bindValue(':news_id',$this->news_id, PDO::PARAM_INT);
            $sth->execute();
			$sth->errorInfo();

		}else{
			$this->news_id='0';
		}
	}

	public function delete_news(){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("DELETE FROM newss WHERE news_id=':news_id'");
            $sth->bindValue(':news_id', $this->news_id, PDO::PARAM_INT);
            $sth->execute();
			$sth->errorInfo();
			return $this->db_connection->lastInsertId();

		}else{
			$this->news_id='0';
			return false;
		}
	}

}
?>