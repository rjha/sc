
--
-- category data
--


insert into sc_list(name,ui_order,code,display) values('CATEGORY',1, 'BABY', 'Baby/Kids');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',2, 'BOOK', 'Books');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',3, 'CLOTH', 'Clothes');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',4, 'FASHION', 'Fashion');

insert into sc_list(name,ui_order,code,display) values('CATEGORY',6, 'FFASHION', 'Fashion - Female');

insert into sc_list(name,ui_order,code,display) values('CATEGORY',7, 'HEALTH', 'Health / Fitness');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',8, 'HOME', 'Home + Interior');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',9, 'GADGET', 'Camera/Mobiles/Gadgets');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',10, 'COMPUTER', 'Computer/Laptops');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',11, 'OTHER', 'Others');


DROP TRIGGER IF EXISTS trg_answer_title;

delimiter //
CREATE TRIGGER trg_answer_title BEFORE INSERT ON sc_answer
    FOR EACH ROW
    BEGIN
	DECLARE p_title  varchar(128) ;
	SELECT title into p_title from sc_question where id = NEW.question_id ;
	set NEW.title = p_title ;

    END;//
delimiter ;

--
-- switch engine to InnoDB
--

--
-- 27 Feb 2012
--



drop table if exists sc_login;
create table sc_login(
	id int(11) NOT NULL auto_increment,
	name varchar(32) not null,
    provider varchar(16) not null,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


-- look @ DB - select user_name,email from sc_user, provider is 3mik
insert into sc_login(name,provider) values ('Rajeev Jha','3mik');

alter table sc_user add column login_id int ;
alter table sc_question add column login_id int ;
alter table sc_answer add column login_id int ;

--
-- update sc_user (login_id vs email)
-- 1 | jha.rajeev@gmail.com
--
--

update sc_user set login_id = 1 where email = 'jha.rajeev@gmail.com';
update sc_question set login_id = 1 where user_email = 'jha.rajeev@gmail.com';
update sc_answer set login_id = 1 where user_email = 'jha.rajeev@gmail.com';

--
-- verify first
-- repeat above for multiple users
-- drop user_email
--

alter table sc_question drop column user_email ;
alter table sc_answer drop column user_email ;

--
-- twitter user
--

drop table if exists sc_twitter;
create table sc_twitter(
	id int(11) NOT NULL auto_increment,
	twitter_id int(11) NOT NULL ,
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
	facebook_id int(11) NOT NULL ,
	login_id int(11) NOT NULL ,
	name varchar(32) not null,
	first_name varchar(32) ,
	last_name varchar(32) ,
	link varchar(128) ,
    gender varchar(8) ,
    email varchar(32) ,
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



--
-- Patch to convert tables to utf-8
--
alter database scdb character set utf8 collate utf8_general_ci ;

alter table sc_question convert to character set utf8 collate utf8_general_ci ;
alter table sc_answer convert to character set utf8 collate utf8_general_ci ;
alter table sc_media convert to character set utf8 collate utf8_general_ci ;
alter table sc_user convert to character set utf8 collate utf8_general_ci ;
alter table sc_list convert to character set utf8 collate utf8_general_ci ;
alter table sc_login convert to character set utf8 collate utf8_general_ci ;
alter table sc_twitter convert to character set utf8 collate utf8_general_ci ;
alter table sc_facebook convert to character set utf8 collate utf8_general_ci ;
alter table sc_feedback convert to character set utf8 collate utf8_general_ci ;

--
-- recreate the trigger
--
alter table sc_question drop column category_code;

alter table sc_question add column is_active int default 1 ;
alter table sc_answer add column is_active int default 1 ;



--
-- 03 March 2012 - DB change patch
--

drop table sc_list ;
alter table sc_question drop column user_name ;
alter table sc_answer drop column user_name ;

alter table sc_question drop column is_active ;
alter table sc_answer drop column is_active ;

alter table sc_question drop column seo_title ;

drop table if exists sc_post_archive;
create table sc_post_archive(
	id int(11) NOT NULL auto_increment,
    login_id int not null,
	title varchar(128) not null,
    description TEXT ,
    links_json TEXT ,
    images_json TEXT,
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;

drop table if exists sc_comment_archive;
create table sc_comment_archive(
	id int(11) NOT NULL auto_increment,
    login_id int not null,
	question_id int not null ,
	title varchar(128) ,
    answer TEXT ,
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;



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
CREATE TRIGGER trg_post_archive  BEFORE DELETE ON sc_question
    FOR EACH ROW
    BEGIN
        insert into sc_post_archive(title,description,location,tags,login_id,links_json,images_json)
        select q.title,q.description,q.location,q.tags,q.login_id,q.links_json,q.images_json
        from sc_question q where q.id = OLD.id ;
    END;//
delimiter ;


DROP TRIGGER IF EXISTS trg_comment_archive;

delimiter //
CREATE TRIGGER trg_comment_archive  BEFORE DELETE ON sc_answer
    FOR EACH ROW
    BEGIN
        insert into sc_comment_archive (login_id,question_id,title,answer)
        select a.login_id,a.question_id,a.title,a.answer from sc_answer a where a.id = OLD.id ;
    END;//
delimiter ;


--
-- 06 march 2012
--
alter table sc_media add column store varchar(8) not null default 'local' ;
alter table sc_media add column thumbnail varchar(64) ;


--
--       09 march 2012
--

--
-- drop column sc_question.category_code from server DB
--

alter table sc_question add column group_slug varchar(64);

--
-- 10 mar 2012
--



drop table if exists sc_user_group;
create table sc_user_group(
	id int(11) NOT NULL auto_increment,
    login_id int not null,
	token varchar(32) ,
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


alter table sc_user_group add constraint UNIQUE(login_id,token);





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
CREATE TRIGGER trg_user_group  AFTER  INSERT ON sc_question
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
CREATE TRIGGER trg_user_group2  AFTER  update ON sc_question
    FOR EACH ROW
    BEGIN
        DECLARE login_id INT ;
        DECLARE slug varchar(64) ;

        SET slug = NEW.group_slug ;
        SET login_id = NEW.login_id ;
        call fn_user_group(login_id,slug);

    END;//
delimiter ;


--
-- @13 mar 2012
--
-- @todo
-- 14 Mar 2012
-- 15 Mar 2012
--

alter table sc_question add column is_feature int default 0 ;
--
-- it is important to make this column NULLABLE
-- that way failed pseudo_id update do not interfere with record creation
--

alter table sc_question add column pseudo_id int  ;
alter table sc_question add constraint unique(pseudo_id) ;

alter table sc_answer change column question_id post_id int not null;
alter table sc_answer change column answer description varchar(512) ;

rename table sc_question to sc_post ;
rename table sc_answer to sc_comment;

alter table sc_comment_archive change column question_id post_id int not null;
alter table sc_comment_archive change column answer description varchar(512) ;


DROP TRIGGER IF EXISTS trg_answer_title;

delimiter //
CREATE TRIGGER trg_comment_title BEFORE INSERT ON sc_comment
    FOR EACH ROW
    BEGIN
	DECLARE p_title  varchar(128) ;
	SELECT title into p_title from sc_post where id = NEW.post_id ;
	set NEW.title = p_title ;

    END;//
delimiter ;


DROP TRIGGER IF EXISTS trg_post_archive;

delimiter //
CREATE TRIGGER trg_post_archive  BEFORE DELETE ON sc_post
    FOR EACH ROW
    BEGIN
        insert into sc_post_archive(title,description,location,tags,login_id,links_json,images_json)
        select q.title,q.description,q.location,q.tags,q.login_id,q.links_json,q.images_json
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


alter table sc_post modify column description varchar(512);

--
-- @todo now run the pseudo0id update DB script now
-- @todo verify on server
--

--
-- 17 March 2012
--

alter table sc_post drop column location ;
alter table sc_post drop column tags ;



--
-- 17 March
--


drop table if exists sc_feature_group;
create table sc_feature_group(
	id int(11) NOT NULL ,
	slug text ,
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


insert into sc_feature_group(id,slug) values(1,'');

--
-- 17 March B
--

alter table sc_post_archive drop column location ;
alter table sc_post_archive drop column tags ;
alter table sc_post_archive add column pseudo_id int;
alter table sc_post_archive add column group_slug varchar(64);


DROP TRIGGER IF EXISTS trg_post_archive;

delimiter //
CREATE TRIGGER trg_post_archive  BEFORE DELETE ON sc_post
    FOR EACH ROW
    BEGIN
        insert into sc_post_archive(title,description,login_id,links_json,images_json,group_slug,pseudo_id)
        select q.title,q.description,q.login_id,q.links_json,q.images_json,q.group_slug,q.pseudo_id
        from sc_post  q where q.id = OLD.id ;
    END;//
delimiter ;

alter table sc_post_archive add column is_feature int;


--
-- 18 Mar 2012
--


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


alter table sc_post add column cat_code varchar(16);
alter table sc_post_archive add column cat_code varchar(16);

insert into sc_list(name,ui_order,code,display) values('CATEGORY',1, 'BABY', 'Baby/Kids');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',2, 'BOOK', 'Books');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',3, 'CLOTH', 'Clothes');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',4, 'FASHION', 'Fashion');


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


--
-- 19 March 2012
--

alter table sc_list modify column code varchar(16) not null ;


delete from sc_list ;

insert into sc_list(name,ui_order,code,display) values('CATEGORY',2, 'BABY', 'Baby/Kids');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',3, 'BOOKM', 'Books/Media');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',4, 'CLOTH', 'Clothes/Accessories');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',7, 'FASHION', 'Fashion');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',8, 'Food', 'Food/Restaurants');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',5, 'GADGET', 'Computer/Mobiles/Gadgets');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',6, 'COOL', 'Cool/Unusual');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',9, 'HOMEI', 'Home/Interiors');
insert into sc_list(name,ui_order,code,display) values('CATEGORY',1, 'ARTC', 'Arts/Crafts');



--
-- 20 March 2012
--

update sc_list set code = 'FOOD' where name = 'CATEGORY' and ui_order =8 and  code = 'Food' ;
update sc_post set cat_code = 'FOOD' where cat_code = 'Food';

update sc_list set code = 'CLOTHA' where name = 'CATEGORY' and ui_order =4 and  code = 'CLOTH' ;
update sc_post set cat_code = 'CLOTHA' where cat_code = 'CLOTH';

update sc_list set code = 'COOLU' where name = 'CATEGORY' and ui_order =6 and  code = 'COOL' ;
update sc_post set cat_code = 'COOLU' where cat_code = 'COOL';

update sc_list set display = 'Fashion/Beauty' where name = 'CATEGORY' and ui_order =7 and  code = 'FASHION' ;
insert into sc_list(name,ui_order,code,display) values('CATEGORY',10, 'TRAVEL', 'Travel');

--
-- load categories data now
--




--
-- 23 March 2012
--


alter table sc_facebook modify column facebook_id varchar(64);
alter table sc_facebook modify column email varchar(64);
alter table sc_twitter modify column twitter_id varchar(64);


--
-- 26 Mar 2012
--


update sc_list set ui_order = 11 where code = 'TRAVEL';
insert into sc_list(name,ui_order,code,display) values('CATEGORY',10, 'RELIGION', 'Religion/Festivals');

--
-- 28 mar 2012
--

--
-- caution - never modify/touch the auto increment PK
--
 alter table sc_post modify column pseudo_id varchar(32) ;

 --
 -- 30 mar 2012
 --

 alter table sc_post_archive  modify column description varchar(512) ;



--
-- 05 April 2012
--

alter table sc_post add column version int default 1 ;

drop table if exists sc_site_tracker;
create table sc_site_tracker(
	id int NOT NULL auto_increment,
	post_id int NOT NULL ,
    version int not null,
    flag int default 0,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


--
-- populate site_tracker table
--

insert into sc_site_tracker (post_id,created_on,version,flag) select id,created_on,version, 0 from sc_post ;


--
-- Adjust triggers
--

DROP TRIGGER IF EXISTS trg_user_group;
DROP TRIGGER IF EXISTS trg_user_group2;




DROP TRIGGER IF EXISTS trg_post_add ;
delimiter //
CREATE TRIGGER trg_post_add  AFTER  INSERT ON sc_post
    FOR EACH ROW
    BEGIN
        DECLARE login_id INT ;
        DECLARE slug varchar(64) ;

        SET slug = NEW.group_slug ;
        SET login_id = NEW.login_id ;
        call fn_user_group(login_id,slug);

        --
        -- Add entry in site tracker
        --
        insert into sc_site_tracker(post_id,flag,version,created_on)
        values (NEW.ID,0,NEW.version,NEW.created_on);


    END;//
delimiter ;


DROP TRIGGER IF EXISTS trg_post_edit ;
delimiter //
CREATE TRIGGER trg_post_edit  AFTER  update ON sc_post
    FOR EACH ROW
    BEGIN
        DECLARE login_id INT ;
        DECLARE slug varchar(64) ;

        SET slug = NEW.group_slug ;
        SET login_id = NEW.login_id ;
        call fn_user_group(login_id,slug);

        update sc_site_tracker set flag=0, version=NEW.version, updated_on= now()
        where post_id = NEW.id ;
    END;//
delimiter ;


DROP TRIGGER IF EXISTS trg_post_archive;

delimiter //
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
    END; //
delimiter ;


--
-- Add new tables
--

drop table if exists sc_site_master;
create table sc_site_master(
	id int NOT NULL auto_increment,
	hash varchar(64) NOT NULL ,
	host varchar(64) NOT NULL ,
	canonical_url varchar(80) NOT NULL ,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;

alter table sc_site_master add constraint uniq_hash unique(hash);

drop table if exists sc_post_site ;
create table sc_post_site (
	id int NOT NULL auto_increment,
	post_id int NOT NULL ,
	site_id int NOT NULL ,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;



drop table if exists sc_tmp_ps ;
create table sc_tmp_ps (
	id int NOT NULL auto_increment,
	post_id int NOT NULL ,
	site_id int NOT NULL ,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;





delimiter //
DROP PROCEDURE IF EXISTS UPDATE_SITE_TRACKER //
CREATE PROCEDURE UPDATE_SITE_TRACKER (IN v_post_id int, IN v_version int)
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
    --
    -- set tracker flag
    --
    update sc_site_tracker set flag = 1 where post_id = v_post_id and version = v_version ;
    COMMIT;

END;//
delimiter ;



--
-- 18 April 2012
--


--
-- 1) Add group master table
--

drop table if exists sc_group_master;
create table sc_group_master(
	id int(11) NOT NULL auto_increment,
	token varchar(32),
	name varchar(32),
    cat_code varchar(16),
    created_on timestamp default '0000-00-00 00:00:00',
	updated_on timestamp default '0000-00-00 00:00:00' ,
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;

alter table sc_group_master add constraint UNIQUE(token);

--
-- @todo run cron scripts before dropping table
-- make sure site processing is not pending
-- 2) Add new flags to site tracker
--

drop table if exists sc_site_tracker;
create table sc_site_tracker(
	id int NOT NULL auto_increment,
	post_id int NOT NULL ,
    version int not null,
    site_flag int default 0,
    group_flag int default 0,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


--
-- 3) remove the function to process group_slugs
--

DROP PROCEDURE IF EXISTS fn_user_group ;

--
-- 4) change trigger on post add
--

DROP TRIGGER IF EXISTS trg_post_add ;
delimiter //
CREATE TRIGGER trg_post_add  AFTER  INSERT ON sc_post
    FOR EACH ROW
    BEGIN
        --
        -- Add entry in site tracker
        --
        insert into sc_site_tracker(post_id,site_flag,group_flag,version,created_on)
            values (NEW.ID,0,0,NEW.version,NEW.created_on);


    END;//
delimiter ;

--
-- 5) change trigger on post edit
--

DROP TRIGGER IF EXISTS trg_post_edit ;
delimiter //
CREATE TRIGGER trg_post_edit  AFTER  update ON sc_post
    FOR EACH ROW
    BEGIN
        --
        -- reset flags for offline processing
        --
        update sc_site_tracker set site_flag = 0, group_flag = 0, version = NEW.version, updated_on= now()
            where post_id = NEW.id ;
    END;//
delimiter ;


--
-- 6)use new site_flag
--


delimiter //
DROP PROCEDURE IF EXISTS UPDATE_SITE_TRACKER //
CREATE PROCEDURE UPDATE_SITE_TRACKER (IN v_post_id int, IN v_version int)
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
    --
    -- set site tracker flag
    --
    update sc_site_tracker set site_flag = 1 where post_id = v_post_id and version = v_version ;
    COMMIT;

END;//
delimiter ;


--
-- populate sc_site_tracker / No need to process sites
--

insert into sc_site_tracker (post_id,created_on,version,site_flag,group_flag)
    select id,created_on,version,1,0 from sc_post ;



--
-- 29 April 2012
--



drop table if exists sc_reset_password;
create table sc_reset_password(
	id int NOT NULL auto_increment,
	name varchar(64) not null ,
	email varchar(64) not null ,
	token varchar(64) not null ,
    flag int default 0,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
	expired_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


drop table if exists sc_php_session;
create table sc_php_session(
	session_id varchar(40),
	data TEXT,
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (session_id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;

--
-- 04 May 2012
--

drop table if exists sc_user_bookmark;
create table sc_user_bookmark(
	id int NOT NULL auto_increment,
	post_id int not null,
	login_id int not null,
    action int not null,
    created_on TIMESTAMP   default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


--
-- u1->u2 , u1 is following u2, u2 has follower u1
--
drop table if exists sc_follow;
create table sc_follow(
	id int NOT NULL auto_increment,
	follower_id int not null,
	following_id int not null,
    created_on TIMESTAMP   default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


--
-- 07 May 2012
--

alter table sc_user_bookmark change post_id item_id int not null ;

--
-- 12 May 2012
-- Add name column to sc_user_group
--

alter table sc_user_group add column name varchar(32) ;
update sc_user_group ug set ug.name = (select g.name from sc_group_master g where g.token = ug.token) ;
update sc_user_group set name = token where name is NULL;

--
-- 13 May 2012
-- Added thumbnail_name column to sc_media
--
alter table sc_media add column thumbnail_name varchar(256);

--
-- @next push
-- 17 May 2012
--

DROP TRIGGER IF EXISTS trg_post_archive;

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

--
-- diagnostic script for orphan comments
--
-- select id from sc_comment where post_id not in (select id from sc_post);
-- no orphan comments on server
-- move orphan comments to archive

--
-- change name column width
--
alter table sc_facebook modify name varchar(64);
alter table sc_twitter modify name varchar(64);


--
--
-- Add denorm_user table
--

drop table if exists sc_denorm_user;
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
    about_me varchar(512) ,
    gender varchar(1) ,
	created_on TIMESTAMP  default '0000-00-00 00:00:00',
    updated_on TIMESTAMP   default '0000-00-00 00:00:00',
	PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;

--
-- load data from sc_user, sc_facebook and sc_twitter tables
--

insert into sc_denorm_user(login_id,name,first_name,last_name,email,provider,created_on)
select u.login_id,u.user_name,u.first_name,u.last_name,u.email, '3mik', now() from sc_user u ;

insert into sc_denorm_user(login_id,name,first_name,last_name,email,provider,website,created_on)
select u.login_id,u.name,u.first_name,u.last_name,u.email, 'facebook', u.link, now() from sc_facebook u ;

insert into sc_denorm_user(login_id,name,nick_name,provider,photo_url,location,created_on)
select u.login_id,u.name,u.screen_name,'twitter',u.profile_image,u.location, now() from sc_twitter u ;


--
-- Add triggers to push data to sc_denorm_table on login creation
--


DROP TRIGGER IF EXISTS trg_mik_user_cp;

DELIMITER //
CREATE TRIGGER trg_mik_user_cp  BEFORE INSERT ON sc_user
    FOR EACH ROW
    BEGIN
        insert into sc_denorm_user(login_id,name,first_name,last_name,email,provider,created_on)
        values(NEW.login_id,NEW.user_name,NEW.first_name,NEW.last_name,NEW.email, '3mik', now());

    END //
DELIMITER ;


DROP TRIGGER IF EXISTS trg_fb_user_cp;

DELIMITER //
CREATE TRIGGER trg_fb_user_cp  BEFORE INSERT ON sc_facebook
    FOR EACH ROW
    BEGIN
        insert into sc_denorm_user(login_id,name,first_name,last_name,email,provider,website,created_on)
        values(NEW.login_id,NEW.name,NEW.first_name,NEW.last_name,NEW.email, 'facebook', NEW.link, now()) ;
    END //
DELIMITER ;


DROP TRIGGER IF EXISTS trg_twitter_user_cp;

DELIMITER //
CREATE TRIGGER trg_twitter_user_cp  BEFORE INSERT ON sc_twitter
    FOR EACH ROW
    BEGIN
        insert into sc_denorm_user(login_id,name,nick_name,provider,photo_url,location,created_on)
        values(NEW.login_id,NEW.name,NEW.screen_name,'twitter',NEW.profile_image,NEW.location, now()) ;
    END //
DELIMITER ;



--
-- 20 May 2012
--

DROP TRIGGER IF Exists trg_mik_user_name ;


DELIMITER //
 CREATE TRIGGER trg_mik_user_name AFTER UPDATE ON sc_denorm_user
    FOR EACH ROW
    BEGIN
        update sc_login set name = NEW.name where id = NEW.login_id ;
    END //
DELIMITER ;


 -- add age column
 alter table sc_denorm_user  add column age int;


--
-- 22 may 2012
--


DROP TRIGGER IF Exists trg_mik_user_name ;

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


--
-- 23 may
--
--

alter table sc_facebook modify column facebook_id varchar(64) not null ;
alter table sc_twitter modify column twitter_id varchar(64) not null ;


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
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY ( id ),
  UNIQUE KEY  uniq_id  ( google_id )
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DELIMITER //
CREATE TRIGGER trg_google_user_cp  BEFORE INSERT ON sc_google_user
    FOR EACH ROW
    BEGIN
        insert into sc_denorm_user(login_id,name,first_name,last_name,email,provider,photo_url,created_on)
        values(NEW.login_id,NEW.name,NEW.first_name,NEW.last_name,NEW.email, 'google', NEW.photo,now()) ;
    END //
DELIMITER ;

--
-- 01 june 2012
--
--


--
-- step1: Add new categories / preserve the old fixed_id
--

alter table sc_list add column fixed_id int ;
update sc_list set fixed_id = ui_order ;

insert into sc_list(name,code,display,fixed_id,ui_order)
    values('CATEGORY', 'CAR', 'Cars/Automobiles', 12, 12);

insert into sc_list(name,code,display,fixed_id,ui_order)
    values('CATEGORY', 'OTHER', 'Other', 13, 13);


update sc_list set ui_order = ui_order +1 where name = 'CATEGORY' and ui_order > 3 ;
update sc_list set ui_order = 4 where name = 'CATEGORY' and code = 'CAR' ;



--
-- step 2: create new table
--

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
-- step 3: run db-to-redis script
-- Transfer data from sc_user_bookmark => sc_bookmark first
-- Transfer data from sc_bookmark and sc_follow => REDIS
--
-- step 4:
-- DROP TABLE IF EXISTS  sc_user_bookmark ;
--

-- 
-- 12 June 2012
--

alter table sc_feedback add column name varchar(64) ;
alter table sc_feedback add column email varchar(64) ;
alter table sc_feedback add column phone varchar(32) ;
alter table sc_feedback modify feedback varchar(512);


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



--
-- 31 Aug 2012
-- @changes for adding oauth access_token to sc_login
--

alter table sc_login add column  access_token text ;
alter table sc_login add column  expire_on  timestamp  DEFAULT '0000-00-00 00:00:00' ;

--
-- changes to mark post and comment columns as not null
--

alter table sc_post modify column description varchar(512) not null ;
alter table sc_post modify column login_id int not null ;
alter table sc_post modify column pseudo_id varchar(32) not null ;

alter table sc_post_archive modify column description varchar(512) not null ;
alter table sc_post_archive modify column login_id int not null ;
alter table sc_post_archive modify column pseudo_id varchar(32) not null ;


alter table sc_comment modify column description varchar(512) not null ;
alter table sc_comment modify column title  varchar(128) not null ;
alter table sc_comment modify column login_id int  not null ;


alter table sc_comment_archive modify column description varchar(512) not null ;
alter table sc_comment_archive modify column title  varchar(128) not null ;
alter table sc_comment_archive modify column login_id int  not null ;


--
-- 09 oct 2012
-- changes to track login : ip_address and session_id
-- 
-- ipv4 -> ipv6 would require 45 chars
-- ABCD:ABCD:ABCD:ABCD:ABCD:ABCD:192.168.158.190
-- 

--
-- ip_address when records are created
--

 alter table sc_user add column ip_address varchar(46) ;
 alter table sc_facebook add column ip_address varchar(46) ;
 alter table sc_twitter add column ip_address varchar(46) ;

 alter table sc_google_user add column ip_address varchar(46) ;
 alter table sc_denorm_user add column ip_address varchar(46) ;
 alter table sc_login add column ip_address varchar(46) ;

--
-- 14 oct. 2012
--

alter table sc_post drop column is_feature ;
alter table sc_post add column fp_bit  int  default 0 ;

alter table sc_denorm_user add column bu_bit int default 0 ;
alter table sc_denorm_user add column tu_bit int default 0 ;
alter table sc_login add column session_id varchar(40);


--
-- change sc_reset_password table name
--

rename table sc_reset_password to sc_mail_queue ;
alter table sc_mail_queue add column source int default 0 ;


--
-- Add data structures
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

-- 
-- copy category data from sc_list to sc_ui_zset
--

insert into  sc_ui_zset(name,ui_code,ui_order,seo_key,set_key,set_hash)
    select c.display, 
    c.code,c.ui_order, 
    c.fixed_id,
    "ui:zset:category", 
    unhex(md5("ui:zset:category"))
    from sc_list c ;

--
-- finally drop the old table 
-- 
-- @drop table sc_list ;
-- 


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
-- populate old sc_feature_group data 
--

insert into sc_glob_table(
    t_key,
    t_hash,
    t_value,
    created_on)
    select "glob:sys:fgroups", 
        unhex(md5("glob:sys:fgroups")), 
        slug , 
        now()
    from sc_feature_group;

-- 
-- transfer the preference data 
-- key is glob:user:login_id:preference

insert into sc_glob_table(t_key,t_hash,t_value,created_on)
select concat("glob:user:",login_id,":preference"), 
        unhex(md5(concat("glob:user:",login_id,":preference"))),
        p_data, now() 
        from sc_preference;


--
--
-- @drop sc_preference
-- @drop sc_feature_group
-- 


--
-- recreate sc_denorm_user_copy triggers
--


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
-- 26 october 2012
--


DROP TABLE IF EXISTS  sc_list ;
CREATE TABLE  sc_list  (
   id  int NOT NULL AUTO_INCREMENT,
   login_id  int NOT NULL,
   name varchar(64) NOT NULL,
   md5_name varchar(32) NOT NULL,
   bin_md5_name BINARY(16) not null,
   user_name varchar(64) not null,
   items_json TEXT,
   num_item int not null,
   version int not null,
   op_bit int not null,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


alter table sc_list add constraint unique uniq_name(login_id,bin_md5_name);


DROP TABLE IF EXISTS  sc_list_item ;
CREATE TABLE  sc_list_item  (
   id  int NOT NULL AUTO_INCREMENT,
   list_id  int NOT NULL,
   item_id int not null ,
   dup_bit int default 0,
   created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
   PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;

alter table sc_list_item add constraint unique uniq_item(list_id,item_id);


