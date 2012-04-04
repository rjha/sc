

-- create database scdb  character set utf8 collate utf8_general_ci ;

drop table if exists sc_post;
create table sc_post(
	id int(11) NOT NULL auto_increment,
    login_id int not null,
	title varchar(128) not null,
    description varchar(512),
    links_json TEXT ,
    images_json TEXT,
    group_slug varchar(64),
    cat_code varchar(16),
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;
    
drop table if exists sc_comment;
create table sc_comment(
	id int(11) NOT NULL auto_increment,
    login_id int not null,
	post_id int not null ,
	title varchar(128) ,
    description varchar(512),
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;
    
   
 
drop table if exists sc_media;
create table sc_media(
	id int(11) NOT NULL auto_increment,
	original_name varchar(256) not null,
    stored_name varchar(64) not null,
    bucket varchar(32) not null,
	size int not null ,
    mime varchar(64) not null,
    store varchar(8) not null default 'local',
    original_height int,
    original_width int ,
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;
    


  drop table if exists sc_user;
   CREATE TABLE sc_user (
       id int(11) NOT NULL auto_increment,
       login_id int not null,
       user_name varchar(64) not null,
       password varchar(64) not null,
       first_name varchar(32) not null,
       last_name varchar(32) not null,
       email varchar(64) not null,
       is_staff int default 0 ,
       is_admin int default 0,
       is_active int not null default 1,
       salt varchar(16) not null,
       login_on TIMESTAMP  default '0000-00-00 00:00:00',
       created_on TIMESTAMP  default '0000-00-00 00:00:00',
       updated_on TIMESTAMP   default '0000-00-00 00:00:00',
       PRIMARY KEY (id)) ENGINE =InnoDB  default character set utf8 collate utf8_general_ci;
    
   alter table  sc_user add constraint UNIQUE uniq_email (email);
   
 
drop table if exists sc_login;
create table sc_login(
	id int(11) NOT NULL auto_increment,
	name varchar(32) not null,
    provider varchar(16) not null,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


drop table if exists sc_twitter;
create table sc_twitter(
	id int(11) NOT NULL auto_increment,
	twitter_id varchar(64) NOT NULL ,
	login_id int(11) NOT NULL ,
	name varchar(32) not null,
	screen_name varchar(32) ,
	profile_image varchar(128) ,
    location varchar(32) ,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


alter table  sc_twitter add constraint UNIQUE uniq_id (twitter_id);

--
-- facebook user 
-- 

drop table if exists sc_facebook;
create table sc_facebook(
	id int(11) NOT NULL auto_increment,
	facebook_id varchar(64) NOT NULL ,
	login_id int(11) NOT NULL ,
	name varchar(32) not null,
	first_name varchar(32) ,
	last_name varchar(32) ,
	link varchar(128) ,
    gender varchar(8) ,
    email varchar(64) ,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


alter table  sc_facebook add constraint UNIQUE uniq_id (facebook_id);




drop table if exists sc_feedback;
create table sc_feedback(
	id int(11) NOT NULL auto_increment,
	feedback text not null,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;
 

drop table if exists sc_post_archive;
create table sc_post_archive(
	id int(11) NOT NULL auto_increment,
    login_id int not null,
	title varchar(128) not null,
    description varchar(512) ,
    links_json TEXT ,
    images_json TEXT,
    pseudo_id varchar(32) ,
    is_feature int ,
    group_slug varchar(64),
    cat_code varchar(16),
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;
    
drop table if exists sc_comment_archive;
create table sc_comment_archive(
	id int(11) NOT NULL auto_increment,
    login_id int not null,
	post_id int not null ,
	title varchar(128) ,
    description varchar(512) ,
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;



   
drop table if exists sc_user_group;
create table sc_user_group(
	id int(11) NOT NULL auto_increment,
    login_id int not null,
	token varchar(32) ,
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


alter table sc_user_group add constraint UNIQUE(login_id,token);
    

drop table if exists sc_list;
create table sc_list(
    id int(11) NOT NULL auto_increment,
    name varchar(16) not null,
    code varchar(16) not null,
    display varchar(32) not null,
    ui_order int not null ,
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;



--
-- Triggers 
--

DROP TRIGGER IF EXISTS trg_comment_title;

delimiter //
CREATE TRIGGER trg_comment_title BEFORE INSERT ON sc_comment
    FOR EACH ROW
    BEGIN
	DECLARE p_title  varchar(128) ;
	SELECT title into p_title from sc_post where id = NEW.post_id ;
	set NEW.title = p_title ;
	
    END;//
delimiter ;


DROP TRIGGER IF EXISTS trg_mik_user_name;

delimiter //
CREATE TRIGGER trg_mik_user_name AFTER UPDATE ON sc_user
    FOR EACH ROW
    BEGIN
        update sc_login set name = NEW.user_name where id = NEW.login_id ;
    END;//
delimiter ;



DROP TRIGGER IF EXISTS trg_post_archive;

delimiter //
CREATE TRIGGER trg_post_archive  BEFORE DELETE ON sc_post
    FOR EACH ROW
    BEGIN
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
    END;//
delimiter ;



DROP TRIGGER IF EXISTS trg_comment_archive;

delimiter //
CREATE TRIGGER trg_comment_archive  BEFORE DELETE ON sc_comment
    FOR EACH ROW
    BEGIN
        insert into sc_comment_archive (login_id,post_id,title,description)
        select a.login_id,a.post_id,a.title,a.description from sc_comment a where a.id = OLD.id ; 
    END;//
delimiter ;



delimiter //
DROP PROCEDURE IF EXISTS fn_user_group//
CREATE PROCEDURE fn_user_group( IN login_id int, IN slug varchar(64))
BEGIN
    DECLARE cur_position INT DEFAULT 1 ;
    DECLARE cur_string VARCHAR(64);
    DECLARE remainder varchar(64);

    --
    -- split slug on comma and push in sc_user_group
    --  

    SET remainder = slug;
    
    WHILE CHAR_LENGTH(remainder) > 0 AND cur_position > 0 DO
        --
        -- delimiter is space
        --
        SET cur_position = INSTR(remainder,' ');
        IF cur_position = 0 THEN
            SET cur_string = remainder;
        ELSE
            SET cur_string = LEFT(remainder, cur_position - 1);
        END IF;
        IF TRIM(cur_string) != '' THEN
            -- 
            -- 
            insert ignore into sc_user_group(login_id,token,created_on) values(login_id,cur_string,now());
        END IF;
        SET remainder = SUBSTRING(remainder, cur_position + 1);
    END WHILE;  


END;
//
delimiter ;


DROP TRIGGER IF EXISTS trg_user_group;

delimiter //
CREATE TRIGGER trg_user_group  AFTER  INSERT ON sc_post
    FOR EACH ROW
    BEGIN
        DECLARE login_id INT ;
        DECLARE slug varchar(64) ;

        SET slug = NEW.group_slug ;
        SET login_id = NEW.login_id ;
        call fn_user_group(login_id,slug);

    END;//
delimiter ;


DROP TRIGGER IF EXISTS trg_user_group2;

delimiter //
CREATE TRIGGER trg_user_group2  AFTER  update ON sc_post
    FOR EACH ROW
    BEGIN
        DECLARE login_id INT ;
        DECLARE slug varchar(64) ;

        SET slug = NEW.group_slug ;
        SET login_id = NEW.login_id ;
        call fn_user_group(login_id,slug);

    END;//
delimiter ;










  


