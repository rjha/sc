x=`date +%d%m%Y`
mysqldump  --complete-insert --add-drop-table   --triggers  --routines -u root -p scdb > scdb.full.$x.sql


