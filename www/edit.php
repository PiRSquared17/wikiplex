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

/**
 * Initializating configuration
 */
SimpleSAML_Configuration::init(dirname(dirname(__FILE__)) . '/config', 'simplemultiwiki');
SimpleSAML_Configuration::init('/var/simplesamlphp-openwiki/config');

$config = SimpleSAML_Configuration::getInstance('simplemultiwiki');

// Starting sessions.
session_start();


include('../config/groups.php');


$username = 'andreas_solberg_uninett';


if (empty($_SESSION['cachedwiki'])) {

	// Create a test wiki
	$ow = new OpenWiki('testwiki');
	$ow->setInfo('Test wiki', 'This is a wiki Andreas is testing', 'andreas_solberg_uninett', 3);
	$ow->addACL(array('@org_x_dc_uninett_no', 1));
	$ow->addACL(array('@org_x_dc_uninett_no2', 15));
	$ow->addACL(array('@org_x_dc_uninett_no3', 1));
	$ow->addACL(array('@org_x_dc_uninett_no4', 0));

	
} else {

	$ow =& $_SESSION['cachedwiki'];

}

if (!empty($_REQUEST['name'])) {
	$ow->setInfo($_REQUEST['name'], $_REQUEST['descr'], $username, $_REQUEST['access']);
}
if (isset($_REQUEST['aclswap'])) {
	$ow->swapACL($_REQUEST['aclswap']);
}
if (isset($_REQUEST['acldelete'])) {
	$ow->removeACL($_REQUEST['acldelete']);
}
if (!empty($_REQUEST['addgroupid'])) {
	$ow->addACL($_REQUEST['addgroupid'], $_REQUEST['addgrouplevel']);
} elseif(!empty($_REQUEST['addpersonid'])) {
	$ow->addACL($_REQUEST['addpersonid'], $_REQUEST['addpersonlevel']);
}

if (isset($_REQUEST['save']) ) {
	#$ow->save();
	
	$et = new SimpleSAML_XHTML_Template($config, 'wikisave.php');
	$et->data['header'] = 'Wiki is successfully saved';
	$et->data['identifier'] = $ow->getIdentifier();
	$et->data['name'] = $ow->getName();
	$et->data['descr'] = $ow->getDescr();	
	$et->show();
	exit;
}



$thiswiki = null;
if (isset($_REQUEST['edit'])) {
	$_SESSION['edit'] = $_REQUEST['edit'];
	$thiswiki = $_REQUEST['edit'];
} elseif(isset($_SESSION['edit'])) {
	$thiswiki = $_SESSION['edit'];
}
if (empty($thiswiki)) throw new Exception('No wiki selected');


$ow->needAdminAccess($username);


#echo 'dump: <pre>' . $ow->getACLdefinition() . '</pre>';



$et = new SimpleSAML_XHTML_Template($config, 'wikiedit.php');
$et->data['header'] = 'Edit wiki';
$et->data['tgroups'] = $groups;
$et->data['taccess'] = $access;

$et->data['identifier'] = $ow->getIdentifier();
$et->data['name'] = $ow->getName();
$et->data['descr'] = $ow->getDescr();
$et->data['acl'] = $ow->getCustomACL();
$et->data['access'] = $ow->getAccess();

$et->show();


?>