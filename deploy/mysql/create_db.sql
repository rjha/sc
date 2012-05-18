
create database scdb  character set utf8 collate utf8_general_ci ;
grant all privileges on scdb.* to 'gloo'@'localhost' identified by 'password'
with grant option;
