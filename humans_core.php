<?php


	Class Core {
		
	
		public $data = "";

		public static function getMessage($messageCode){

			switch ($messageCode) {
				case "noRoute":
					echo self::renderRoutes();
					break;
				
				case "noHuman":
					$msg = array("message" => "No human found");
					self::response(self::json($msg));
					break;

				case "insrt":
					$msg = array("message" => "A human is born! Yey!");
					self::response(self::json($msg));
					break;

				case "updt":
					$msg = array("message" => "You changed a human! Woo!");
					self::response(self::json($msg));
					break;
				
				case "dlt":
					$msg = array("message" => "The human just died! :( ");
					self::response(self::json($msg));
					break;

				default:
					$msg = array("message" => "Sorry human, There was a problem understanding the request.");
					self::response(self::json($msg));
					break;
			}


		}


		private function renderRoutes(){

			$html = '';
			$html .= '<div class="" style="font-family:Calibri;line-height:2">';
			$html .= '	<ul>';
			$html .= '		<p>No routes found.<br/> HUMAN API serves the following routes:</p>';
			$html .= '		<li><b>GET /humans</b> - Retrieve all humans in the database</li>';
			$html .= '		<li><b>GET /human/{id}</b> - Retrieve a single human record by ID</li>';
			$html .= '		<li><b>POST /human</b> - Create a new human record</li>';
			$html .= '		<li><b>PUT /human/{id}</b> - Update a human record by ID</li>';
			$html .= '		<li><b>DELETE /human{id}</b> - Delete a human record by ID</li>';
			$html .= '	</ul>';
			$html .= '</div>';

			return  $html;
		}


		/* Encode array data into JSON */
		public static function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}

		public static function set_headers(){
			//header("HTTP/1.1 ".$this->_code." ".$this->get_code_desc());
			header("Content-Type: application/json");
		}

		public static function response($data){
			self::set_headers();
			echo $data;
			exit;
		}

	}



?>