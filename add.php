<?php
session_start();
include './header.php';
include './config.php';
include './common.php';
include './thumblib/ThumbLib.inc.php';

if( !mysql_connect( $CONFIG[ "db_host" ], $CONFIG[ "db_user" ], $CONFIG[ "db_password" ]) ) {
    die("Не удалось подключиться к БД");
}
if( !mysql_select_db( $CONFIG[ "db_name" ] )) {
    die("Не удалось выбрать БД");
}
mysql_query( 'SET NAMES utf8' );

print( "<script type=\"text/javascript\" src=\"ckeditor/ckeditor.js\"></script>\n" );
echo "<pre>";
echo "<H1>REQUEST</H1>";
var_dump($_REQUEST);
//echo "<H1>FILES</H1>";
//var_dump($_FILES);
//echo "<H1>POST</H1>";
//var_dump($_POST);
echo "</pre>";

if( isset( $_REQUEST[ "create" ] ) ) {
    save();
} else if( isset( $_REQUEST[ "duplicate" ] ) ) {
    duplicate_goods();
}

include './menu.html';
print_add_form();

mysql_close();
?>