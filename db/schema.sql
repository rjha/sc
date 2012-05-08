
DROP TABLE IF EXISTS  sc_comment ;
CREATE TABLE  sc_comment  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   post_id  int(11) NOT NULL,
   description  varchar(512) DEFAULT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   title  varchar(128) DEFAULT NULL,
   login_id  int(11) DEFAULT NULL,
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

DELIMITER //
CREATE  TRIGGER trg_comment_title BEFORE INSERT ON sc_comment
   FOR EACH ROW
   BEGIN
	 DECLARE p_title  varchar(128) ;
	 SELECT title into p_title from sc_post where id = NEW.post_id ;
	 set NEW.title = p_title ;
    END //

DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_comment_archive  BEFORE DELETE ON sc_comment
    FOR EACH ROW
    BEGIN
        insert into sc_comment_archive (login_id,post_id,title,description)
        select a.login_id,a.post_id,a.title,a.description from sc_comment a where a.id = OLD.id ; 
    END //

DELIMITER ;


DROP TABLE IF EXISTS  sc_comment_archive ;
CREATE TABLE  sc_comment_archive  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   login_id  int(11) NOT NULL,
   post_id  int(11) NOT NULL,
   title  varchar(128) DEFAULT NULL,
   description  varchar(512) DEFAULT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  sc_facebook ;
CREATE TABLE  sc_facebook  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   facebook_id  varchar(64) DEFAULT NULL,
   login_id  int(11) NOT NULL,
   name  varchar(32) NOT NULL,
   first_name  varchar(32) DEFAULT NULL,
   last_name  varchar(32) DEFAULT NULL,
   link  varchar(128) DEFAULT NULL,
   gender  varchar(8) DEFAULT NULL,
   email  varchar(64) DEFAULT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_id  ( facebook_id )
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_feature_group ;
CREATE TABLE  sc_feature_group  (
   id  int(11) NOT NULL,
   slug  text,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  sc_feedback ;
CREATE TABLE  sc_feedback  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   feedback  mediumtext NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_follow ;
CREATE TABLE  sc_follow  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   follower_id  int(11) NOT NULL,
   following_id  int(11) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_group_master ;
CREATE TABLE  sc_group_master  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   token  varchar(32) DEFAULT NULL,
   name  varchar(32) DEFAULT NULL,
   cat_code  varchar(16) DEFAULT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  token  ( token )
) ENGINE=InnoDB AUTO_INCREMENT=5467 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_list ;
CREATE TABLE  sc_list  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   name  varchar(16) NOT NULL,
   code  varchar(16) NOT NULL,
   display  varchar(32) NOT NULL,
   ui_order  int(11) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  sc_login ;
CREATE TABLE  sc_login  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   name  varchar(32) NOT NULL,
   provider  varchar(16) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_media ;
CREATE TABLE  sc_media  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   original_name  varchar(256) NOT NULL,
   stored_name  varchar(64) NOT NULL,
   bucket  varchar(32) NOT NULL,
   size  int(11) NOT NULL,
   mime  varchar(64) NOT NULL,
   original_height  int(11) DEFAULT NULL,
   original_width  int(11) DEFAULT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   store  varchar(8) NOT NULL DEFAULT 'local',
   thumbnail  varchar(64) DEFAULT NULL,
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=3225 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_php_session ;
CREATE TABLE  sc_php_session  (
   session_id  varchar(40) NOT NULL DEFAULT '',
   data  text,
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( session_id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 

DROP TABLE IF EXISTS  sc_post ;
CREATE TABLE  sc_post  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   title  varchar(128) NOT NULL,
   description  varchar(512) DEFAULT NULL,
   links_json  mediumtext,
   images_json  mediumtext,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   login_id  int(11) DEFAULT NULL,
   group_slug  varchar(64) DEFAULT NULL,
   is_feature  int(11) DEFAULT '0',
   pseudo_id  varchar(32) DEFAULT NULL,
   cat_code  varchar(16) DEFAULT NULL,
   version  int(11) DEFAULT '1',
  PRIMARY KEY ( id ),
  UNIQUE KEY  pseudo_id  ( pseudo_id )
) ENGINE=InnoDB AUTO_INCREMENT=1911 DEFAULT CHARSET=utf8;

DELIMITER //
 CREATE TRIGGER trg_post_add  AFTER  INSERT ON sc_post
 FOR EACH ROW
    BEGIN
        insert into sc_site_tracker(post_id,site_flag,group_flag,version,created_on) 
            values (NEW.ID,0,0,NEW.version,NEW.created_on);

    END //
DELIMITER ;

DELIMITER //
 CREATE  TRIGGER trg_post_edit  AFTER  update ON sc_post
    FOR EACH ROW
    BEGIN
        update sc_site_tracker set site_flag = 0, group_flag = 0, version = NEW.version, updated_on= now() 
            where post_id = NEW.id ;
    END  //
DELIMITER ;

DELIMITER //

CREATE TRIGGER trg_post_archive  BEFORE DELETE ON sc_post
    FOR EACH ROW
    BEGIN
        delete from sc_site_tracker where post_id = OLD.id ;
        insert into sc_post_archive(title,
                                    description,
                                    login_id,
                                    links_json,
                                    images_json,
                                    group_slug,
                                    pseudo_id,
                                    cat_code,
                                    created_on)
        select q.title,
                q.description,
                q.login_id,
                q.links_json,
                q.images_json,
                q.group_slug,
                q.pseudo_id,
                q.cat_code,
                q.created_on
        from sc_post  q where q.id = OLD.id ; 
    END //
DELIMITER ;


DROP TABLE IF EXISTS  sc_post_archive ;
CREATE TABLE  sc_post_archive  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   login_id  int(11) NOT NULL,
   title  varchar(128) NOT NULL,
   description  varchar(512) DEFAULT NULL,
   links_json  text,
   images_json  text,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   pseudo_id  int(11) DEFAULT NULL,
   group_slug  varchar(64) DEFAULT NULL,
   is_feature  int(11) DEFAULT NULL,
   cat_code  varchar(16) DEFAULT NULL,
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
 

DROP TABLE IF EXISTS  sc_post_site ;
CREATE TABLE  sc_post_site  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   post_id  int(11) NOT NULL,
   site_id  int(11) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=2111 DEFAULT CHARSET=utf8;
 

DROP TABLE IF EXISTS  sc_reset_password ;
CREATE TABLE  sc_reset_password  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   name  varchar(64) NOT NULL,
   email  varchar(64) NOT NULL,
   token  varchar(64) NOT NULL,
   flag  int(11) DEFAULT '0',
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   expired_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_site_master ;
CREATE TABLE  sc_site_master  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   hash  varchar(64) NOT NULL,
   host  varchar(64) NOT NULL,
   canonical_url  varchar(80) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_hash  ( hash )
) ENGINE=InnoDB AUTO_INCREMENT=311 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_site_tracker ;
CREATE TABLE  sc_site_tracker  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   post_id  int(11) NOT NULL,
   version  int(11) NOT NULL,
   site_flag  int(11) DEFAULT '0',
   group_flag  int(11) DEFAULT '0',
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=2130 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_tmp_ps ;
CREATE TABLE  sc_tmp_ps  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   post_id  int(11) NOT NULL,
   site_id  int(11) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=2051 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_twitter ;
CREATE TABLE  sc_twitter  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   twitter_id  varchar(64) DEFAULT NULL,
   login_id  int(11) NOT NULL,
   name  varchar(32) NOT NULL,
   screen_name  varchar(32) DEFAULT NULL,
   profile_image  varchar(128) DEFAULT NULL,
   location  varchar(32) DEFAULT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_id  ( twitter_id )
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
 

DROP TABLE IF EXISTS  sc_user ;
CREATE TABLE  sc_user  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   user_name  varchar(64) NOT NULL,
   password  varchar(64) NOT NULL,
   first_name  varchar(32) NOT NULL,
   last_name  varchar(32) NOT NULL,
   email  varchar(64) NOT NULL,
   is_staff  int(11) DEFAULT '0',
   is_admin  int(11) DEFAULT '0',
   is_active  int(11) NOT NULL DEFAULT '1',
   salt  varchar(16) NOT NULL,
   login_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   login_id  int(11) DEFAULT NULL,
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_email  ( email )
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

DELIMITER //
 CREATE TRIGGER trg_mik_user_name AFTER UPDATE ON sc_user
    FOR EACH ROW
    BEGIN
        update sc_login set name = NEW.user_name where id = NEW.login_id ;
    END //
DELIMITER ;


DROP TABLE IF EXISTS  sc_user_bookmark ;
CREATE TABLE  sc_user_bookmark  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   item_id  int(11) NOT NULL,
   login_id  int(11) NOT NULL,
   action  int(11) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
 

DROP TABLE IF EXISTS  sc_user_group ;
CREATE TABLE  sc_user_group  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   login_id  int(11) NOT NULL,
   token  varchar(32) DEFAULT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  login_id  ( login_id , token )
) ENGINE=InnoDB AUTO_INCREMENT=17593 DEFAULT CHARSET=utf8;
 

DROP PROCEDURE IF EXISTS  UPDATE_SITE_TRACKER  
DELIMITER //
CREATE PROCEDURE  UPDATE_SITE_TRACKER (IN v_post_id int, IN v_version int)
BEGIN
    DECLARE EXIT HANDLER for SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    DECLARE EXIT HANDLER for SQLWARNING
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    delete from sc_post_site where post_id = v_post_id ;
    insert into sc_post_site(post_id,site_id,created_on)  
    select post_id,site_id,now() from sc_tmp_ps where post_id = v_post_id ;
    
    
    
    update sc_site_tracker set site_flag = 1 where post_id = v_post_id and version = v_version ;
    COMMIT; 

END //
DELIMITER ;