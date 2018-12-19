<?php

namespace ojathelonius\oidc\service;

/* This is required to use /includes functions from extensions */
include($this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext);


class UserService
{   

	/**
	 * @param bool $sub
	 * @return bool
	 */
	public static function userExists($sub) {

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
	public static function createUser($oidcUser) {

		/* TODO : customize default values or inherit from IdP */
		$defaultGroupId = 1;
		$defaultUserType = 1;
		
		/* TODO : use subject instead of username as principal */
		$userArray = [
			"username" => $oidcUser->getPreferredUsername(),
			"group_id" => $defaultGroupId,
			"user_email" => $oidcUser->getEmail(),
			"user_type" => $defaultUserType
		];
		$userId = user_add($userArray);

		var_dump($userId);
		exit();
	}
}
