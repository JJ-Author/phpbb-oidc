<?php

namespace ojathelonius\oidc\auth\provider;

use Jumbojett\OpenIDConnectClient;
use Symfony\Component\Yaml\Yaml;

if (!defined('IN_PHPBB')) {
    exit;
}

class auth_oidc extends \phpbb\auth\provider\base
{
    /** @var \phpbb\db\driver\driver_interface $db */
    protected $db;

    /**
     * Database Authentication Constructor
     *
     * @param \phpbb\db\driver\driver_interface $db
     */
    public function __construct(\phpbb\db\driver\driver_interface $db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function login($username, $password)
    {
        $this->oidcLogin();
    }

    /**
     * {@inheritdoc}
     */
    public function autologin()
    {
        $this->oidcLogin();
    }

    /**
     * OpenID Connect login
     */
    public function oidcLogin()
    {
        $config = $this->getOIDCConfig();
        $oidc = new OpenIDConnectClient(
            $config['url'],
            $config['clientId'],
            $config['secret']);

        $oidc->setVerifyPeer($config['ssl']);
        $oidc->setVerifyHost($config['ssl']);

        $oidc->setRedirectURL(generate_board_url() . '/');
        $oidc->authenticate();

    }

    /**
     * Retrieve OIDC config from yml config file
     */
    public function getOIDCConfig()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/../../config/oidc.yml'));
    }
}
