<?php

namespace com\indigloo\sc\mysql {

    use com\indigloo\mysql as MySQL;

    class Media {

        static function getMediaOnPostId($postId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($postId,"integer");

            $sql = " select * from sc_media where post_id = %d " ;
            $sql = sprintf($sql,$postId);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function deleteOnId($mediaId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($mediaId,"integer");

            $sql = " delete from sc_media where id = ? ";
            MySQL\Helper::executeSQL($mysqli,$sql);

            $stmt = $mysqli->prepare($sql);
            if($stmt) {
                $stmt->bind_param("i",$mediaId);
                $stmt->execute();
                if($mysqli->affected_rows != 1)  {
                    MySQL\Error::handle($stmt);
                }

                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function add($mediaVO) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $mediaId = NULL ;

            $sql = " insert into sc_media(bucket,original_name, stored_name, " ;
            $sql .= " size,mime, original_height, original_width,created_on,store,thumbnail) ";
            $sql .= " values(?,?,?,?,?,?,?,now(),?,?) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sssisiiss",
                        $mediaVO->bucket,
                        $mediaVO->originalName,
                        $mediaVO->storeName,
                        $mediaVO->size,
                        $mediaVO->mime,
                        $mediaVO->height,
                        $mediaVO->width,
                        $mediaVO->store,
                        $mediaVO->thumbnail);


                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

            $mediaId = MySQL\Connection::getInstance()->getLastInsertId();
            return $mediaId;
        }

    }

}
?>
