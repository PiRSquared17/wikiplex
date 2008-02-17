<?php

$access = array(
	0 => 'Private',
	1 => 'All feide users can read, no anonymous access',
	2 => 'Anonymous users can read',
	3 => 'Feide users can write, no anonymous access',
	4 => 'Feide users can write, anonymous users can read'
);

$groups = array(
	'@realm-uninett.no'	=> 'Everyone at UNINETT',
	'@affiliation-uninett.no-employee' => 'Employees at UNINETT',
	'@affiliation-uninett.no-member' => 'Members of UNINETT',
	'@orgunit-uninett.no-ou=SU_ou=TA_ou=UNINETT_ou=organization_dc=uninett_dc=no' => 'Systemutviklingsgruppa',
	'@orgunit-uninett.no-ou=TA_ou=UNINETT_ou=organization_dc=uninett_dc=no' => 'Tjenesteavdelingen',
	'@orgunit-uninett.no-ou=UNINETT_Sigma_ou=organization_dc=uninett_dc=no' => 'Employees UNINETT Sigma',
	'@feidecore' => 'Feide prosjektgruppe',
	'@realm-uio.no'	=> 'Everyone at UiO',
	'@realm-ntnu.no'=> 'Everyone at NTNU',
	'@realm-uit.no'	=> 'Everyone at UiT',
	'@realm-uib.no'	=> 'Everyone at UiB',
	'@entitlement-orphanage.dk_aai.dk-dk.dk_aai.orphanage.dev' => 'DK-AAI Utviklingsgruppe'
);