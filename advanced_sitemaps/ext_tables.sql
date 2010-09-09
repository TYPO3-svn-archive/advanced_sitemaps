#
# Table structure for table 'tx_advancedsitemaps_configurations'
#
CREATE TABLE tx_advancedsitemaps_configurations (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumtext,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	tablename tinytext,
	parent tinytext,
	url_params tinytext,
	sortby tinytext,
	sortby_dir tinytext,
	record_limit int(11) DEFAULT '0' NOT NULL,
	title_field tinytext,
	single_page text,
	startingpoint text,
	recursive tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);