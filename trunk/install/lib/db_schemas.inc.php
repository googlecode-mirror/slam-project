<?php

#
# This file generates globals that contain the info necessary to create table schemas, etc.
#

#
# required tables
#
$sql_create_required=array();
$sql_create_required['researchers']=array();
$sql_create_required['researchers']['sql']="CREATE TABLE IF NOT EXISTS `SLAM_Researchers` (
	`username` varchar( 255 ) NOT NULL,
	`crypt` varchar( 40 ) NOT NULL,
	`salt` varchar( 8 ) NOT NULL,
	`email` varchar( 255 ) NOT NULL,
	`superuser` tinyint( 4 ) NOT NULL DEFAULT '0',
	`projects` varchar( 255 ) NOT NULL DEFAULT '',
	`prefs` text NOT NULL,
	PRIMARY KEY ( `username` )
) ENGINE = MyISAM DEFAULT CHARSET = latin1;
";

$sql_create_required['categories']=array();
$sql_create_required['categories']['sql']="CREATE TABLE IF NOT EXISTS `SLAM_Categories` (
  `Name` varchar(255) NOT NULL,
  `Prefix` varchar(2) NOT NULL,
  `List Fields` varchar(255) NOT NULL DEFAULT 'Identifier,Researcher,Date,Files',
  `Field Order` text NOT NULL,
  `Title Field` varchar(255) NOT NULL DEFAULT 'Identifier',
  `Owner Field` varchar(255) NOT NULL DEFAULT 'Researcher',
  `Date Field` varchar(255) NOT NULL DEFAULT 'Date',
  PRIMARY KEY (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
";

$sql_create_required['permissions']=array();
$sql_create_required['permissions']['sql']="CREATE TABLE IF NOT EXISTS `SLAM_Permissions` (
  `Identifier` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `owner_access` tinyint(4) NOT NULL DEFAULT '3',
  `projects` varchar(255) NOT NULL,
  `project_access` tinyint(4) NOT NULL DEFAULT '3',
  `default_access` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Identifier`),
  FULLTEXT KEY `Projects` (`projects`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$sql_create_required['projects']=array();
$sql_create_required['projects']['sql']="CREATE TABLE IF NOT EXISTS `SLAM_Projects` (
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
";

#
# optional categories
#
$sql_create_optional = array();

$sql_create_optional['Template']=array();
$sql_create_optional['Template']['description']="A minimal category that can be used as the basis for creating custom categories.";
$sql_create_optional['Template']['prefix']='TP';
$sql_create_optional['Template']['sql']="CREATE TABLE IF NOT EXISTS `Template` (
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
) ENGINE = MyISAM DEFAULT CHARSET = latin1 AUTO_INCREMENT=0 ;";

$sql_create_optional['Cell Strains']=array();
$sql_create_optional['Cell Strains']['description']="A category for glycerol stocks of bacteria containing valuable plasmids";
$sql_create_optional['Cell Strains']['prefix']="CS";
$sql_create_optional['Cell Strains']['sql']="CREATE TABLE IF NOT EXISTS `Cell Strains` (
  `Serial` int(11) NOT NULL AUTO_INCREMENT,
  `Identifier` varchar(20) NOT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0',
  `Project` varchar(20) NOT NULL,
  `Label` varchar(255) NOT NULL COMMENT 'The physical label on the asset',
  `Researcher` varchar(255) DEFAULT NULL COMMENT 'The person responsible for this asset',
  `Entered By` varchar(255) NOT NULL COMMENT 'The person who entered this asset',
  `Date` date DEFAULT NULL COMMENT 'The date this asset was generated/entered',
  `Labbook Ref.` varchar(255) DEFAULT NULL COMMENT 'The name and page of the labbook on which this asset was generated',
  `Storage Location` varchar(255) DEFAULT NULL COMMENT 'The physical storage location of this asset',
  `Type` enum('Glycerol Stock','Competent Cells','Other') NOT NULL DEFAULT 'Glycerol Stock',
  `Parent Cells` varchar(255) NOT NULL COMMENT '#link The identifier of the parent cell stock this asset was generated from',
  `Plasmid` varchar(255) NOT NULL COMMENT '#link The plasmid(s) present in this asset',
  `Notes` mediumtext NOT NULL,
  `Files` text,
  PRIMARY KEY (`Identifier`),
  UNIQUE KEY `Serial` (`Serial`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
";

$sql_create_optional['Samples']=array();
$sql_create_optional['Samples']['description']="A category for protein and RNA sample solutions.";
$sql_create_optional['Samples']['prefix']="SP";
$sql_create_optional['Samples']['sql']="CREATE TABLE IF NOT EXISTS `Samples` (
  `Serial` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Identifier` varchar(255) NOT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0',
  `Project` varchar(20) NOT NULL,
  `Label` varchar(255) NOT NULL COMMENT 'The physical label on this asset',
  `Researcher` varchar(255) DEFAULT NULL COMMENT 'The person responsible for this asset',
  `Entered By` varchar(255) NOT NULL COMMENT 'The person who entered this asset',
  `Date` date DEFAULT NULL COMMENT 'The date this asset was generated/entered',
  `Labbook Ref.` varchar(255) DEFAULT NULL COMMENT 'The name and page of the labbook on which this asset was generated',
  `Storage Location` varchar(255) DEFAULT NULL COMMENT 'The physical storage location of this asset',
  `Organism` varchar(255) NOT NULL COMMENT 'The original organism(s) of this asset',
  `Protein/RNA Name` varchar(255) NOT NULL COMMENT 'The name of the protein(s)/RNA(s) present in the sample',
  `Source` varchar(255) DEFAULT NULL COMMENT '#link The source of this sample (e.g. Collaborator, identifier of another sample, etc.)',
  `Cell Strain` varchar(255) DEFAULT NULL COMMENT '#link The identifier(s) of any cell strains used in the generation of this asset (e.g. BL21)',
  `Plasmid` varchar(20) DEFAULT NULL COMMENT '#link The identifier of any plasmid(s) used to generate this sample',
  `Protocol` varchar(20) DEFAULT NULL COMMENT '#link The identifier of any protocol(s) used in the generation of this asset',
  `Culture Size` varchar(255) NOT NULL DEFAULT '1 liter' COMMENT 'The size of the culture used in growing this sample',
  `Labeling` varchar(255) DEFAULT '15N' COMMENT 'The isotopes/compounds used in growing this sample',
  `Concentration` varchar(255) NOT NULL DEFAULT '0.00mM' COMMENT 'The concentration of this sample',
  `Volume` varchar(255) NOT NULL DEFAULT '0.00mL' COMMENT 'The volume of this sample',
  `Buffer Conditions` varchar(255) NOT NULL COMMENT 'The buffer conditions present in this sample',
  `Notes` mediumtext NOT NULL,
  `Files` text,
  PRIMARY KEY (`Identifier`),
  UNIQUE KEY `Serial` (`Serial`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
";

$sql_create_optional['NMR Samples']=array();
$sql_create_optional['NMR Samples']['description']="A category for NMR samples.";
$sql_create_optional['NMR Samples']['prefix']="NS";
$sql_create_optional['NMR Samples']['sql']="CREATE TABLE IF NOT EXISTS `NMR Samples` (
  `Serial` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Identifier` varchar(20) NOT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0',
  `Project` varchar(20) NOT NULL,
  `Label` varchar(255) NOT NULL COMMENT 'The physical label on the asset',
  `Researcher` varchar(255) DEFAULT NULL COMMENT 'The person responsible for this asset',
  `Entered By` varchar(255) NOT NULL COMMENT 'The person who entered this asset',
  `Date` date DEFAULT NULL COMMENT 'The date this asset was generated/entered',
  `Labbook Ref.` varchar(255) DEFAULT NULL COMMENT 'The name and page of the labbook on which this asset was generated',
  `Storage Location` varchar(255) DEFAULT NULL COMMENT 'The physical storage location of this asset',
  `Protocol` varchar(20) DEFAULT NULL COMMENT '#link The protocol(s) used to generate this asset',
  `Sample` varchar(20) DEFAULT NULL COMMENT '#link The sample(s) used in making this asset',
  `Isotopes` set('2H','13C','15N','Other') NOT NULL COMMENT 'Isotopes present in this sample',
  `Scheme` varchar(255) NOT NULL DEFAULT 'uniform 15N' COMMENT 'The labeling scheme used in this sample',
  `D2O` varchar(255) NOT NULL DEFAULT '10%' COMMENT 'The percentage of D2O present in the sample solution',
  `Concentration` varchar(255) NOT NULL DEFAULT 'mM' COMMENT 'The total protein/RNA concentration',
  `Buffer` varchar(255) NOT NULL DEFAULT 'Phosphate' COMMENT 'The buffer conditions used in the sample',
  `pH` varchar(255) NOT NULL DEFAULT '8.0' COMMENT 'The pH of the sample',
  `Tube Type` enum('Standard','Reduced Volume') NOT NULL DEFAULT 'Standard' COMMENT 'The type of tube this sample is in',
  `Notes` mediumtext NOT NULL,
  `Files` text,
  PRIMARY KEY (`Identifier`),
  UNIQUE KEY `Label` (`Label`),
  UNIQUE KEY `Serial` (`Serial`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
";

$sql_create_optional['Plasmids']=array();
$sql_create_optional['Plasmids']['description']="A category for plasmids.";
$sql_create_optional['Plasmids']['prefix']="PL";
$sql_create_optional['Plasmids']['sql']="CREATE TABLE IF NOT EXISTS `Plasmids` (
  `Serial` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Identifier` varchar(255) NOT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0',
  `Project` varchar(20) NOT NULL,
  `Label` varchar(255) NOT NULL COMMENT 'The physical label on the asset',
  `Researcher` varchar(255) NOT NULL COMMENT 'The person responsible for this asset',
  `Entered By` varchar(255) NOT NULL COMMENT 'The person who entered this asset',
  `Date` date DEFAULT NULL COMMENT 'The date this asset was generated/entered',
  `Labbook Ref.` varchar(255) DEFAULT NULL COMMENT 'The name and page of the labbook on which this asset was generated',
  `Storage Location` varchar(255) DEFAULT NULL COMMENT 'The physical storage location of this asset',
  `Vector Type` varchar(255) DEFAULT NULL COMMENT 'The name of the parent vector type (e.g. pET32)',
  `Original Plasmid` varchar(20) DEFAULT NULL COMMENT '#link The identifier of the parent plasmid, or plasmid name',
  `Source` varchar(255) DEFAULT NULL COMMENT 'Source of this plasmid, e.g. commercial, collaborator, etc.',
  `Plasmid Sequence` text,
  `Insert Sequence` text COMMENT 'The complete sequence of the insert',
  `Insert Type` enum('Protein','RNA','Other') DEFAULT NULL,
  `Insert Organism` varchar(255) NOT NULL COMMENT 'Organism this insert originated from',
  `Protein Product` varchar(255) DEFAULT NULL COMMENT '#link',
  `RNA Product` varchar(255) DEFAULT NULL COMMENT '#link The primers used in the generation of this plasmid',
  `Primers Used` text COMMENT '#link',
  `Restriction Sites` varchar(255) DEFAULT NULL COMMENT 'Restriction enzymes used in insertion',
  `Antibiotic Resistance` varchar(255) DEFAULT NULL COMMENT 'Antibiotic resistances used in this plasmid',
  `Expression System` varchar(255) DEFAULT 'T7' COMMENT 'The expression promoter system used, e.g. T7, lac, etc.',
  `Expression Conditions` varchar(255) DEFAULT NULL COMMENT 'Any special conditions required for expression',
  `Linearization REs` varchar(255) DEFAULT NULL COMMENT 'The restriction enzyme for linearizing the plasmid pre RNA transcription',
  `Storage Buffer` varchar(255) DEFAULT 'Elution Buffer' COMMENT 'Buffer this plasmid is stored in, e.g. water, EB, etc.',
  `Last Used` date DEFAULT NULL COMMENT 'Date this asset was last used',
  `Last Sequenced` date DEFAULT NULL COMMENT 'Date this asset was last sequenced',
  `Verified` enum('Y','N') DEFAULT 'Y' COMMENT 'Has the product of this plasmid been verified as correct?',
  `Notes` text,
  `Files` text,
  PRIMARY KEY (`Identifier`),
  UNIQUE KEY `Serial` (`Serial`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
";

$sql_create_optional['Primers']=array();
$sql_create_optional['Primers']['description']="A category for primers used in vector engineering.";
$sql_create_optional['Primers']['prefix']="PI";
$sql_create_optional['Primers']['sql']="CREATE TABLE IF NOT EXISTS `Primers` (
  `Serial` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Identifier` varchar(255) NOT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0',
  `Project` varchar(20) NOT NULL,
  `Label` varchar(255) NOT NULL COMMENT 'The physical label on the asset',
  `Researcher` varchar(255) NOT NULL COMMENT 'The person responsible for this asset',
  `Entered By` varchar(255) NOT NULL COMMENT 'The person who entered this asset',
  `Date` date NOT NULL COMMENT 'The date this asset was generated/entered',
  `Labbook Ref.` varchar(255) DEFAULT NULL COMMENT 'The name and page of the labbook on which this asset was generated',
  `Storage Location` varchar(255) DEFAULT NULL COMMENT 'The physical storage location of this asset',
  `Sequence` text NOT NULL COMMENT 'The primer sequence (5''-3'')',
  `Restriction Sites` varchar(255) NOT NULL COMMENT 'Restriction enzymes used to insert this primer',
  `Direction` enum('Forward','Reverse') NOT NULL DEFAULT 'Forward',
  `Partner` varchar(255) NOT NULL COMMENT '#link The partner primer of this asset',
  `Template(s)` text COMMENT '#link The plasmid this primer was used on',
  `Successful?` enum('','Yes','No') DEFAULT NULL COMMENT 'Was this insert/mutation successful?',
  `Children` text NOT NULL COMMENT '#link The identifier(s) of any product this primer was used to generate',
  `Notes` text,
  `Files` text,
  PRIMARY KEY (`Identifier`),
  UNIQUE KEY `Serial` (`Serial`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0  ;
";

$sql_create_optional['Protocols']=array();
$sql_create_optional['Protocols']['description']="An category for storing useful lab protocols.";
$sql_create_optional['Protocols']['prefix']="PR";
$sql_create_optional['Protocols']['sql']="CREATE TABLE IF NOT EXISTS `Protocols` (
  `Serial` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Identifier` varchar(255) NOT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0',
  `Project` varchar(20) NOT NULL,
  `Title` varchar(255) NOT NULL COMMENT 'The title of this protocol',
  `Researcher` varchar(255) DEFAULT NULL COMMENT 'The person responsible for this protocol',
  `Entered By` varchar(255) NOT NULL COMMENT 'The person who entered this protocol',
  `Date` date DEFAULT NULL COMMENT 'The date this protocol was generated/entered',
  `Labbook Ref.` varchar(255) DEFAULT NULL COMMENT 'The name and page of the labbook on which this protocol was generated',
  `Type` enum('Protein','RNA','Analysis','Instrumentation','Other') NOT NULL,
  `Version` varchar(255) NOT NULL COMMENT 'The current version of this protocol',
  `Previous Version` varchar(20) DEFAULT NULL COMMENT '#link The identifier of any previous protocols this one is to supercede',
  `Notes` mediumtext NOT NULL,
  `Files` text,
  PRIMARY KEY (`Identifier`),
  UNIQUE KEY `Serial` (`Serial`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
";

$sql_create_optional['Protein Types']=array();
$sql_create_optional['Protein Types']['description']="An experimental category for storing protein variants.";
$sql_create_optional['Protein Types']['prefix']="PT";
$sql_create_optional['Protein Types']['sql']="CREATE TABLE IF NOT EXISTS `Protein Types` (
  `Serial` int(11) NOT NULL AUTO_INCREMENT,
  `Identifier` varchar(255) NOT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0',
  `Project` varchar(20) NOT NULL,
  `Researcher` varchar(255) NOT NULL,
  `Entered By` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  `Labbook Ref.` varchar(255) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Type` enum('Wild-Type','Mutant','N/A') NOT NULL,
  `Organism` varchar(255) NOT NULL,
  `Mutations` varchar(255) NOT NULL,
  `Phenotype` varchar(255) NOT NULL,
  `Expression Vector` varchar(20) DEFAULT NULL COMMENT '#Plasmids',
  `Expression Protocol` varchar(20) DEFAULT NULL COMMENT '#Protocols',
  `Gene Sequence` text,
  `Uncleaved Sequence` text,
  `Uncleaved Attributes` text,
  `Mature Sequence` text,
  `Mature Attributes` text,
  `Cleavage Notes` text,
  `Storage Conditions` varchar(255) DEFAULT NULL,
  `Notes` text,
  `Files` text,
  PRIMARY KEY (`Identifier`),
  UNIQUE KEY `Serial` (`Serial`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
";

?>
