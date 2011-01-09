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
	andWhere text,
	gs_template tinytext,
	gs_priority char(3) DEFAULT '' NOT NULL,
	gs_changeFreq varchar(16) DEFAULT '' NOT NULL,
	gsn_dateField varchar(255) DEFAULT '' NOT NULL,
	gsn_keywordsField varchar(255) DEFAuLT '' NOT NULL,
	gsn_stockTickerField varchar(255) DEFAuLT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE pages (
	tx_advancedsitemaps_priority char(3) DEFAULT '' NOT NULL,
	tx_advancedsitemaps_changeFreq varchar(16) DEFAULT '' NOT NULL
);

CREATE TABLE tx_advancedsitemaps_configurations_records (
	`table_name` varchar(255) DEFAULT '' NOT NULL,
	`record_uid` int(11) DEFAULT '0' NOT NULL,
	`dataType` varchar(24) DEFAULT '' NOT NULL,
	`value` varchar(16) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (table_name,record_uid,`type`)
);