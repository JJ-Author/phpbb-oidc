<?php

namespace ojathelonius\oidc\auth\provider;

use Jumbojett\OpenIDConnectClient;
use ojathelonius\oidc\model\OIDCUser;
use ojathelonius\oidc\service\UserService;
use Symfony\Component\Yaml\Yaml;

if (!defined('IN_PHPBB')) {
    exit;
}

class auth_oidc extends \phpbb\auth\provider\base
{
    /* Configuration */
    private $pluginConfig;

    private $userService;

    private $oidc;
    private $redirectUrl;
    private $workaroundRed;
    private $workaroundLogout;

    /**
     * OIDC Authentication Constructor
     *
     * @param    \phpbb\db\driver\driver_interface     $db        Database object
     * @param    \phpbb\config\config         $config        Config object
     * @param    string                 $phpbb_root_path        Relative path to phpBB root
     * @param    string                 $php_ext        PHP file extension
     */
    public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, $phpbb_root_path, $php_ext)
    {
	if($this->dbg())
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		echo "called auth constructor: with GET parameters:";
		echo print_r($_GET);
	}
        /* Plugin configuration */
        $this->pluginConfig = $this->getPluginConfig();

        $this->userService = new UserService($db, $config, $phpbb_root_path, $php_ext);

	$this->oidc = new OpenIDConnectClient(
       	  $this->pluginConfig['url'],
          $this->pluginConfig['clientId'],
          $this->pluginConfig['secret'] );
	
        $this->oidc->setVerifyPeer($this->pluginConfig['ssl']);
        $this->oidc->setVerifyHost($this->pluginConfig['ssl']);
	$red = $this->oidc->getRedirectUrl();
	$this->redirectUrl = $this->pluginConfig['redirect'] . '/?oidc=true';
	if ($this->dbg()) echo "\n\t automatically derived redirect: $red";
	if ($this->dbg()) echo "\n\t referrer: $_SERVER[HTTP_REFERER]";
	if ($this->pluginConfig['stableRedirect'])
	{
		//print_r($config);
		if ($this->dbg()) echo " override automatic redirect with stable redirect from config .";
		$this->oidc->setRedirectURL($this->redirectUrl);	
	}
	//$this->workaroundRed = 'workaround';//$this->redirectUrl."/workaround";
	$red = $this->oidc->getRedirectUrl();
	if ($this->dbg()) echo "\n final redirect: $red";
    }

    /**
     * {@inheritdoc}
     */
    public function login($username, $password)
    {
	    if($this->dbg()) echo "\ncalled auth login()";
	    return null;
        /**
         * The login function is designed to receive username and passwords, thus is redundant with OpenID Connect login.
         * It is possible to implement it, however it should return array messages instead of user rows, see phpbb\auth or
         * phpbb\auth\provider\apache for an example
         */
    }

    /**
     *returns whether plugin should print debug messages
     */
    private function dbg()
    {
	return $this->getPluginConfig()['debug'];
    }

    /**
     * {@inheritdoc}
     */
    public function autologin()
    {
	 if($this->dbg()) 
	 {
		 $red = $this->oidc->getRedirectUrl();
		 echo "\ncalled autologin() function: current OIDC redirect: $red and GET parameters:";
		 echo print_r($_GET);
	 }
	 $login_called = $_GET['login'];
       
	 if ($this->passToOidc())
	 {
		 $this->workaroundLogout = false;
		 return $this->oidcLogin();
	 }
	 else
       	         if($this->dbg()) echo " ===> did not perform OIDC login since the called page (and Get parameters) is not configured to trigger OIDCLogin --> use anynomous session as fallback";
    }

    /**
     * {@inheritdoc}
     */
    public function logout($data, $new_session)
    { 
	$red= $this->oidc->getRedirectURL();
	if($this->dbg()) echo "\ncalled auth logout(): current OIDC redirect =$red";
	/**
	 * this is a workaround: since logout is also called to kill an anomymous session in order to initiate an OICD login we need to check whether it is an actual "OICD logout" or just anonymous session logout
	 */
        if (!$this->workaroundLogout)	// this is a regular session logout (e.g. user clicked on logout button)
	{     
		if ($this->pluginConfig['LogoutAllOidcOnForumLogout']) //check if to logout also "OIDC session" or only local phpbb session which was authenticated by keycloak
			$this->oidc->signOut($this->oidc->getAccessToken(), $this->oidc->getRedirectURL());
	}
	else // this is the case where we actually want to logout an anomymous session in order to login via OICD.  we reset the redirect URI in order to end the workaround and be able to really log out in case logout button will be called in the future
	{	
		//$this->oidc->setRedirectURL($this->redirectUrl);
	}
    }

    /**
     * OpenID Connect login
     */
    private function oidcLogin()
    {   
        $this->oidc->authenticate();

        /* Create OIDCUser */
        $oidcUser = new OIDCUser($this->oidc->requestUserInfo());

        /* If user does not already exist */
        if (!$this->userService->userExists($oidcUser->getPreferredUsername())) {

            /* If configuration allows, create new user */
            if ($this->pluginConfig['createIfMissing']) {
                return $this->userService->createUser($oidcUser);
            } else {
                /* TODO : handle error */
                /* The issue here is that we cannot call trigger_error() from autologin to display a proper error message */
	    	echo "Something went wrong with creating a new user for you";
	    }

        } else {
            $this->userService->updateUser($oidcUser);
            return $this->userService->getUserRow($oidcUser->getPreferredUsername());
        }
    }

    /**
     *check whether the current web request should be passed over to OIDC procedures or not
     */
    private function passToOidc()
    {
	    //$page=basename($_SERVER['REQUEST_URI']);
	    $page = basename($_SERVER['PHP_SELF']);
	    if ($this->dbg()) echo " called page: $page";
	    $mode=$_GET['mode'];
	    $ucp_login= false; //($mode=='login'); //TODO consider all pages with mode=login parameter as login page
	    $callback_oidc=$_GET['state'] && $_GET['session_state'] && $_GET['code']; //TODO the callback from keycloak to phpbb with login information
	    $called_getLogin=($this->workaroundLogout);
	    if ($this->dbg()) echo " called get_login= $called_getLogin";
	    return ($ucp_login || $callback_oidc || $called_getLogin);
    }

    /**
     * logs out the session of the current user if it exists
     */
    public function myphpbb_logout()
    {
		    global $user;
		    try 
		    {
			    if ($this->session_exists())
			    {
				    if($this->dbg()) {echo "\nresetting session_id : $user->session_id";}
				    $user->session_kill();
			    }
			    else
				    if($this->dbg()) echo "~~~~~~~ NO session ID yet !!!!!!";
		    } catch (Exception $e) {echo "reached catch";}
    }

    /**
     * check if a session exists for the user (true) or whether it is being created (false)
     */
    private function session_exists() 
    { 
	global $user;
	$sid = $user->session_id;
	if ($sid!='')
		return true;
	else
		return false;					  
    }  

   /**
    * workaround to reset session data when the login page is rendered in order to delete the (anonymous) user session so that phpBB triggers autologin function call again for the visit of the next site he will get redirected to 
    */
    public function get_login_data()
    {
	global $user;    
	if ($this->dbg()) echo "\ncalled get_login_data: ";
	if ($this->session_exists())
	{
		if ($this->dbg()) echo "\n\t try to terminate current user session to perform autologin in next HTTP call";
		//$this->oidc->setRedirectURL($this->workaroundRed);
		$this->workaroundLogout = true; // remember that the logout was not triggered by user but by ourself
		$this->myphpbb_logout();
	}
    }

    /**
     * Retrieve plugin configuration from yml config file
     */
    private function getPluginConfig()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/../../config/oidc.yml'));
    }
}
