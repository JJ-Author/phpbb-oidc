<?php

namespace ojathelonius\oidc\service;

/* This is required to use /includes functions from extensions */
include($this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext);


class UserService
{   
	public static function userExists($sub) {

		/* user_get_id_name requires passing parameters by value */
		$userArray = [];
		$subjectArray = [$sub];
		
		/* Retrieve user based on subject */
		user_get_id_name($userArray, $subjectArray);

		return !empty($userArray);
	}

	public static function createUser() {
		
	}
}
