<?php

namespace ojathelonius\oidc\auth\provider;

use Jumbojett\OpenIDConnectClient;
use Symfony\Component\Yaml\Yaml;
use ojathelonius\oidc\model\OIDCUser;
use ojathelonius\oidc\service\UserService;

if (!defined('IN_PHPBB')) {
    exit;
}

class auth_oidc extends \phpbb\auth\provider\base
{
    /** @var \phpbb\db\driver\driver_interface $db */
    protected $db;

    /* Configuration */
    private $config;

    /**
     * Database Authentication Constructor
     *
     * @param \phpbb\db\driver\driver_interface $db
     */
    public function __construct(\phpbb\db\driver\driver_interface $db)
    {
        $this->db = $db;

        $this->config = $this->getOIDCConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function login($username, $password)
    {
        return $this->oidcLogin();
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
            $this->config['url'],
            $this->config['clientId'],
            $this->config['secret']);

        $oidc->setVerifyPeer($this->config['ssl']);
        $oidc->setVerifyHost($this->config['ssl']);

        $oidc->setRedirectURL(generate_board_url() . '/');
        $oidc->authenticate();
        
        /* Create OIDCUser */
        $oidcUser = new OIDCUser($oidc->requestUserInfo());

        /* If user does not exist, and config disallow creating new users */
        if (!UserService::userExists($oidcUser->getSub()) && !$this->config['createIfMissing']) {
            /* TODO : handle exceptions */
        }
    }

    /**
     * Retrieve OIDC config from yml config file
     */
    public function getOIDCConfig()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/../../config/oidc.yml'));
    }
}
