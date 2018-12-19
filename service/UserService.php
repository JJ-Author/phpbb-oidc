<?php

namespace ojathelonius\oidc\service;

/* This is required to use /includes functions from extensions */
include $this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext;

class UserService
{

    /**
     * @param bool $sub
     * @return bool
     */
    public static function userExists($sub)
    {

        /* user_get_id_name requires passing parameters by value */
        $userArray = [];
        $subjectArray = [$sub];

        /* Retrieve user based on subject */
        user_get_id_name($userArray, $subjectArray);

        return !empty($userArray);
    }

    /**
     * @param OIDCUser $oidcUser
     */
    public static function createUser($oidcUser)
    {

        /* TODO : customize default values or inherit from IdP */
        $defaultGroupId = 1;
        $defaultUserType = 1;

        /* TODO : use subject instead of username as principal */
        $userArray = [
            "username" => $oidcUser->getPreferredUsername(),
            "group_id" => $defaultGroupId,
            "user_email" => $oidcUser->getEmail(),
            "user_type" => $defaultUserType,
        ];
        return user_add($userArray);
    }

    /**
     * @param bool $sub
     * @return array
     */
    public static function getUserRow($sub)
    {
		global $db;
		echo $sub;
        $sql = 'SELECT user_id, username, user_password, user_passchg, user_email, user_type, user_login_attempts
            FROM ' . USERS_TABLE . "
            WHERE username_clean = '" . $db->sql_escape($sub) . "'";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	
		return $row;
    }

}
