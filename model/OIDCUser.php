<?php

namespace ojathelonius\oidc\model;

class OIDCUser
{   
    private $sub;
    private $name;
    private $given_name;
    private $family_name;
    private $middle_name;
    private $nickname;
    private $preferred_username;
    private $profile;
    private $picture;
    private $website;
    private $email;
    private $email_verified;
    private $gender;
    private $birthdate;
    private $zoneinfo;
    private $locale;
    private $phone_number;
    private $phone_number_verified;
    private $address;
    private $updated_at;

    function __construct($userObject)
    {	
		$this->sub = $userObject->sub;
		$this->name = $userObject->name;
		$this->given_name = $userObject->given_name;
		$this->family_name = $userObject->family_name;
		$this->middle_name = $userObject->middle_name;
		$this->nickname = $userObject->nickname;
		$this->preferred_username = $userObject->preferred_username;
		$this->profile = $userObject->profile;
		$this->picture = $userObject->picture;
		$this->website = $userObject->website;
		$this->email = $userObject->email;
		$this->email_verified = $userObject->email_verified;
		$this->gender = $userObject->gender;
		$this->birthdate = $userObject->birthdate;
		$this->zoneinfo = $userObject->zoneinfo;
		$this->locale = $userObject->locale;
		$this->phone_number = $userObject->phone_number;
		$this->phone_number_verified = $userObject->phone_number_verified;
		$this->address = $userObject->address;
		$this->updated_at = $userObject->updated_at;
		
    }

    public function getSub(){
		return $this->sub;
	}

	public function setSub($sub){
		$this->sub = $sub;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getGiven_name(){
		return $this->given_name;
	}

	public function setGiven_name($given_name){
		$this->given_name = $given_name;
	}

	public function getFamily_name(){
		return $this->family_name;
	}

	public function setFamily_name($family_name){
		$this->family_name = $family_name;
	}

	public function getMiddle_name(){
		return $this->middle_name;
	}

	public function setMiddle_name($middle_name){
		$this->middle_name = $middle_name;
	}

	public function getNickname(){
		return $this->nickname;
	}

	public function setNickname($nickname){
		$this->nickname = $nickname;
	}

	public function getPreferred_username(){
		return $this->preferred_username;
	}

	public function setPreferred_username($preferred_username){
		$this->preferred_username = $preferred_username;
	}

	public function getProfile(){
		return $this->profile;
	}

	public function setProfile($profile){
		$this->profile = $profile;
	}

	public function getPicture(){
		return $this->picture;
	}

	public function setPicture($picture){
		$this->picture = $picture;
	}

	public function getWebsite(){
		return $this->website;
	}

	public function setWebsite($website){
		$this->website = $website;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function getEmail_verified(){
		return $this->email_verified;
	}

	public function setEmail_verified($email_verified){
		$this->email_verified = $email_verified;
	}

	public function getGender(){
		return $this->gender;
	}

	public function setGender($gender){
		$this->gender = $gender;
	}

	public function getBirthdate(){
		return $this->birthdate;
	}

	public function setBirthdate($birthdate){
		$this->birthdate = $birthdate;
	}

	public function getZoneinfo(){
		return $this->zoneinfo;
	}

	public function setZoneinfo($zoneinfo){
		$this->zoneinfo = $zoneinfo;
	}

	public function getLocale(){
		return $this->locale;
	}

	public function setLocale($locale){
		$this->locale = $locale;
	}

	public function getPhone_number(){
		return $this->phone_number;
	}

	public function setPhone_number($phone_number){
		$this->phone_number = $phone_number;
	}

	public function getPhone_number_verified(){
		return $this->phone_number_verified;
	}

	public function setPhone_number_verified($phone_number_verified){
		$this->phone_number_verified = $phone_number_verified;
	}

	public function getAddress(){
		return $this->address;
	}

	public function setAddress($address){
		$this->address = $address;
	}

	public function getUpdated_at(){
		return $this->updated_at;
	}

	public function setUpdated_at($updated_at){
		$this->updated_at = $updated_at;
	}
}
