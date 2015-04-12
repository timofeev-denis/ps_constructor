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

$type = filter_input( INPUT_GET, "type", FILTER_VALIDATE_INT);
if( $type === false || $type === NULL ) {
    $type = filter_input( INPUT_POST, "type", FILTER_VALIDATE_INT);    
}

if( $type === false || $type === NULL ) {
    $type = TYPE_PRODUCT;
}

if( isset( $_REQUEST[ "create" ] ) ) {
    $product_id = save_product( $type );
    if( $type == TYPE_COMPONENT ) {
        save_component( $type );
    }
} else {
    $product_id = 0;
}

print_add_form( $product_id, $type );

mysql_close();
?>