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
SimpleSAML_Configuration::init(dirname(dirname(__FILE__)) . '/config', 'wikiplex');
SimpleSAML_Configuration::init('/var/simplesamlphp-openwiki/config');

$config = SimpleSAML_Configuration::getInstance('wikiplex');

// Starting sessions.
session_start();


include('../config/groups.php');

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

$username = $attributes['eduPersonPrincipalName'][0];

if (!isset($_SESSION['wikiplex_cachedwiki'])) {
	$_SESSION['wikiplex_cachedwiki'] = array();
}



try {
	
	/*
	 * What wiki are we talking about?
	 */
	$thiswiki = null;
	if (isset($_REQUEST['edit'])) {
		$_SESSION['edit'] = $_REQUEST['edit'];
		$thiswiki = $_REQUEST['edit'];
	} elseif(isset($_SESSION['edit'])) {
		$thiswiki = $_SESSION['edit'];
	}
	if (empty($thiswiki)) throw new Exception('No wiki selected');
	
	
	
	$link = mysql_connect(
		$config->getValue('db.host', 'localhost'), 
		$config->getValue('db.user'),
		$config->getValue('db.pass'));
	if(!$link){
		throw new Exception('Could not connect to database: '.mysql_error());
	}
	mysql_select_db($config->getValue('db.name','feideopenwiki'));
	
	
	
	
	if (! array_key_exists($thiswiki,$_SESSION['wikiplex_cachedwiki'] )) {
	
		// Create a test wiki
		$ow = new OpenWiki($thiswiki, $link);
		#$ow->setInfo('Test wiki', 'This is a wiki Andreas is testing', 'andreas_solberg_uninett', 3);
	
		if (isset($_REQUEST['createnewsubmit'])) {
			
			if (!preg_match('/^[a-z]+$/', $thiswiki))
				throw new Exception('You tried to create a new wiki, but the wiki ID that you chose contained illegal characters. A wiki ID can only contain lowercase letters [a-z]!');
		
			if (!$ow->isLoaded()) {
				$ow->setOwner($username);
			}
			// Load from db.	
		}
		
		$_SESSION['wikiplex_cachedwiki'][$thiswiki] =& $ow;
		
		/*
		$ow->setInfo('Test wiki', 'This is a wiki Andreas is testing', 'andreas_solberg_uninett', 3);
		$ow->addACL(array('@org_x_dc_uninett_no', 1));
		$ow->addACL(array('@org_x_dc_uninett_no2', 15));
		$ow->addACL(array('@org_x_dc_uninett_no3', 1));
		$ow->addACL(array('@org_x_dc_uninett_no4', 0));
		*/
		
	} else {
	
		$ow =& $_SESSION['wikiplex_cachedwiki'][$thiswiki];
	
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
		$ow->setDBhandle($link);
		$ow->savetoDB();
		unset($_SESSION['wikiplex_cachedwiki'][$thiswiki]);
		
		OpenWikiDirectory::writeACLdefinition($link, $config->getValue('aclfile'));
		
		$et = new SimpleSAML_XHTML_Template($config, 'wikisave.php');
		$et->data['header'] = 'Wiki is successfully saved';
		$et->data['identifier'] = $ow->getIdentifier();
		$et->data['name'] = $ow->getName();
		$et->data['descr'] = $ow->getDescr();	
		$et->show();
		exit;
	}
	
	
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

} catch (Exception $e) {
	
	
	SimpleSAML_Utilities::fatalError((isset($session) ? $session->getTrackID() : null), null, $e);

}


?>