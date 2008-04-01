<?php

$path_extra = '/var/simplesamlphp-openwiki/lib';
$path = ini_get('include_path');
$path = $path_extra . PATH_SEPARATOR . $path;
ini_set('include_path', $path);

/**
 * Loading simpleSAMLphp libraries
 */
require_once('SimpleSAML/Configuration.php');
require_once('SimpleSAML/Utilities.php');
require_once('SimpleSAML/Session.php');
require_once('SimpleSAML/Metadata/MetaDataStorageHandler.php');
require_once('SimpleSAML/XHTML/Template.php');

/*
 * Loading OpenWiki libraries*
 */
require_once('../lib/OpenWiki.class.php');
require_once('../lib/OpenWikiDictionary.class.php');

/**
 * Initializating configuration
 */
SimpleSAML_Configuration::init(dirname(dirname(__FILE__)) . '/config', 'simplemultiwiki');
SimpleSAML_Configuration::init('/var/simplesamlphp-openwiki/config');

$config = SimpleSAML_Configuration::getInstance('simplemultiwiki');

// Starting sessions.
session_start();



/* Load simpleSAMLphp, configuration and metadata */
$sspconfig = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
$session = SimpleSAML_Session::getInstance();

/* Check if valid local session exists.. */
if (!isset($session) || !$session->isValid('saml2') ) {
	SimpleSAML_Utilities::redirect(
		'/' . $sspconfig->getValue('baseurlpath') .
		'saml2/sp/initSSO.php',
		array('RelayState' => SimpleSAML_Utilities::selfURL())
		);
}
$attributes = $session->getAttributes();

#$username = $attributes['eduPersonPrincipalName'][0];
$username = 'na';
if (isset($attributes['mail'])) {
	$username = $attributes['mail'][0];
}
if (isset($attributes['eduPersonPrincipalName'])) {
	$username = $attributes['eduPersonPrincipalName'][0];
}


include('../config/groups.php');





$link = mysql_connect(
	$config->getValue('db.host', 'localhost'), 
	$config->getValue('db.user'),
	$config->getValue('db.pass'));
if(!$link){
	throw new Exception('Could not connect to database: '.mysql_error());
}
mysql_select_db($config->getValue('db.name','feideopenwiki'));


$owd = new OpenWikiDirectory($link);


$list = $owd->getListPublic();
$listprivate = $owd->getListOwner($username);

$et = new SimpleSAML_XHTML_Template($config, 'wikilist.php');
$et->data['header'] = 'List of wikis';
$et->data['user'] = $username;
$et->data['listpublic'] = $list;
$et->data['listprivate'] = $listprivate;


$et->show();


?>