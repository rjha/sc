


DROP TABLE IF EXISTS  app_login ;
CREATE TABLE  app_login  (
   id  int NOT NULL AUTO_INCREMENT,
   name  varchar(32) NOT NULL,
   source  int default 1,
   access_token text ,
   ip_address varchar(46),
   session_id varchar(40),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   expire_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  app_facebook_user ;
CREATE TABLE  app_facebook_user  (
   id  int NOT NULL AUTO_INCREMENT,
   facebook_id  varchar(64) NOT NULL ,
   login_id  int(11) NOT NULL,
   name  varchar(64) NOT NULL,
   first_name  varchar(32) ,
   last_name  varchar(32) ,
   email  varchar(64) ,
   ip_address varchar(46),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_id  ( facebook_id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  app_source ;
CREATE TABLE  app_source  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   login_id int not null,
   source_id  varchar(64) NOT NULL ,
   source_type int default 1,
   name varchar(64) not null,
   unix_ts varchar(16), 
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  UNIQUE KEY  uniq_id  (source_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  app_comment ;
CREATE TABLE  app_comment  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   login_id int not null,
   source_id  varchar(64) NOT NULL ,
   unix_ts varchar(64),
   
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id),
  UNIQUE KEY  uniq_id  (source_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



