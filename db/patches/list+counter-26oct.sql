
--
-- 26 october 2012
-- 
-- patch to create lists and site counters
-- 
--


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


DROP TABLE IF EXISTS  sc_list ;
CREATE TABLE  sc_list  (
    id  int NOT NULL AUTO_INCREMENT,
    login_id  int NOT NULL,
    name varchar(64) NOT NULL,
    seo_name varchar(64) not null,
    md5_name varchar(32) NOT NULL,
    bin_md5_name BINARY(16) not null,
    items_json TEXT,
    item_count int default 0,
    pseudo_id varchar(32) ,
    description varchar(512),
    version int not null,
    op_bit int not null,
    dl_bit int default 0,
    created_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    updated_on  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


alter table sc_list add constraint unique uniq_name(login_id,bin_md5_name);
alter table sc_list add constraint unique uniq_pid(pseudo_id);




DROP TABLE IF EXISTS  sc_site_counter ;
CREATE TABLE  sc_site_counter  (
    id  int NOT NULL AUTO_INCREMENT,
    post_count int  default 0,
    comment_count int  default 0 ,
    user_count int  default 0,
    list_count int  default 0 ,
    PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;




DROP TABLE IF EXISTS  sc_user_counter ;
CREATE TABLE  sc_user_counter  (
   id  int NOT NULL AUTO_INCREMENT,
   login_id  int NOT NULL,
   post_count int default 0,
   comment_count int default 0 ,
   like_count int default 0 ,
   list_count int default 0,
   follower_count int default 0,
   following_count int default 0,
   PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;


DROP TABLE IF EXISTS  sc_post_counter ;
CREATE TABLE  sc_post_counter  (
   id  int NOT NULL AUTO_INCREMENT,
   post_id  int NOT NULL,
   comment_count int default 0,
   like_count int  default 0,
   list_count int  default 0,
   PRIMARY KEY (id)) ENGINE = InnoDB default character set utf8 collate utf8_general_ci;



--
-- update triggers to use counters
--

--
-- new triggers
-- 

drop trigger if exists trg_post_del ;
drop trigger if exists trg_comment_add ;
drop trigger if exists trg_comment_del ;
drop trigger if exists trg_bookmark_add ;
drop trigger if exists trg_bookmark_del ;
drop trigger if exists trg_follow_del ;
drop trigger if exists trg_follow_add ;
drop trigger if exists trg_list_add;
drop trigger if exists trg_list_del;


--
-- old triggers
-- 

DROP TRIGGER IF EXISTS  trg_post_archive ;
DROP TRIGGER IF EXISTS  trg_post_add ;
DROP TRIGGER IF EXISTS  trg_comment_title;
DROP TRIGGER IF EXISTS  trg_comment_archive ;
DROP TRIGGER IF EXISTS  trg_fb_user_cp  ;
DROP TRIGGER IF EXISTS  trg_twitter_user_cp ;  
DROP TRIGGER IF EXISTS  trg_mik_user_cp ;
DROP TRIGGER IF EXISTS  trg_google_user_cp ;





DELIMITER //
CREATE TRIGGER trg_post_del  BEFORE DELETE ON sc_post
    FOR EACH ROW
    BEGIN
        -- no offline processing 
        delete from sc_site_tracker where post_id = OLD.id ;
        delete from sc_comment where post_id = OLD.id;
        
        -- update counters
        delete from sc_post_counter where post_id = OLD.id ;
        update sc_user_counter set post_count = post_count + 1 where login_id = OLD.login_id ;
        update sc_site_counter set post_count = post_count - 1 ;

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


DELIMITER //
 CREATE TRIGGER trg_post_add  AFTER  INSERT ON sc_post
 FOR EACH ROW
    BEGIN
      -- signal offline process
        insert into sc_site_tracker(post_id,site_flag,group_flag,version,created_on)
        values (NEW.ID,0,0,NEW.version,NEW.created_on);
      -- update counters
      insert into sc_post_counter (post_id) values(NEW.id);
      update sc_user_counter set post_count = post_count + 1 where login_id = NEW.login_id ;
      update sc_site_counter set post_count = post_count + 1 ;

    END //
DELIMITER ;




DELIMITER //
CREATE  TRIGGER trg_comment_add BEFORE INSERT ON sc_comment
    FOR EACH ROW
    BEGIN
        DECLARE p_title  varchar(128) ;
        SELECT title into p_title from sc_post where id = NEW.post_id ;
        set NEW.title = p_title ;

        -- update counters
        update sc_post_counter set comment_count = comment_count + 1 where post_id = NEW.post_id ;
        update sc_user_counter set comment_count = comment_count + 1 where login_id = NEW.login_id ;
        update sc_site_counter set comment_count = comment_count + 1 ;

    END //

DELIMITER ;



DELIMITER //
CREATE TRIGGER trg_comment_del  BEFORE DELETE ON sc_comment
    FOR EACH ROW
    BEGIN
        insert into sc_comment_archive (login_id,post_id,title,description)
        select a.login_id,a.post_id,a.title,a.description from sc_comment a where a.id = OLD.id ;
        -- update counters
        update sc_post_counter set comment_count = comment_count - 1 where post_id = OLD.post_id ;
        update sc_user_counter set comment_count = comment_count - 1 where login_id = OLD.login_id ;
        update sc_site_counter set comment_count = comment_count - 1 ;
    END //

DELIMITER ;




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

        --
        -- mail queue
        -- 
        insert into sc_mail_queue(name,email,source,created_on)
        values(NEW.name,NEW.email,2,now());
        --
        -- counters
        -- 
        insert into sc_user_counter (login_id) values(NEW.login_id);  
        update sc_site_counter set user_count = user_count + 1 ;  

        
        

    END //
DELIMITER ;



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

            -- update counter
            insert into sc_user_counter (login_id) values(NEW.login_id);  
            update sc_site_counter set user_count = user_count + 1 ;  
    END //
DELIMITER ;



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

        -- update counter
        insert into sc_user_counter (login_id) values(NEW.login_id);  
        update sc_site_counter set user_count = user_count + 1 ;  


    END //
DELIMITER ;





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

        -- update counter
        insert into sc_user_counter (login_id) values(NEW.login_id);  
        update sc_site_counter set user_count = user_count + 1 ;


    END //
DELIMITER ;





DELIMITER //
    CREATE TRIGGER trg_follow_add  AFTER  INSERT ON sc_follow
    FOR EACH ROW
    BEGIN
      -- update counters
      update sc_user_counter set following_count = following_count + 1 where login_id = NEW.follower_id ;
      update sc_user_counter set follower_count = follower_count + 1 where login_id = NEW.following_id ;
    END //
DELIMITER ;


DELIMITER //
    CREATE TRIGGER trg_follow_del  BEFORE  DELETE ON sc_follow
    FOR EACH ROW
    BEGIN
      -- update counters
      update sc_user_counter set following_count = following_count - 1 where login_id = OLD.follower_id ;
      update sc_user_counter set follower_count = follower_count - 1 where login_id = OLD.following_id ;
    END //
DELIMITER ;



DELIMITER //
    CREATE TRIGGER trg_bookmark_add  AFTER  INSERT ON sc_bookmark
    FOR EACH ROW
    BEGIN
        -- update counters
        IF (NEW.verb = 1 ) THEN 
              update sc_user_counter set like_count = like_count + 1 where login_id = NEW.subject_id ;
              update sc_post_counter set like_count = like_count + 1 where post_id = NEW.object_id ;
        END IF;
        

    END //
DELIMITER ;



DELIMITER //
    CREATE TRIGGER trg_bookmark_del  BEFORE DELETE  ON sc_bookmark
    FOR EACH ROW
    BEGIN
        -- update counters
        IF (OLD.verb = 1 ) THEN 
              update sc_user_counter set like_count = like_count - 1 where login_id = OLD.subject_id ;
              update sc_post_counter set like_count = like_count - 1 where post_id = OLD.object_id ;
        END IF;
        

    END //
DELIMITER ;





DELIMITER //
    CREATE TRIGGER trg_list_add  AFTER  INSERT ON sc_list
    FOR EACH ROW
    BEGIN
      -- update counters
      update sc_site_counter set list_count = list_count + 1 ;
      update sc_user_counter set list_count = list_count + 1 where login_id = NEW.login_id ;
    END //
DELIMITER ;


DELIMITER //
    CREATE TRIGGER trg_list_del  BEFORE  DELETE ON sc_list
    FOR EACH ROW
    BEGIN
        -- update counters
        update sc_site_counter set list_count = list_count - 1 ;
        update sc_user_counter set list_count = list_count - 1 where login_id = OLD.login_id ;
        delete from sc_list_item where list_id = old.id ;
    END //
DELIMITER ;


DELIMITER //
    CREATE TRIGGER trg_list_item_add  AFTER  INSERT ON sc_list_item
    FOR EACH ROW
    BEGIN
      -- update counters
      update sc_list set item_count = item_count + 1  where id = NEW.list_id;
    END //
DELIMITER ;






-- 
-- seed counter tables 
--

insert into sc_site_counter(id,post_count)
    select 1 , count(id) from sc_post ;

update sc_site_counter set comment_count = (select count(id) from sc_comment)  where id = 1 ;
update sc_site_counter set user_count = (select count(id) from sc_login)  where id = 1 ;
update sc_site_counter set list_count = 0  where id = 1 ;

insert into sc_user_counter (login_id) 
    select id from sc_login ;


insert into sc_post_counter(post_id)
    select id from sc_post ;



--
-- @run move-favorites script to move data from 
-- sc_bookmark table to sc_list_item table.
-- set dl_bit
-- update sc_list set dl_bit = 1 where name = 'Favorites' ;
-- 


--
-- @run seed-counters.php script 
-- 


-- 
-- cleanup
-- after running the script - clean sc_bookmark.verb = 2 rows
-- 
-- delete from sc_bookmark where verb = 2 ;
-- 
