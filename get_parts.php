<?php
include './config.php';
$result = array();
$result[] = array( "name" => "-", "item" => "-", "price" => "0" );
if( !mysql_connect( $CONFIG[ "db_host" ], $CONFIG[ "db_user" ], $CONFIG[ "db_password" ]) ) {
    $result[] = array( "item" => "Не удалось подключиться к БД", "price" => "0" );
    echo json_encode($result);
    exit;
}
if( !mysql_select_db( $CONFIG[ "db_name" ] )) {
    $result[] = array( "item" => "Не удалось выбрать БД", "price" => "0" );
    echo json_encode($result);
    exit;
}
mysql_query( 'SET NAMES utf8' );
$res = mysql_query( "SELECT tid, name, CONCAT(name, \" - \", retail_price) item, retail_price FROM {$CONFIG['parts_table_name']} WHERE subcategory='{$_REQUEST['subcategory']}' ORDER BY 1" );
if( $res ) {
    while ($data = mysql_fetch_array($res)) {
        $result[] = array( "tid" => $data[ "tid" ], "name" => str_replace( "'", "\"", $data[ "name" ] ), "item" => $data[ "item" ], "price" => $data[ "retail_price" ] );
    }
} else {
    $result[] = array( "item" => mysql_error(), "price" => "0" );
}
echo json_encode($result);

?>
