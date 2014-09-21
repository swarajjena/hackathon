<?php
class Blog{
	public $blog_id=0;
	public $blog_heading="";
	public $blog_content="";
	public $blog_short_details="";
	public $blog_categories=array();
	public $posted_by="";
	public $posted_on="";
	public $blog_image="";
	public $blog_approved="";
	public $blog_likes="";
	public $blog_visits="";
	public $solutions=array();
	public $blog_exists=false;
	private $db_connection=null;
	


    public function __construct($id = NULL)
    {
		if(isset($id)){
		    $this->blog_id=$id;
		    $this->check_blog_existance($id);
    	    if(isset($_POST['blog_title'])){
		    	if($this->blog_image==''){
			    	$rand=round(rand()*10000,4);
    			    $this->blog_image= preg_replace('/[^a-zA-Z0-9\']/','_',$_POST['blog_title']).$rand;
    			    
			    }
			    if(isset($_FILES['blog_image_new']) && $_FILES['blog_image_new']['name']!='' 
			    && ($_FILES["blog_image_new"]["type"] == "image/jpeg") ){
			   	    move_uploaded_file($_FILES["blog_image_new"]["tmp_name"],"images/blog/".$this->blog_image.'.jpg');
			   	$resize = new ResizeImage("images/blog/".$this->blog_image.'.jpg');
				$resize->resizeTo(400, 400,maxHeight);
				$resize->saveImage("images/blog/".$this->blog_image.'_300.jpg');
				
				    $document_root=$_SERVER['DOCUMENT_ROOT'];
				
				    copy("images/blog/".$this->blog_image.'.jpg',$document_root.MAIN_SITE_FOLDER."/images/blog/".$this->blog_image.'.jpg');
				    copy("images/blog/".$this->blog_image.'_300.jpg',$document_root.MAIN_SITE_FOLDER."/images/blog/".$this->blog_image.'_300.jpg');
			     }
			$string = $_POST['blog_content_new'];
            $fp = fopen("blog/".$this->blog_image.".php", "w");

            fwrite($fp, $string);

            fclose($fp);
			
				 
			     $this->update_blog();	
	        }
			
		}else{
    	    if(isset($_POST['blog_title'])){
		    	if($this->blog_image==''){
			    	$rand=round(rand()*10000,4);
    			    $this->blog_image= preg_replace('/[^a-zA-Z0-9\']/','_',$_POST['blog_title']).$rand;
			    }
			    if(isset($_FILES['blog_image_new']) && $_FILES['blog_image_new']['name']!='' 
			    && ($_FILES["blog_image_new"]["type"] == "image/jpeg") ){
			   	    move_uploaded_file($_FILES["blog_image_new"]["tmp_name"],"images/blog/".$this->blog_image.'.jpg');
			   	$resize = new ResizeImage("images/blog/".$this->blog_image.'.jpg');
				$resize->resizeTo(400, 400,maxHeight);
				$resize->saveImage("images/blog/".$this->blog_image.'_300.jpg');
				
				    $document_root=$_SERVER['DOCUMENT_ROOT'];
				
				    copy("images/blog/".$this->blog_image.'.jpg',$document_root.MAIN_SITE_FOLDER."/images/blog/".$this->blog_image.'.jpg');
				    copy("images/blog/".$this->blog_image.'_300.jpg',$document_root.MAIN_SITE_FOLDER."/images/blog/".$this->blog_image.'_300.jpg');
			     }
			$string = $_POST['blog_content_new'];
            $fp = fopen("blog/".$this->blog_image.".php", "w");

            fwrite($fp, $string);

            fclose($fp);
			
			    $this->blog_id= $this->add_blog();	
	    	}
			
		}
		    $this->check_blog_existance($this->blog_id);
    }


    public function find_blog($id)
    {
		$this->blog_id=$id;
		$this->check_blog_existance($id);
;
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

	public function check_blog_existance($id){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM blogs WHERE blog_id = :blog_id");
            $sth->bindValue(':blog_id', $id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->blog_id)) {
				$this->blog_exists=true;
				$this->blog_heading=$result_row->blog_heading;
				$this->blog_content=$result_row->blog_content;
				$this->blog_short_details=$result_row->blog_short_details;
				$this->posted_by=$result_row->blog_writer_id;
				$this->posted_on=$result_row->posted_on;
				$this->blog_image=$result_row->blog_images;				
				$this->blog_approved=$result_row->approved;				
				$this->blog_categories=explode(':',$result_row->blog_categories);				
			}		
		}else{
			$this->blog_id='0';
		}
	}

	public function add_blog(){
		if($this->databaseConnection()){
			$date=date('j-M-Y G:i');
            $sth = $this->db_connection->prepare("INSERT INTO blogs(
																	blog_heading,
																	blog_content,
																	blog_categories,
																	blog_writer_id,
																	blog_images) 
			                                                         
																	VALUES(
																	:blog_heading,
																	:blog_content,
																	:blog_categories,
																	:blog_writer_id,
																	:blog_images) 
																	");
            $sth->bindValue(':blog_heading', $_POST['blog_title'], PDO::PARAM_STR);
            $sth->bindValue(':blog_content', $_POST['blog_content_new'], PDO::PARAM_STR);
			if(isset($_POST['blog_categories'])){
            $sth->bindValue(':blog_categories',$_POST['blog_categories'], PDO::PARAM_STR);
			}else{
            $sth->bindValue(':blog_categories','', PDO::PARAM_STR);
			}
            $sth->bindValue(':blog_images',$this->blog_image, PDO::PARAM_STR);
            $sth->bindValue(':blog_writer_id',$_SESSION['user_id'], PDO::PARAM_INT);
            $sth->execute();
			$sth->errorInfo();
			return $this->db_connection->lastInsertId();

		}else{
			$this->blog_id='0';
			return false;
		}
	}
	public function approve_blog(){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("UPDATE blogs SET
																	approved=:approved,
																	approved_by_id=:approved_by
																WHERE
																    blog_id=:blog_id
																	");
            $sth->bindValue(':approved',$this->blog_approved, PDO::PARAM_INT);
            $sth->bindValue(':approved_by',$_SESSION['user_id'], PDO::PARAM_INT);
            $sth->bindValue(':blog_id',$this->blog_id, PDO::PARAM_INT);
            $sth->execute();$sth->errorInfo();

		}	
	}
	public function delete_blog(){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("DELETE FROM blogs WHERE
																    blog_id=:blog_id
																	");
            $sth->bindValue(':blog_id',$this->blog_id, PDO::PARAM_INT);
            $sth->execute();$sth->errorInfo();

		}	
	}
	public function update_blog(){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("UPDATE blogs SET
																	blog_heading=:blog_heading,
																	blog_content=:blog_content,
																	blog_categories=:blog_categories,
																    blog_images=:blog_image
																	
																WHERE
																    blog_id=:blog_id
																	");
            $sth->bindValue(':blog_heading', $_POST['blog_title'], PDO::PARAM_STR);
            $sth->bindValue(':blog_content', $_POST['blog_content_new'], PDO::PARAM_STR);
			if(isset($_POST['blog_categories'])){
            $sth->bindValue(':blog_categories',$_POST['blog_categories'], PDO::PARAM_STR);
			}else{
            $sth->bindValue(':blog_categories','', PDO::PARAM_STR);
			}
			$sth->bindValue(':blog_image',$this->blog_image, PDO::PARAM_STR);
            $sth->bindValue(':blog_id',$this->blog_id, PDO::PARAM_INT);
            $sth->execute();$sth->errorInfo();

		}else{
			$this->blog_id='0';
			return false;
		}
	}

	public function add_comment(){
		if($this->databaseConnection()){
			$date=date('j-M-Y G:i');
            $sth = $this->db_connection->prepare("INSERT INTO solutions_blogs(
			                                                             
																	blog_id,
																	solution,
																	posted_by,
																	posted_on) 
			                                                         
																	VALUES(
																	:blog_id,
																	:solution,
																	:posted_by,
																	:posted_on
																	 )");
            $sth->bindValue(':blog_id', $this->blog_id, PDO::PARAM_INT);
            $sth->bindValue(':solution', $_POST['add_details'], PDO::PARAM_STR);
            $sth->bindValue(':posted_by',$_SESSION['user_name'], PDO::PARAM_STR);
            $sth->bindValue(':posted_on', $date, PDO::PARAM_STR);
            $sth->execute();
			$result_row=array();
			foreach($sth as $row){
            $result_row[]=$row['solution_id'];
			}
			return $result_row;

		}else{
			$this->blog_id='0';
			return false;
		}
	}

}
class comment{
	public $solution_id="";
	public $comment_id="";
	public $solution_name="";
	public $comment_details="";
	public $posted_by="";
	public $posted_on="";
	public $likes=0;
	public $dislikes=0;
	public $comment_exists=false;
	private $db_connection=null;

    public function __construct($id)
    {
		$this->comment_id=$id;
		$this->check_comment_existance($id);
		
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

	public function check_comment_existance($id){
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM comments_solution WHERE comment_id = :comment_id");
            $sth->bindValue(':comment_id', $id, PDO::PARAM_INT);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->comment_id)) {
				$this->comment_exists=true;
				$this->comment_details=$result_row->comment;
				$this->posted_by=$result_row->posted_by;
				$this->posted_on=$result_row->posted_on;
				$this->likes=$result_row->likes;				
				$this->dislikes=$result_row->dislikes;								
				
			}		
		}else{
			$this->comment_id='0';
		}
	}

	public function check_user_liked_disliked($user_name,$like_dislike){
		$liked=0;
		if($this->databaseConnection()){
            $sth = $this->db_connection->prepare("SELECT * FROM likes_dislikes WHERE user_name = :user_name AND for_l='comments' AND target_id=:target_id AND like_dislike=:like_dislike");
            $sth->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $sth->bindValue(':target_id', $this->comment_id, PDO::PARAM_INT);
            $sth->bindValue(':like_dislike', $like_dislike, PDO::PARAM_STR);
            $sth->execute();
            $result_row = $sth->fetchObject();

            if (isset($result_row->like_id)) {
				$liked=1;
			}		
		}
		return $liked;		
	}

	public function add_like_dislike($like_dislike){
		$liked=0;
		if($this->databaseConnection()){
     		$user=$_SESSION['user_name'];
			$liked=$this->check_user_liked_disliked($user,$like_dislike);
			if($liked!=1){
            $sth = $this->db_connection->prepare("INSERT INTO likes_dislikes(
			                                                             
																	like_dislike,
																	for_l,
																	target_id,
																	user_name) 
			                                                         
																	VALUES(
																	:like_dislike,
																	:for_l,
																	:target_id,
																	:user_name
																	 )");
																			 
            $sth->bindValue(':like_dislike', $like_dislike, PDO::PARAM_STR);
            $sth->bindValue(':for_l', 'comments', PDO::PARAM_STR);
            $sth->bindValue(':target_id', $this->comment_id, PDO::PARAM_INT);
            $sth->bindValue(':user_name',$_SESSION['user_name'], PDO::PARAM_STR);
            
            if ($sth->execute()) {
				$liked=1;
				$prev=0;
				if($like_dislike=='like'){
					$prev=$this->likes;
					$prev++;
                    $sth = $this->db_connection->prepare("UPDATE comments_solution SET likes=:likes WHERE comment_id=:comment_id");
                    $sth->bindValue(':likes',$prev, PDO::PARAM_INT);
                    $sth->bindValue(':comment_id', $this->comment_id, PDO::PARAM_INT);
                    $sth->execute();
				}elseif($like_dislike=='dislike'){
					$prev=$this->dislikes;
					$prev++;
                    $sth = $this->db_connection->prepare("UPDATE comments_solution SET dislikes=:dislikes WHERE comment_id=:comment_id");
                    $sth->bindValue(':dislikes',$prev, PDO::PARAM_INT);
                    $sth->bindValue(':comment_id', $this->comment_id, PDO::PARAM_INT);
                    $sth->execute();
				}
			}		
			}
		}
		return $liked;		
	}


}
?>