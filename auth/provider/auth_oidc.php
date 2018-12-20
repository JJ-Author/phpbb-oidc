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

    /**
	 * OIDC Authentication Constructor
	 *
	 * @param	\phpbb\db\driver\driver_interface 	$db		Database object
	 * @param	\phpbb\config\config 		$config		Config object
	 * @param	string 				$phpbb_root_path		Relative path to phpBB root
	 * @param	string 				$php_ext		PHP file extension
	 */
    public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, $phpbb_root_path, $php_ext)
    {
        /* Plugin configuration */
        $this->pluginConfig = $this->getPluginConfig();

        $this->userService = new UserService($db, $config, $phpbb_root_path, $php_ext);
    }

    /**
     * {@inheritdoc}
     */
    public function login($username, $password)
    {
        /**
         * The login function is designed to receive username and passwords, thus is redundant with OpenID Connect login.
         * It is possible to implement it, however it should return array messages instead of user rows, see phpbb\auth or
         * phpbb\auth\provider\apache for an example
         */
    }

    /**
     * {@inheritdoc}
     */
    public function autologin()
    {
        return $this->oidcLogin();
    }

    /**
     * OpenID Connect login
     */
    public function oidcLogin()
    {

        $oidc = new OpenIDConnectClient(
            $this->pluginConfig['url'],
            $this->pluginConfig['clientId'],
            $this->pluginConfig['secret']);

        $oidc->setVerifyPeer($this->pluginConfig['ssl']);
        $oidc->setVerifyHost($this->pluginConfig['ssl']);

        $oidc->setRedirectURL(generate_board_url() . '/');
        $oidc->authenticate();

        /* Create OIDCUser */
        $oidcUser = new OIDCUser($oidc->requestUserInfo());

        /* If user does not already exist */
        if (!$this->userService->userExists($oidcUser->getPreferredUsername())) {

            /* If configuration allows, create new user */
            if ($this->pluginConfig['createIfMissing']) {
                return $this->userService->createUser($oidcUser);
            } else {
                /* TODO : handle error */
            }

        } else {
            return $this->userService->getUserRow($oidcUser->getPreferredUsername());
        }
    }

    /**
     * Retrieve plugin configuration from yml config file
     */
    public function getPluginConfig()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/../../config/oidc.yml'));
    }
}
