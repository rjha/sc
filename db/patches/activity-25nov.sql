

DROP TABLE IF EXISTS  sc_activity ;
CREATE TABLE  sc_activity  (
   id  int NOT NULL AUTO_INCREMENT,
   subject_id int not NULL,
   object_id  int NOT NULL,
   owner_id int ,
   subject varchar(128),
   object varchar(128),
   verb_name varchar(16) not null,
   verb int not null,
   source varchar(16),
   content varchar(512) ,
   op_bit int default 0,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- clean sc_activity table now
-- run scripts/move-activity.php
-- 
-- clean (flushdb) redis store
-- run maintenance/db-to-redis.php
-- set sc_activity.op_bit = 1 so we do not load this data again
-- Install new cron scripts
--
 


DROP TABLE IF EXISTS  sc_email_capture ;
CREATE TABLE  sc_email_capture  (
   id  int NOT NULL AUTO_INCREMENT,
   email varchar(64),
   message varchar(512) ,
   op_bit int default 0,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


