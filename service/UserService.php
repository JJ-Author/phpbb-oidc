<?php

namespace ojathelonius\oidc\service;

class UserService
{

    private $db;
    private $config;
    private $phpbb_root_path;
    private $php_ext;

    public function __construct($db, $config, $phpbb_root_path, $php_ext)
    {
        $this->db = $db;
        $this->config = $config;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->php_ext = $php_ext;

        /* This is required to use /includes functions from extensions */
        if (!function_exists('user_add')) {
            include $this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext;
        }
    }

    /**
     * Returns true if user exists in database, false otherwise
     * @param bool $sub
     * @return bool
     */
    public function userExists($sub)
    {
        /* user_get_id_name requires passing parameters by value as it modifies the one or the other */
        $userArray = [];
        $subjectArray = [$sub];

        /* Retrieve user based on subject */
        user_get_id_name($userArray, $subjectArray);

        return !empty($userArray);
    }

    /**
     * Create user
     * @param OIDCUser $oidcUser
     */
    public function createUser($oidcUser)
    {
        $userId = user_add($this->createDefaultUserRow($oidcUser));

        return $this->getUserRow($oidcUser->getPreferredUsername());
    }

    /**
     * Get user row based on subject
     * @param bool $sub
     * @return array
     */
    public function getUserRow($sub)
    {
        $sql = 'SELECT *
            FROM ' . USERS_TABLE . "
            WHERE username_clean = '" . $this->db->sql_escape($sub) . "'";
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);
        return $row;
    }

    /**
     * Create a default user row
     * @param OIDCUser $oidcUser
     */
    public function createDefaultUserRow($oidcUser)
    {
        /* Retrieve default groupId */
        $sql = 'SELECT group_id
        FROM ' . GROUPS_TABLE . "
        WHERE group_name = '" . $this->db->sql_escape('REGISTERED') . "'
            AND group_type = " . GROUP_SPECIAL;
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$row) {
            trigger_error('NO_GROUP');
        }

        return array(
            'username' => $oidcUser->getPreferredUsername(),
            'user_email' => $oidcUser->getEmail(),
            'group_id' => (int) $row['group_id'],
            'user_type' => USER_NORMAL,
            'user_new' => ($this->config['new_member_post_limit']) ? 1 : 0,
        );
    }

}
