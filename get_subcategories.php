<?php
include './config.php';
$result = array();
$result[] = array( "id" => "-", "name" => "-" );
//$result = array( $_REQUEST['category'] );
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
if( isset( $_REQUEST[ "table" ] ) ) {
    $table_name = $CONFIG[ $_REQUEST[ "table" ] ];
} else {
    $table_name = $CONFIG[ "goods_table_name" ];
}
//if( !$res = mysql_query( "SELECT DISTINCT subcategory FROM {$table_name} WHERE category='{$_REQUEST['category']}'" ) ) {
if( !$res = mysql_query( "SELECT cl.id_category, cl.name FROM ps_category_lang cl, ps_category c WHERE cl.id_lang=1 AND cl.id_category = c.id_category AND c.id_parent={$_REQUEST['category']}" ) ) {
    $result[] = array( "id" => "-", "name" => mysql_error() );
    echo json_encode( $result );
    exit;
}
while( $data = mysql_fetch_assoc( $res ) ) {
    $result[] = array( "id" => $data[ "id_category" ], "name" => $data[ "name" ] );
}
//$subcategories = array( "alpha", "beta" );
echo json_encode( $result );
//var_dump( $_REQUEST );
?>