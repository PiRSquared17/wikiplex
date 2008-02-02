<?php

/**
 *
 */
class OpenWiki {

	private $identifier;
	private $name;
	private $descr;
	private $owner;
	
	/**
	 * 0 Private
	 * 1 All feide users can read, no anonymous access
	 * 2 Anonymous users can read
	 * 3 Feide users can write, no anonymous access
	 * 4 Feide users can write, anonymous users can read
	 */
	private $access = 0;
	private $customacl = array();
	
	private $loadedFromDB = false;

	function __construct($identifier) {
		$this->identifier = $identifier;
	}
	
	function setInfo($name, $descr, $owner, $access) {
		$this->name = $name;
		$this->descr = $descr;
		$this->owner = $owner;
		$this->access = $access;
	}
	
	public function loadFromDB() {
		throw new Exception('Could not find wiki in database');
	}
	
	public function getIdentifier() {
		return $this->identifier;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDescr() {
		return $this->descr;
	}
	
	public function getOwner() {
		return $this->owner;
	}
	
	public function getAccess() {
		return $this->access;
	}
	
	private function publicACL() {
		$aclmap = array(
			0 => 0,
			1 => 0,
			2 => 1,
			3 => 0,
			4 => 1
		);
		return $aclmap[$this->getAccess()];
	}
	
	private function feideACL() {
		$aclmap = array(
			0 => 0,
			1 => 1,
			2 => 1,
			3 => 15,
			4 => 15
		);
		return $aclmap[$this->getAccess()];
	}
	
	
	public function addACL($groupid, $level) {
		$this->customacl[] = array($groupid, $level);
	}
	
	public function removeACL($no) {
		$newacl = array();
		foreach ($this->customacl AS $key => $entry) {
			if ($key != $no) $newacl[] = $entry;
		}
		$this->customacl = $newacl;
	}
	
	public function getCustomACL() {
		return $this->customacl;
	}
	
	public function swapACL($no) {
		$temp = $this->customacl[$no];
		$this->customacl[$no] = $this->customacl[$no+1];
		$this->customacl[$no+1] = $temp;
	}
	
	/**
	 * Does nothing, but throws an exception when user is not the owner 
	 * of this wiki.
	 */
	public function needAdminAccess($username) {
		if ($username != $this->getOwner()) 
			throw new Exception($username . ' is not the owner of this wiki.');
	}
	
	public function getACLdefinition() {
		$def  = $this->getIdentifier() . ':* @ALL ' . $this->publicACL()  . "\r\n";
		$def .= $this->getIdentifier() . ':* @users ' . $this->feideACL() . "\r\n";
		$def .= $this->getIdentifier() . ':* ' . $this->getOwner() . ' 15' . "\r\n";
		foreach ($this->getCustomACL() AS $aclentry) {
			$def .= $this->getIdentifier() . ':* @' . $aclentry[0] . ' ' . $aclentry[1] . "\r\n";
		}
		return $def;
	}

}

?>