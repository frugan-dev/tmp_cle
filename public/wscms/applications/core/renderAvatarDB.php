<?php

/*	framework siti html-PHP-Mysql	copyright 2011 Roberto Mantovani	http://www.robertomantovani.vr;it	email: me@robertomantovani.vr.it	core/renderAvatarDB.php v.2.5.0. UTF-8 02/09/2015
*/

define('PATH', '../');
include_once(PATH.'include/configuration.inc.php');
include_once(PATH.'include/connectionDB.inc.php');

if (isset($_GET['id'])) {
    $id = @intval($_GET['id']);
    if (intval($id) > 0) {
        try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = 'SELECT avatar,avatar_info FROM '.Sql::getTablePrefix().'users WHERE id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $id, PDO::PARAM_INT);
            $result->execute();
            if ($db->query('SELECT FOUND_ROWS()')->fetchColumn() > 0) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $array_avatarInfo = unserialize($row['avatar_info']);
                $img = $row['avatar'];
                @header('Content-type: '.$array_avatarInfo['type']);
                echo $img;
            }
        } catch (PDOException $e) {
            echo 'Si è verificata una eccezione PDO Exception.';
            echo 'Errore restituito dal DB: ';
            echo 'SQL Query: ', $query;
            echo 'Errore: ' . $e->getMessage();
        }
    } else {
        echo 'Impossibile soddisfare la richiesta.';
    }
} else {
    echo 'Impossibile soddisfare la richiesta.';
}
