<?php  
	/*
		Plugin Name: My DB
		Author: Muhammad Haris
		Version: 1.0
	*/
	class My_DB{
		protected $host = DB_HOST;
		protected $user = DB_USER;
		protected $pass = '';
		protected $db = DB_NAME;
		protected $con;
		function __construct(){
			$this->con = new mysqli($this->host, $this->user, $this->pass, $this->db);
			add_action('admin_menu', [$this, 'add_in_menu']);
			add_action('admin_enqueue_scripts', [$this, 'child_enqueue_styles']);
			add_action( 'wp_ajax_show_rec', [$this, 'show_rec'] );
			add_action( 'wp_ajax_update_rec', [$this, 'update_rec'] );
			add_action( 'wp_ajax_update', [$this, 'update'] );
			add_action( 'wp_ajax_delete', [$this, 'delete'] );
		}
		public function add_in_menu(){
			add_menu_page('My DB', 'My DB', 'administrator', 'my-db', [$this, 'include_template'] , 'dashicons-list-view' );
		}
		public function include_template() {
			include plugin_dir_path(__FILE__).'templates/db.php';
		} 
		public function child_enqueue_styles() {
			wp_enqueue_style('my-db-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
			wp_enqueue_style('my-db-style', plugins_url('/my-db/assets/css/style.css') );
			wp_enqueue_script('my-db-bootstrapjs', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
			wp_enqueue_script('my-db-scripts', plugins_url('/my-db/assets/js/scripts.js'));
		}
		public function show_rec() {
			$rs = $this->con->query('SELECT * FROM '.$_POST['tblName'].' WHERE '.$_POST['col'].' = '.$_POST['id']);
			foreach ($rs as $key => $r)
				$records[] = $this->_htmlspecialchars($r);
			echo(json_encode(['records' => $records]));
			exit;
		}
		public function update_rec() {
			$col = $_POST;
			$q = 'UPDATE '.$_POST['tblName'];
			unset($col['action'], $col['id'], $col['col'], $col['tblName']);
			$count = 0;

			foreach ($col as $key => $c) {
				$count++;
				if ($count == 1) 
					$q .= ' SET ';	
				$q .= $key.' = ';
				if($c['type'] == 'MD5')
					$q .= '"'.md5($c['value']).'", ';
				else
					$q .= count($col) == $count ? '"'.$c['value'].'" ' : '"'.$c['value'].'", ';
				
			}
			$q .= ' WHERE '.$_POST['col'].' = '.$_POST['id'];
			if($this->con->query($q))
				echo 'success';
			else
				echo 'error';
			exit;
		}
		public function update(){
			if($this->con->query( 'UPDATE '.$_POST['tblName'].' SET '.$_POST['col'].' = "'.$_POST['val'].'" WHERE '.$_POST['col'].' =  "'.$_POST['valO'].'" AND '.$_POST['recIDKey'].' = '.$_POST['recID'])){
				echo 'Update Successfully!';
			}
			else{
				echo mysqli_error($con);
			}
			exit;
		}
		public function delete(){
			if($this->con->query( 'DELETE FROM '.$_POST['tblName'].' WHERE '.$_POST['col'].' =  "'.$_POST['id'].'"')){
				echo 'Delete Successfully!';
			}
			else{
				echo mysqli_error($con);
			}
			exit;
		}
		protected function _htmlspecialchars($r){
			return array_map(function($val){
				return htmlspecialchars($val); 

			}, $r);
		}
	}	
	new My_DB;
?>