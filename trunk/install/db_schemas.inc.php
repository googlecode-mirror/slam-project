<?php

#
# This file generates globals that contain the info necessary to create table schemas, etc.
#

#
# required tables
#
$sql_create_required=array();
$sql_create_required['researchers']="CREATE TABLE `SLAM_Researchers` (
	`username` varchar( 255 ) NOT NULL,
	`crypt` varchar( 40 ) NOT NULL,
	`salt` varchar( 8 ) NOT NULL,
	`email` varchar( 255 ) NOT NULL,
	`superuser` tinyint( 4 ) NOT NULL DEFAULT '0',
	`groups` varchar( 255 ) NOT NULL DEFAULT '',
	`prefs` text NOT NULL,
	PRIMARY KEY ( `username` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;";

$sql_create_required['categories']="CREATE TABLE `SLAM_Categories` (
	`Name` varchar( 255 ) NOT NULL,
	`Prefix` varchar( 2 ) NOT NULL,
	`List Fields` varchar( 255 ) NOT NULL DEFAULT 'Identifier,Researcher,Date,Files',
	`Title Field` varchar( 255 ) NOT NULL DEFAULT 'Identifier',
	`Owner Field` varchar( 255 ) NOT NULL DEFAULT 'Researcher',
	`Date Field` varchar( 255 ) NOT NULL DEFAULT 'Date',
	PRIMARY KEY ( `Name` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;";

#
# optional categories
#
$sql_create_optional = array();

$sql_create_optional['Template']=array();
$sql_create_optional['Template']['description']="A minimal category that can be used as the basis for creating custom categories.";
$sql_create_optional['Template']['prefix']='TP';
$sql_create_optional['Template']['checked']=true;
$sql_create_optional['Template']['sql']="CREATE TABLE `Template` (
	`Serial`			  mediumint( 9 )		NOT NULL AUTO_INCREMENT,
	`Identifier`	  varchar( 20 )		NOT NULL,
	`Permissions`	  varchar( 255 )		NOT NULL,
	`Removed`		  tinyint( 1 )			NOT NULL DEFAULT '0',
	`Project`		  varchar( 20 )		COMMENT 'The project this asset is used in',
	`Researcher`	  varchar( 255 )		COMMENT 'The person responsible for this asset',
	`Date`			  date					COMMENT 'The date this asset was generated/entered',
	`Notes`			  mediumtext			COMMENT 'General notes for this asset',
	`Files`			  text,
	PRIMARY KEY ( `Identifier` ),
	UNIQUE KEY `Serial` ( `Serial` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;";

$sql_create_optional['Samples']=array();
$sql_create_optional['Samples']['description']="A category for protein and RNA sample solutions";
$sql_create_optional['Samples']['prefix']="SP";
$sql_create_optional['Samples']['checked']=false;
$sql_create_optional['Samples']['sql']="CREATE TABLE `Samples` (
	`Serial`				mediumint( 9 )		NOT NULL AUTO_INCREMENT,
	`Identifier`		varchar( 255 )		NOT NULL,
	`Permissions`		varchar( 255 )		NOT NULL,
	`Removed`			tinyint( 1 )		NOT NULL DEFAULT '0',
	`Project`			varchar( 20 )		COMMENT 'The project this asset is used in',,
	`Label`				varchar( 255 )		COMMENT 'The physical label on this asset',
	`Researcher`		varchar( 255 )		COMMENT 'The person responsible for this asset',
	`Entered By`		varchar( 255 )		COMMENT 'The person who entered this asset',
	`Date`				date					COMMENT 'The date this asset was generated/entered',
	`Labbook Ref.`		varchar( 255 )		COMMENT 'The name and page of the labbook on which this asset was generated',
	`Storage Location`	varchar( 255 )	COMMENT 'The physical storage location of this asset',
	`Organism`			varchar( 255 )		COMMENT 'The original organism(s) of this asset',
	`Protein/RNA Name`	varchar( 255 )	COMMENT 'The name of the protein(s)/RNA(s) present in the sample',
	`Source`				varchar( 255 )		COMMENT '#link The source of this sample (e.g. Collaborator, identifier of another sample, etc.)',
	`Cell Strain`		varchar( 255 )		COMMENT '#link The identifier(s) of any cell strains used in the generation of this asset (e.g. BL21)',
	`Plasmid`			varchar( 20 )		COMMENT '#link The identifier of any plasmid(s) used to generate this sample',
	`Protocol`			varchar( 20 )		COMMENT '#link The identifier of any protocol(s) used in the generation of this asset',
	`Culture Size`		varchar( 255 )		DEFAULT '1 liter' COMMENT 'The size of the culture used in growing this sample',
	`Labeling`			varchar( 255 )		DEFAULT '15N' COMMENT 'The isotopes/compounds used in growing this sample',
	`Concentration`	varchar( 255 )		DEFAULT '0.00mM' COMMENT 'The concentration of this sample',
	`Volume`				varchar( 255 )		DEFAULT '0.00mL' COMMENT 'The volume of this sample',
	`Buffer Conditions`	varchar( 255 )	COMMENT 'The buffer conditions present in this sample',
	`Notes`				mediumtext			COMMENT 'General notes for this asset',
	`Files`				text,
	PRIMARY KEY ( `Identifier` ) ,
	UNIQUE KEY `Serial` ( `Serial` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;";

?>
