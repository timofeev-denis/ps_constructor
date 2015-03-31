<?php
include './common.php';
include './config.php';
if( !mysql_connect( $CONFIG[ "db_host" ], $CONFIG[ "db_user" ], $CONFIG[ "db_password" ]) ) {
    $result[] = array( "id" => "-", "name" => "Не удалось подключиться к БД" );
    echo json_encode( $result );
    exit;
}
if( !mysql_select_db( $CONFIG[ "db_name" ] )) {
    $result[] = array( "id" => "-", "name" => "Не удалось выбрать БД" );
    echo json_encode( $result );
    exit;
}
mysql_query( 'SET NAMES utf8' );

$product_id = intval( filter_input( INPUT_GET, "id", FILTER_VALIDATE_INT ) );
if( !$product_id ) {
    die( "Product ID is not specified" );
}
mysql_query( "START TRANSACTION" );
if( !mysql_query( "DELETE FROM {$CONFIG[ "parts_table_name" ]} WHERE tid != 0 AND tid = (SELECT tid FROM {$CONFIG[ "ps_product" ]} WHERE id_product = {$product_id})" ) ) {
    print( "ERROR (__FILE__:__LINE__): " . mysql_error() );
    mysql_query("ROLLBACK");
    exit;
}
if( !mysql_query( "DELETE FROM ps_product WHERE id_product = {$product_id}" ) ) {
    print( "ERROR (__FILE__:__LINE__): " . mysql_error() );
    mysql_query("ROLLBACK");
    exit;
}
if( !mysql_query( "DELETE FROM ps_category_product WHERE id_product = {$product_id}" ) ) {
    print( "ERROR (__FILE__:__LINE__): " . mysql_error() );
    mysql_query("ROLLBACK");
    exit;
}
if( !mysql_query( "DELETE FROM ps_product_lang WHERE id_product = {$product_id}" ) ) {
    print( "ERROR (__FILE__:__LINE__): " . mysql_error() );
    mysql_query("ROLLBACK");
    exit;
}
if( !mysql_query( "DELETE FROM ps_product_shop WHERE id_product = {$product_id}" ) ) {
    print( "ERROR (__FILE__:__LINE__): " . mysql_error() );
    mysql_query("ROLLBACK");
    exit;
}
// Delete image
if( delete_product_image( $product_id ) ) {
    mysql_query("COMMIT");
    echo "OK";
} else {
    mysql_query("ROLLBACK");
    echo "Something is wrong...";
}

?>