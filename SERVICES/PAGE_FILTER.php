<?php
/**
 * Very basic auth mechanism to check if the page being loaded is one 
 * you allow access to and under what conditions
 * 
 * 
 * @author	Jason Medland<jason.medland@gmail.com>
 * @package	JCORE
 * @subpackage	AUTH
 */
 

/**
 * Class PHPASS
 *
 * @package JCORE\AUTH
*/
namespace JCORE\SERVICE\AUTH;
use JCORE\TRANSPORT\SOA\SOA_BASE as SOA_BASE; //if it extends SOA_BASE it should be a a *.service.php class
use JCORE\DAO\DAO as DAO;
use JCORE\AUTH\AUTH_INTERFACE as AUTH_INTERFACE;


#use JCORE\SERVICE\CRUD\CRUD as CRUD;
##### -^ update this 
#use SERVICE\AUTH\PHPASS as PHPASS;

/**
 * Class PAGE_FILTER
 *
 * @package SERVICE\AUTH 
*/
class PAGE_FILTER extends SOA_BASE implements AUTH_INTERFACE{ 
	/** 
	* 
	*/
	protected $serviceRequest = null;
	/** 
	* 
	*/
	public $serviceResponse = null;
	/** 
	* 
	*/
	public $error = null;
	
	/**
	* DESCRIPTOR: an empty constructor, the service MUST be called with 
	* the service name and the service method name specified in the 
	* in the method property of the JSONRPC request in this format
	* 		""method":"AJAX_STUB.aServiceMethod"
	* 
	* @param param 
	* @return return  
	*/
	public function __construct(){
		return;
	}
	
	public function init($args){
		/**
		* echo __METHOD__.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'; 
		*/
		return;
	}
	
	/**
	* DESCRIPTOR: an example namespace call 
	* 
	* @params array 
	* @return this->serviceResponse  
	*/
	public function authenticate($params = null){
		if(!isset($params["FILTER_TYPE"])){
			return false;
		}
		switch(strtolower($params["FILTER_TYPE"])){
			case "WHITELIST":
				$this->authenticateWHITELIST($params);
				break;
			case "BLACKLIST":
				$this->authenticateBLACKLIST($params);
				break;
			default:
				return false;
				break;
		}
		#echo __METHOD__.__LINE__.'$this->serviceResponse<pre>['.var_export($this->serviceResponse, true).']</pre>'.'<br>'; 
		if(isset($this->serviceResponse["status"]) && 'OK' == $this->serviceResponse["status"]){
			return true;
		}
		return false;
	}
	
	
	/**
	* DESCRIPTOR: an example namespace call 
	* 
	* @args array 
	* @return this->serviceResponse  
	*/
	public function authorize($params = null){
		
		return false;
	}
	/**
	* DESCRIPTOR: an example namespace call 
	* 
	* @args array 
	* @return this->serviceResponse  
	*/
	public function authenticateWHITELIST($args){
		
		#echo __METHOD__.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'; 
		
		$this->init($args);
		$this->DAO = new DAO();
		#$this->DAO = new DAO($args);
		$searchCriteria = array(
			'DSN' => 'BLACKWATCH',
			'table' => 'client_user',
			'pk_field' => 'client_user_pk',
			'foundation' => true,
			'search' => array(
				'email' => $args["email"],
				#'password' => $args["password"],
			),
		);
		#$this->DAO->initialize($args["DSN"], $args["table"], true);
		$this->DAO->initializeBySearch($searchCriteria);
		$stored_hash = $this->DAO->get($searchCriteria["table"], 'password');
		#echo __METHOD__.__LINE__.'$password<pre>['.var_export($stored_hash, true).']</pre>'.'<br>'.PHP_EOL; 

		if(true ===  \password_verify($args['password'], $stored_hash)){
			$result['status'] = 'OK';
			$result['user_id'] = $this->DAO->get($searchCriteria["table"], $searchCriteria["pk_field"]);
			$this->serviceResponse = $result;
		}else{
			$result['error'] = 'failed to authenticate';
			$this->serviceResponse = $result;
		}
		
		
		#echo __METHOD__.__LINE__.'$this->serviceResponse<pre>['.var_export($this->serviceResponse, true).']</pre>'.'<br>'.PHP_EOL; 
		#echo __METHOD__.__LINE__.'$this->DAO<pre>['.var_export($this->DAO, true).']</pre>'.'<br>'.PHP_EOL; 
		return $this->serviceResponse;
	}
	
	
	/**
	* DESCRIPTOR: an example namespace call 
	* @param param 
	* @return return  
	*/
	public function authenticateUserSession($args){
		/*
		echo __METHOD__.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'.PHP_EOL; 
		echo __METHOD__.__LINE__.'$_SESSION<pre>['.var_export($_SESSION, true).']</pre>'.'<br>'.PHP_EOL; 
		*/
		if(
			!isset($_SESSION) 
			||
			(
				!isset($_SESSION['user_id']) 
				|| 
				!is_numeric($_SESSION['user_id'])
			)
			|| 
			!isset($_SESSION['user_email'])
		){
			$result['error'] = 'failed to authenticate';
			$this->serviceResponse = $result;
			return $this->serviceResponse;
		}
		/*
		$_SESSION['user_id'] = $authCheck["user_id"];
		$_SESSION['user_email'] = $_POST["email"];
		*/
		$this->DAO = new DAO();
		$searchCriteria = array(
			'DSN' => 'BLACKWATCH',
			'table' => 'client_user',
			'pk_field' => 'client_user_pk',
			'foundation' => true,
			'search' => array(
				'email' => $_SESSION['user_email'],
				#'password' => $args["password"],
			),
		);
		$this->DAO->initializeBySearch($searchCriteria);
		$user_id = $this->DAO->get($searchCriteria["table"], $searchCriteria["pk_field"]);
		#echo __METHOD__.__LINE__.'$user_id['.$user_id.'] $_SESSION["user_id"]['.$_SESSION['user_id'].']</pre>'.'<br>'; 
		if($user_id  == $_SESSION['user_id']){
			$result['status'] = 'OK';
			$this->serviceResponse = $result;
			return $this->serviceResponse;
		}
		return false;
	}
	/**
	* DESCRIPTOR: an example namespace call 
	* @param param 
	* @return return  
	*/
	public function aServiceMethod($args){
		#echo __METHOD__.__LINE__.'<br>';
		#echo __METHOD__.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'; 
		if(!isset($args["action"])){
			$this->error = new StdClass();
			$this->error->code = "FAILED_CALL";
			$this->error->message = ' NO SERVICE ACTION DEFINED';
			$this->error->data = 'no service call made';
			return false;
		}

		$this->serviceResponse = array();
		$this->serviceResponse["title"] = 'Block Eight';
		$this->serviceResponse["type"] = 'page';
		return true;
	}
	
}



?>