<?php
include './config.php';
$result = array();
$result[] = "-";
if( !mysql_connect( $CONFIG[ "db_host" ], $CONFIG[ "db_user" ], $CONFIG[ "db_password" ]) ) {
    $result[] = "Не удалось подключиться к БД";
    echo json_encode( $result );
    exit;
}
if( !mysql_select_db( $CONFIG[ "db_name" ] )) {
    $result[] = "Не удалось выбрать БД";
    echo json_encode( $result );
    exit;
}
mysql_query( 'SET NAMES utf8' );
$parts_category = filter_input( INPUT_GET, "parts_category", FILTER_SANITIZE_STRING );
if( $parts_category === false ) {
    $result[] = "Некорректное название категории";
    echo json_encode( $result );
    exit;
}
if( !$res = mysql_query( "SELECT DISTINCT subcategory FROM {$CONFIG[ "parts_table_name" ]} WHERE category='{$parts_category}'" ) ) {
    $result[] = mysql_error();
    echo json_encode( $result );
    exit;
}
while( $data = mysql_fetch_assoc( $res ) ) {
    $result[] = $data[ "subcategory" ];
}
echo json_encode( $result );
?>