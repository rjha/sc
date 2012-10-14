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

