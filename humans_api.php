
<?php

	include 'humans_core.php';
	
	class HumansAPI {

		
		public function __construct(){
			$this->httpInputs();			
			$this->dbConnect();				
		}
		
		/* Database Connection */
		private function dbConnect(){
			$this->db = mysql_connect("localhost", "root", "");
			mysql_select_db("humans_api",$this->db);
			$this->tbl = "hmn_users";
		}

		public function get_request_method(){
			return $_SERVER['REQUEST_METHOD'];
		}
		
		private function httpInputs(){
			switch($this->get_request_method()){
				case "POST":
					$this->_request = $this->cleanInputs($_POST);
					break;
				case "GET":
				case "DELETE":
					$this->_request = $this->cleanInputs($_GET);
					break;
				case "PUT":
					parse_str(file_get_contents("php://input"),$this->_request);
					$this->_request = $this->cleanInputs($this->_request);
					break;
				default:
					$this->response('',406);
					break;
			}
		}		
		
		private function cleanInputs($data){
			$clean_input = array();
			if(is_array($data)){
				foreach($data as $key => $val){
					$clean_input[$key] = $this->cleanInputs($val);
				}
			}else{
				if(get_magic_quotes_gpc()){
					$data = trim(stripslashes($data));
				}
				$data = strip_tags($data);
				$clean_input = trim($data);
			}
			return $clean_input;
		}

		
		public function processRequest(){
						
			if($_REQUEST != NULL && $_REQUEST != "") {

				$function = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
				if((int)method_exists($this,$function) > 0){
					$this->$function(); 
				}else Core::getMessage("noRoute");						
				
			}else Core::getMessage("noRoute");						
				
		}

		
		private function humans(){
			self::gethuman(); 	
		}	

		private function human(){	

			if($this->get_request_method() == "GET"){
				self::gethuman(); 		
			}

			if($this->get_request_method() == "POST"){
				self::insertHuman();
			}

			if($this->get_request_method() == "PUT"){
				self::updateHuman();
			}

			if($this->get_request_method() == "DELETE"){
				self::deleteHuman();
			}
		} 


		private function gethuman(){
		
			$id = (!empty($this->_request['id'])) ? $this->_request['id'] : "";
			$condition = (!empty($id)) ? " WHERE id=".$id : ""; 

			$sql = mysql_query("SELECT id, first_name, last_name FROM $this->tbl $condition", $this->db);
			if(mysql_num_rows($sql) > 0){
				$result = array();
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
					$result[] = $rlt;
				}
				Core::response(Core::json($result));
			}else Core::getMessage("noHuman");	
		}


		private function insertHuman(){

			$firstName = (!empty($this->_request['first_name'])) ? $this->_request['first_name'] : "" ;		
			$lastName = (!empty($this->_request['last_name'])) ? $this->_request['last_name'] : "";
			
			if(!empty($firstName) AND !empty($lastName)){
		
				$sql = mysql_query("INSERT INTO $this->tbl (`id`, `first_name`, `last_name`) VALUES ('', '$firstName', '$lastName')");
				$result = ($sql == true) ? Core::getMessage("insrt") : Core::getMessage("default");
				Core::response(Core::json($result));

			}else Core::getMessage("default");					
		}

		private function updateHuman(){

			$firstName = (!empty($this->_request['first_name'])) ? $this->_request['first_name'] : "" ;		
			$lastName = (!empty($this->_request['last_name'])) ? $this->_request['last_name'] : "";
			
				if (!empty($this->_request['id'])) {

					$id = (!empty($this->_request['id'])) ? $this->_request['id'] : "";
					$condition = (!empty($id)) ? " WHERE id=".$id : ""; 

					$query = "UPDATE $this->tbl SET ";
					$arrayUpd = array('first_name' => $firstName, 'last_name' => $lastName);

					foreach ($arrayUpd as $key => $value) {
						if (!empty($value)) {
							$query .= $key ." = '". $value ."'," ;
						}					
					}
					
					$query = rtrim($query, ',');
					$query .= $condition;
		
					$sql = mysql_query($query);
					$result = ($sql == true) ? Core::getMessage("updt") : Core::getMessage("default");
					Core::response(Core::json($result));
					
				}else Core::getMessage("default");	
		}

		private function deleteHuman(){

			$id = (!empty($this->_request['id'])) ? $this->_request['id'] : "";
			if($id > 0){				
				$sql = mysql_query("DELETE FROM $this->tbl WHERE id = $id");
				$result = ($sql == true) ? Core::getMessage("dlt") : Core::getMessage("default");
				Core::response(Core::json($result));
			}else Core::getMessage("default");
		}

	}

	$api = new HumansAPI;
	$api->processRequest();

?>
