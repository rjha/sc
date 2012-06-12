

-- 
-- 12 June 2012
--

alter table sc_feedback add column name varchar(64) ;
alter table sc_feedback add column email varchar(64) ;
alter table sc_feedback add column phone varchar(32) ;
alter table sc_feedback modify feedback varchar(512);



