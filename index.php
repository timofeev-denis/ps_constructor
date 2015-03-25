<?php
session_start();
include './header.php';
include './config.php';
include './common.php';

if( !mysql_connect( $CONFIG[ "db_host" ], $CONFIG[ "db_user" ], $CONFIG[ "db_password" ]) ) {
    die("Не удалось подключиться к БД");
}
if( !mysql_select_db( $CONFIG[ "db_name" ] )) {
    die("Не удалось выбрать БД");
}
mysql_query( 'SET NAMES utf8' );

if( isset( $_REQUEST[ "create" ] ) ) {
    save();
} else if( isset( $_REQUEST[ "duplicate" ] ) ) {
    
    duplicate_goods();
}

    echo "<pre>";
    //var_dump($_GET);
    echo "</pre>";
include './menu.html';
//print_add_form();

print_table_header();
//$result = mysql_query("SELECT articul, name, category, subcategory, price, small_scale_price, retail_price FROM {$CONFIG['goods_table_name']} ORDER BY tid DESC LIMIT 0, 20");
$result = mysql_query("SELECT p.*, pl.name pname, pl.description, cl.name cname 
FROM {$CONFIG['ps_product']} p, {$CONFIG['ps_product_lang']} pl, {$CONFIG['ps_category_lang']} cl 
WHERE p.id_product = pl.id_product 
      AND pl.id_lang=1 
      AND cl.id_category=p.id_category_default
      AND p.active=1
ORDER BY id_product DESC LIMIT 0, 20");
if( $result ) {
    while ($row = mysql_fetch_array($result)) {
        print_table_row( $row );
    }
}
print_table_footer();
mysql_close();
?>