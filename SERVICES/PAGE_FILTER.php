<?php
/**
 * Very basic auth mechanism to check if the page being loaded is one 
 * you allow access to and under what conditions
 * 
 * 
 * @author	Jason Medland<jason.medland@gmail.com>
 * @package	JCORE\SERVICE\AUTH 
 */

namespace JCORE\SERVICE\AUTH;
use JCORE\TRANSPORT\SOA\SOA_BASE as SOA_BASE; //if it extends SOA_BASE it should be a a *.service.php class
use JCORE\DAO\DAO as DAO;
use JCORE\AUTH\AUTH_INTERFACE as AUTH_INTERFACE;


/**
 * Class PAGE_FILTER
 *
 * @package JCORE\SERVICE\AUTH 
*/
class PAGE_FILTER extends SOA_BASE implements AUTH_INTERFACE{ 
	/**
	* serviceRequest
	* 
	* @access protected 
	* @var string
	*/
	protected $serviceRequest = null;
	/**
	* serviceResponse
	* 
	* @access public 
	* @var string
	*/
	public $serviceResponse = null;
	/**
	* error
	* 
	* @access public 
	* @var mixed
	*/
	public $error = null;
	
	/**
	* DESCRIPTOR: an empty constructor, the service MUST be called with 
	* the service name and the service method name specified in the 
	* in the method property of the JSONRPC request in this format
	* 		""method":"AJAX_STUB.aServiceMethod"
	* 
	* @param null 
	* @return null
	*/
	public function __construct(){
		return;
	}
	/**
	* DESCRIPTOR: init
	* 
	* @access public 
	* @param array args
	* @return null
	*/
	public function init($args){
		/**
		* echo __METHOD__.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'; 
		*/
		return;
	}
	
	/**
	* DESCRIPTOR: authenticate against:
	* abstracted for JCORE-AUTH-AUTH_HARNESS
	*   WHITELIST
	*   BLACKLIST
	*   TOKEN
	*  
	* 
	* @access public 
	* @param array params
	* @return bool
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
			case "TOKEN":
				$this->authenticateTOKEN($params);
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
	* DESCRIPTOR: authorize
	* 
	*  
	* 
	* @access public 
	* @param array params
	* @return bool
	*/
	public function authorize($params = null){
		
		return false;
	}
	/**
	* DESCRIPTOR: authenticateWHITELIST
	* 
	* @access public 
	* @param array args
	* @return bool
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
	* DESCRIPTOR: authenticateBLACKLIST 
	*  
	* 
	* @access public 
	* @param array params
	* @return bool
	*/
	public function authenticateBLACKLIST($args){
		$result = array();
		/**
		* do a quick check first
		*/
		if(
			true === !in_array($_SERVER["REQUEST_URI"],$args['DENY'])
			||
			true === !in_array($_SERVER["PHP_SELF"],$args['DENY'])
		){
			$result['status'] = 'OK';
			$this->serviceResponse = $result;
			return $this->serviceResponse;
		}
		
		/**
		* then more thorough
		*/
		foreach($args['DENY'] as $key => $value){
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
	* DESCRIPTOR: authenticateTOKEN
	* 
	*  
	* 
	* @access public 
	* @param array args
	* @return bool
	*/
	public function authenticateTOKEN($args){
		$result = array();
		
		/*
		* A crude example below for the TOKEN_HAYSTACK
		* 
		* 
		* 
			$W =  md5 ( 'what' ); 	//4a2028eceac5e1f4d252ea13c71ecec6
			$T =  md5 ( 'the' ); 	//8fc42c6ddf9966db3b09e84365034357
			$F =  md5 ( 'fuck' ); 	//99754106633f94d350db34d548d6091a
			//all together now 
			$WTF =  md5 ( $W.$T.$F ); //d41d8cd98f00b204e9800998ecf8427e
			
			$args["TOKEN"] = array(
				//'TOKEN_SCOPE' => '_REQUEST', //_POST,_GET,_REQUEST,args ...
				//'TOKEN_NAME' => 'PUBLIC_TOKEN',
				'TOKEN_VALUE' => 'TOKEN_VALUE',
				'TOKEN_HAYSTACK' => array( 
					// a filtered result set prepared by whatever (extended class etc.) 
					// is calling this service method 
					$W,
					$T,
					$W,
					$WTF,
				),
			);
		*/
		
		/**
		* do a basic arg validation check first
		*/		
		if(!isset($args["TOKEN"]) && !is_array($args["TOKEN"])){
			return false;
		}
		/**
		* do a quick check first
		*/		
		if(
			true === in_array($args["TOKEN"]["TOKEN_VALUE"],$args['TOKEN']['TOKEN_HAYSTACK'])
		){
			/**
			* then more thorough
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
			*/
			$result['status'] = 'OK';
			$this->serviceResponse = $result;
			return $this->serviceResponse;
		}else{
			return false;
		}
		


		
	}
	
	

	/**
	* DESCRIPTOR: aServiceMethod..an example namespace call 
	*  
	* 
	* @access public 
	* @param array args
	* @return bool
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