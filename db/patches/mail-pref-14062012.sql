
--
-- 14 June, 2012
-- 
-- preferences table
-- 

DROP TABLE IF EXISTS  sc_preference ;
CREATE TABLE  sc_preference (
   id  int NOT NULL AUTO_INCREMENT,
   login_id  int NOT NULL,
   p_data varchar(512) not null,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

alter table  sc_preference add constraint UNIQUE uniq_login (login_id);


