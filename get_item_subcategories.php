<?php
include './config.php';
$result = array( "-" );
//$result = array( $_REQUEST['category'] );
if( !mysql_connect( $CONFIG[ "db_host" ], $CONFIG[ "db_user" ], $CONFIG[ "db_password" ]) ) {
    $result[] = "Не удалось подключиться к БД";
    echo json_encode($result);
    exit;
}
if( !mysql_select_db( $CONFIG[ "db_name" ] )) {
    $result[] = "Не удалось выбрать БД";
    echo json_encode($result);
    exit;
}
mysql_query( 'SET NAMES utf8' );
if( isset( $_REQUEST[ "table" ] ) ) {
    $table_name = $CONFIG[ $_REQUEST[ "table" ] ];
} else {
    $table_name = $CONFIG[ "goods_table_name" ];
}
if( !$res = mysql_query( "SELECT DISTINCT subcategory FROM {$table_name} WHERE category='{$_REQUEST['category']}'" ) ) {
    $result[] = mysql_error();
    echo json_encode($result);
    exit;
}
while( $data = mysql_fetch_assoc( $res ) ) {
    $result[] = $data[ "subcategory" ];
}
//$subcategories = array( "alpha", "beta" );
echo json_encode($result);
//var_dump( $_REQUEST );
?>