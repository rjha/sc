<?php
namespace com\indigloo\sc\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;
    use \com\indigloo\sc\Constants as AppConstants;

    class Activity {

        static function addRow($ownerId,$subjectId,$objectId,$subject,$object,$verb,$content) {

            $dbh = NULL ;
             
            try {

                
                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $sql = " insert into sc_activity(owner_id,subject_id,subject,object_id, " ;
                $sql .= " object,verb, verb_name, op_bit, content, created_on) " ;
                $sql .= " values(:owner_id, :subject_id, :subject, :object_id, " ;
                $sql .= " :object, :verb, :verb_name, :op_bit, :content, now()) ";
               
                
                $op_bit = 0 ;
                $verbName = NULL ;

                switch($verb) {
                    case AppConstants::LIKE_VERB :
                        $verbName = AppConstants::STR_LIKE ;
                        break ;
                    case AppConstants::SAVE_VERB :
                        $verbName = AppConstants::STR_SAVE ;
                        break ;
                    case AppConstants::COMMENT_VERB :
                        $verbName = AppConstants::STR_COMMENT ;
                        break ;
                    case AppConstants::FOLLOW_VERB :
                        $verbName = AppConstants::STR_FOLLOW ;
                        break ;
                    case AppConstants::UNFOLLOW_VERB :
                        $verbName = AppConstants::STR_UNFOLLOW ;
                        break ;
                    case AppConstants::POST_VERB :
                        $verbName = AppConstants::STR_POST ;
                        break ;
                    default :
                        $message = "Unknown activity verb : aborting! ";
                        trigger_error($message,E_USER_ERROR);
                }
                 
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(":owner_id", $ownerId);
                $stmt->bindParam(":subject_id", $subjectId);
                $stmt->bindParam(":object_id", $objectId);
                $stmt->bindParam(":subject", $subject);
                $stmt->bindParam(":object", $object);
                $stmt->bindParam(":verb", $verb);
                $stmt->bindParam(":verb_name", $verbName);
                $stmt->bindParam(":op_bit", $op_bit);
                $stmt->bindParam(":content", $content);

                $stmt->execute();
                $stmt = NULL ;
                
                //Tx end
                $dbh->commit();
                $dbh = null;
                

            }catch (\PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($e->getMessage(),$e->getCode());

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                $message = $ex->getMessage();
                throw new DBException($message);
            }
        }
    }
}
?>

