


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


