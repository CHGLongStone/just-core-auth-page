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
		#echo __METHOD__.__LINE__.'$params<pre>['.var_export($params, true).']</pre>'.'<br>'; 
		if(!isset($params["FILTER_TYPE"])){
			return false;
		}
		switch(strtoupper($params["FILTER_TYPE"])){
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
		$result = array();
		/**
		* do a quick check first
		*/
		if(
			true === in_array($_SERVER["REQUEST_URI"],$args['ALLOW'])
			||
			true === in_array($_SERVER["PHP_SELF"],$args['ALLOW'])
		){
			$result['status'] = 'OK';
			$this->serviceResponse = $result;
			return $this->serviceResponse;
		}
		
		/**
		* then more thorough
		*/
		foreach($args['ALLOW'] as $key => $value){
			if(
				$value == strstr($_SERVER["REQUEST_URI"], $value)
				||
				$value == strstr($_SERVER["PHP_SELF"], $value)
			){
				$result['status'] = 'OK';
				$this->serviceResponse = $result;
				return $this->serviceResponse;
			}
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