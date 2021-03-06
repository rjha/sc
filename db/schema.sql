
DROP TABLE IF EXISTS  sc_comment ;
CREATE TABLE  sc_comment  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   post_id  int(11) NOT NULL,
   description  varchar(512) not null,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   title  varchar(128) not null,
   login_id  int(11) not null ,
  PRIMARY KEY ( id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



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
   title  varchar(128) not null,
   description  varchar(512) not null,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  sc_facebook ;
CREATE TABLE  sc_facebook  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   facebook_id  varchar(64) NOT NULL ,
   login_id  int(11) NOT NULL,
   name  varchar(64) NOT NULL,
   first_name  varchar(32) ,
   last_name  varchar(32) ,
   link  varchar(128) ,
   gender  varchar(8) ,
   email  varchar(64) ,
   ip_address varchar(46),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_id  ( facebook_id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TRIGGER IF EXISTS  trg_fb_user_cp ;

DELIMITER //
CREATE TRIGGER trg_fb_user_cp  BEFORE INSERT ON sc_facebook
    FOR EACH ROW
    BEGIN
        insert into sc_denorm_user(
            login_id,
            name,
            first_name,
            last_name,
            email,
            provider,
            website,
            ip_address,
            created_on)
        values(
            NEW.login_id,
            NEW.name,
            NEW.first_name,
            NEW.last_name,
            NEW.email,
            'facebook', 
            NEW.link, 
            NEW.ip_address,
            now()) ;

        insert into sc_mail_queue(name,email,source,created_on)
        values(NEW.name,NEW.email,2,now());

    END //
DELIMITER ;


DROP TABLE IF EXISTS  sc_feedback ;
CREATE TABLE  sc_feedback  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   name varchar(64),
   email varchar(64),
   phone varchar(32),
   feedback  varchar(512) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
   token  varchar(32) ,
   name  varchar(32) ,
   cat_code  varchar(16) ,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  token  ( token )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  sc_login ;
CREATE TABLE  sc_login  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   name  varchar(32) NOT NULL,
   provider  varchar(16) NOT NULL,
   access_token text ,
   ip_address varchar(46),
   session_id varchar(40),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   expire_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_media ;
CREATE TABLE  sc_media  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   original_name  varchar(256) NOT NULL,
   thumbnail_name  varchar(256) NOT NULL,
   stored_name  varchar(64) NOT NULL,
   bucket  varchar(32) NOT NULL,
   size  int(11) NOT NULL,
   mime  varchar(64) NOT NULL,
   original_height  int(11) ,
   original_width  int(11) ,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   store  varchar(8) NOT NULL DEFAULT 'local',
   thumbnail  varchar(64) ,
  PRIMARY KEY ( id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
   description  varchar(512) not null ,
   links_json  mediumtext,
   images_json  mediumtext,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   login_id  int(11) not null ,
   group_slug  varchar(64) ,
   fp_bit  int(11) DEFAULT '0',
   pseudo_id  varchar(32) not null,
   cat_code  varchar(16) ,
   version  int(11) DEFAULT '1',
  PRIMARY KEY ( id ),
  UNIQUE KEY  pseudo_id  ( pseudo_id )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
        delete from sc_comment where post_id = OLD.id;
    END //
DELIMITER ;

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

DROP TABLE IF EXISTS  sc_post_archive ;
CREATE TABLE  sc_post_archive  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   login_id  int(11) NOT NULL,
   title  varchar(128) NOT NULL,
   description  varchar(512) not null ,
   links_json  text,
   images_json  text,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   pseudo_id  int(11) not null,
   group_slug  varchar(64) ,
   fp_bit  int(11) ,
   cat_code  varchar(16) ,
  PRIMARY KEY ( id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_post_site ;
CREATE TABLE  sc_post_site  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   post_id  int(11) NOT NULL,
   site_id  int(11) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_mail_queue ;
CREATE TABLE  sc_mail_queue  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   name  varchar(64) NOT NULL,
   email  varchar(64) NOT NULL,
   token  varchar(64) NOT NULL,
   flag  int(11) DEFAULT 0,
   source int default 1 ,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   expired_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  sc_tmp_ps ;
CREATE TABLE  sc_tmp_ps  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   post_id  int(11) NOT NULL,
   site_id  int(11) NOT NULL,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS  sc_twitter ;
CREATE TABLE  sc_twitter  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   twitter_id  varchar(64) ,
   login_id  int(11) NOT NULL,
   name  varchar(64) NOT NULL,
   screen_name  varchar(32) ,
   profile_image  varchar(128) ,
   location  varchar(32) ,
   ip_address varchar(46),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_id  ( twitter_id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TRIGGER IF EXISTS  trg_twitter_user_cp ;

DELIMITER //
CREATE TRIGGER trg_twitter_user_cp  BEFORE INSERT ON sc_twitter
    FOR EACH ROW
    BEGIN
        insert into sc_denorm_user(
            login_id,
            name,
            nick_name,
            provider,
            photo_url,
            location,
            ip_address,
            created_on)
        values(
            NEW.login_id,
            NEW.name,
            NEW.screen_name,
            'twitter',
            NEW.profile_image,
            NEW.location,
            NEW.ip_address, 
            now()) ;
    END //
DELIMITER ;


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
   ip_address varchar(46),
   login_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   login_id  int(11) ,
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_email  ( email )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TRIGGER IF EXISTS  trg_mik_user_cp ;

DELIMITER //
CREATE TRIGGER trg_mik_user_cp  BEFORE INSERT ON sc_user
    FOR EACH ROW
    BEGIN
        insert into sc_denorm_user(
            login_id,
            name,
            first_name,
            last_name,
            email,
            provider,
            ip_address,
            created_on)
        values(
            NEW.login_id,
            NEW.user_name,
            NEW.first_name,
            NEW.last_name,
            NEW.email,
            '3mik',
            NEW.ip_address,
            now());

        insert into sc_mail_queue(name,email,source,created_on)
        values(NEW.user_name,NEW.email,2,now());


    END //
DELIMITER ;



DROP TABLE IF EXISTS  sc_user_group ;
CREATE TABLE  sc_user_group  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   login_id  int(11) NOT NULL,
   token  varchar(32) NOT  NULL,
   name  varchar(32)  ,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  login_id  ( login_id , token )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP PROCEDURE IF EXISTS  UPDATE_SITE_TRACKER ;

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



--
-- Add denorm_user table
--

create table sc_denorm_user(
    id int(11) NOT NULL auto_increment,
    login_id int not null,
	name varchar(64) not null ,
	nick_name varchar(32) ,
    first_name  varchar(32) ,
    last_name  varchar(32) ,
    email  varchar(64) ,
    provider varchar(16) NOT NULL,
    website varchar(128) ,
    blog varchar(128) ,
    photo_url varchar(128) ,
    location varchar(32) ,
    about_me varchar(512),
    age int ,
    bu_bit int default 0,
    tu_bit int default 0,
    gender varchar(1) ,
    ip_address varchar(46),
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;
	


DELIMITER //
 CREATE TRIGGER trg_mik_user_name AFTER UPDATE ON sc_denorm_user
    FOR EACH ROW
    BEGIN
        --
        -- nickname takes precedence over name
        --
        IF (NEW.nick_name is NULL || NEW.nick_name = "" ) THEN
            update sc_login set name = NEW.name where id = NEW.login_id ;
        ELSE
            update sc_login set name = NEW.nick_name where id = NEW.login_id ;
        END IF;

    END //
DELIMITER ;



DROP TABLE IF EXISTS  sc_google_user ;
CREATE TABLE  sc_google_user  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   google_id  varchar(64) NOT NULL,
   login_id  int(11) NOT NULL,
   name  varchar(64) NOT NULL,
   first_name  varchar(32) ,
   last_name  varchar(32) ,
   photo  varchar(128) ,
   email  varchar(64) ,
   ip_address varchar(46),
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_id  ( google_id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;




DROP TRIGGER IF EXISTS trg_google_user_cp;

DELIMITER //
CREATE TRIGGER trg_google_user_cp  BEFORE INSERT ON sc_google_user
    FOR EACH ROW
    BEGIN
        insert into sc_denorm_user(
            login_id,
            name,
            first_name,
            last_name,
            email,
            provider,
            photo_url,
            ip_address,
            created_on)
        values(
            NEW.login_id,
            NEW.name,
            NEW.first_name,
            NEW.last_name,
            NEW.email,
            'google', 
            NEW.photo,
            NEW.ip_address,
            now()) ;
        -- 
        -- source for new a/c mail :2 
        --
        insert into sc_mail_queue(name,email,source,created_on)
        values(NEW.name,NEW.email,2,now());


    END //
DELIMITER ;


DROP TABLE IF EXISTS  sc_bookmark ;
CREATE TABLE  sc_bookmark (
   id  int NOT NULL AUTO_INCREMENT,
   owner_id  int NOT NULL,
   subject varchar(32),
   subject_id int not null,
   object varchar(16) not null ,
   object_id int not null,
   object_title varchar(128),
   verb int not null,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id )) ENGINE=InnoDB  DEFAULT CHARSET=utf8;




--
-- for sc_ui_zset 
-- ui_order is the score associated with a member in zset (sorted set)
-- 
-- Question - what do we need seo_key column? 
-- 
-- The URL for category navigation is like 
-- /category/1 , /category/2 etc.
-- 
-- 1) we cannnot use ui_order in SEO URL because that obviously can change.
-- 2) we cannot use ui_code or name either because that can also change, like
-- what we call code CAR today can be code AUTO tomorrow and then all 
-- /category/CAR link will not work
-- 
-- 3) we cannot use DB primary key/ set hash etc in  in SEO URL 
--  + looks bad, not readable (/category/6E707C49D1FDCF7E4288EEB27E0158E6 
--  + rows can be deleted, can start from N when migrating 
--  to other DB etc. 
--  
-- That is why we need another column to track what we print in SEO URL
-- that can be ported to any DB irrespective of internal implementation.
-- 
--  
-- 



drop table if exists sc_ui_zset;
create table sc_ui_zset(
    id int(11) NOT NULL auto_increment,
    name varchar(32) not null,
    ui_code varchar(16) not null,
    ui_order int not null ,
    seo_key varchar(16) not null,
    set_hash BINARY(16) not null,
    set_key varchar(32) not null,
    created_on timestamp default '0000-00-00 00:00:00',
    updated_on timestamp default '0000-00-00 00:00:00' ,
    PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;

--
-- indexes
--

alter table sc_ui_zset add constraint UNIQUE uniq_key(set_key,seo_key);


DROP TABLE IF EXISTS  sc_set ;

CREATE TABLE  sc_set (
  id  int(11) NOT NULL AUTO_INCREMENT,
  set_hash BINARY(16) not null,
  set_key varchar(32) not null,
  member varchar(64) not null,
  member_hash  BINARY(16) not null,
  created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS  sc_glob_table ;

CREATE TABLE  sc_glob_table (
  t_key varchar(32) not null,
  t_hash BINARY(16) not null,
  t_value text not null,
  created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (t_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- indexes
-- 

alter table sc_post add index idx_fp_bit (fp_bit) ;
alter table sc_post add index idx_login (login_id) ;
alter table sc_post add index idx_cat (cat_code) ;
alter table sc_post add index idx_date(created_on) ;

alter table sc_comment add index idx_login (login_id);
alter table sc_comment add index idx_post (post_id);

alter table sc_facebook add index idx_id(facebook_id);
alter table sc_google_user add index idx_id (google_id);
alter table sc_twitter add index idx_id (twitter_id);
alter table sc_user add index idx_login (login_id);

alter table sc_mail_queue add index idx_email(email);


alter table sc_denorm_user add index id_login (login_id) ;
alter table sc_denorm_user add index idx_email (email) ;
alter table sc_denorm_user add index idx_date (created_on) ;
alter table sc_denorm_user add index idx_ban_bit (bu_bit) ;
alter table sc_denorm_user add index idx_taint_bit (tu_bit) ;


alter table sc_set add index idx_key(set_key) ;
alter table sc_set add index idx_smhash (set_hash,member_hash) ;

alter table sc_glob_table add index idx_key(t_key) ;
alter table sc_ui_zset add index idx_key(set_key) ;

alter table sc_bookmark add index idx_sub_verb(subject_id,verb) ;
alter table sc_follow add index idx_following(following_id) ;
alter table sc_follow add index idx_follower(follower_id) ;

alter table sc_login add index idx_session(session_id) ;
alter table sc_login add index idx_date(created_on);

alter table sc_site_master add index idx_hash(hash);

alter table sc_tmp_ps add index idx_post_id(post_id) ;
alter table sc_tmp_ps add index idx_site_id (site_id) ;

alter table sc_post_site add index idx_post_id(post_id) ;
alter table sc_post_site add index idx_site_id (site_id) ;




--
-- missing foreign keys
-- need to take a call on foreign keys
-- foreign keys may have performance implications!
--


-- alter table sc_comment add constraint foreign key(post_id)  references sc_post(id);
--
-- alter table sc_facebook add constraint foreign key(login_id)  references sc_login(id);
-- alter table sc_twitter add constraint foreign key(login_id)  references sc_login(id);
-- alter table sc_google_user add constraint foreign key(login_id)  references sc_login(id);
-- alter table sc_user add constraint foreign key(login_id)  references sc_login(id);
-- alter table sc_denorm_user add constraint foreign key(login_id)  references sc_login(id);
--
-- alter table sc_follow add constraint foreign key(follower_id)  references sc_login(id);
-- alter table sc_follow add constraint foreign key(following_id)  references sc_login(id);
--
--
-- alter table sc_post_site add constraint foreign key(post_id)  references sc_post(id);
-- alter table sc_post_site add constraint foreign key(site_id)  references sc_site_master(id);
-- alter table sc_tmp_ps add constraint foreign key(post_id)  references sc_post(id);
-- alter table sc_tmp_ps add constraint foreign key(site_id)  references sc_site_master(id);
--
-- alter table sc_site_tracker add constraint foreign key(post_id)  references sc_post(id);
-- alter table sc_user_group add constraint foreign key(login_id)  references sc_login(id);

-- alter table sc_list_item add constraint foreign key(list_id)  references sc_list(id);
-- alter table sc_list_item add constraint foreign key(item_id)  references sc_post(id);
