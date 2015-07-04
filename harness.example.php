<?php 
session_start();
/**
* 
* 
* 
* 
*/
$AUTH_HARNESS = new JCORE\AUTH\AUTH_HARNESS();
if(true !== $AUTH_HARNESS->register('JCORE\SERVICE\AUTH\PAGE_FILTER')){
	die('failed to load PAGE_FILTER');
}



/**
* call our authentication method/service, we're only looking for a boolean response
* for a basic website, for an API we'll do a different hook forcing 
* authentication at the header level or in the transport request
* 
*/
$AUTH_TEST = true; //add your hook here
$AUTH_TEST = $AUTH_HARNESS->authenticate(
	'SERVICE\AUTH\LOGIN_SERVICE',
	array(
		'AUTH_TYPE' => 'session'
	)
);
/**
* pages not to lock out
* login, signup, logout
* 
*/
$PAGE_HOOKS = $GLOBALS["CONFIG_MANAGER"]->getSetting('AUTH','PAGE_FILTER_ALLOW_PUBLIC');

$PAGE_TEST = $AUTH_HARNESS->authenticate('JCORE\SERVICE\AUTH\PAGE_FILTER2',$PAGE_HOOKS);
#######################################
#echo ' restrictive mode...pass the white list first, then check credentials<br>'.PHP_EOL;
if(true === $PAGE_TEST){
	#$passed = true;
}else{
	#echo ' run a secondary auth test<br>'.PHP_EOL;
	if(false === $AUTH_TEST){
		/**
		echo 'redirect<br>'.PHP_EOL;
		echo __METHOD__.__LINE__.'$_SESSION<pre>['.var_export($_SESSION, true).']</pre>'.PHP_EOL; 
		exit;
		*/
		header('Location: http://'.$_SERVER['SERVER_NAME'].'/login.php');
		exit;
	}
}
?>