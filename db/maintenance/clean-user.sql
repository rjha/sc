--  
-- to clean a user 
-- select login_id, email,provider  from sc_denorm_user where email = 'rjha94@gmail.com' ;
-- select login_id  from sc_twitter where screen_name = 'rjha94' ;
-- 

delete from sc_comment where login_id= 441 ; 
delete from sc_post where login_id= 441 ;

delete from   sc_user_group where login_id= 441 ;
delete from   sc_glob_table  where t_key = concat("glob:user:",441,":preference") ;

delete from sc_bookmark where subject_id = 441 ;
delete from sc_follow where follower_id = 441 ;
delete from sc_follow where following_id = 441 ;


delete from sc_list where login_id = 441 ;
delete from sc_facebook where login_id= 441 ;
delete from sc_twitter where login_id= 441 ;
delete from   sc_google_user where login_id= 441 ;
delete from   sc_denorm_user where login_id= 441 ;
delete from   sc_user where login_id= 441 ;
delete from sc_login where id = 441 ;
