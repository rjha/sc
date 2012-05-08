x=`date +%d%m%Y`
mysqldump --no-data --add-drop-table --triggers --routines -u root -p webgloodb  > webgloodb.schema.$x.sql

