--  
-- to clean a user 
-- 
delete from sc_comment where login_id= 41 ; 
delete from sc_post where login_id= 41 ;

delete from   sc_user_group where login_id= 41 ;
delete from   sc_glob_table  where t_key = concat("glob:user:",41,":preference") ;

	
delete from sc_facebook where login_id= 41 ;
delete from sc_twitter where login_id= 41 ;
delete from   sc_google_user where login_id= 41 ;
delete from   sc_denorm_user where login_id= 41 ;
delete from   sc_user where login_id= 41 ;
delete from sc_login where id = 41 ;
