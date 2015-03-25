<?php
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
if( !mysql_query( "DELETE FROM ps_product WHERE id_product = {$product_id}" ) ) {
    mysql_query("ROLLBACK");
    mysql_close();
    die( "ERROR: " . mysql_error() );
}
if( !mysql_query( "DELETE FROM ps_category_product WHERE id_product = {$product_id}" ) ) {
    mysql_query("ROLLBACK");
    mysql_close();
    die( "ERROR: " . mysql_error() );
}
if( !mysql_query( "DELETE FROM ps_product_lang WHERE id_product = {$product_id}" ) ) {
    mysql_query("ROLLBACK");
    mysql_close();
    die( "ERROR: " . mysql_error() );
}
if( !mysql_query( "DELETE FROM ps_product_shop WHERE id_product = {$product_id}" ) ) {
    mysql_query("ROLLBACK");
    mysql_close();
    die( "ERROR: " . mysql_error() );
}
// Delete image
if( !$res = mysql_query( "SELECT count(*) c,id_image FROM ps_image WHERE id_image = (SELECT id_image FROM ps_image WHERE id_product = {$product_id})" ) ) {
    mysql_query("ROLLBACK");
    mysql_close();
    die( "ERROR: " . mysql_error() );
}
///////////////////////////// COMMIT //////////////////////
mysql_query("COMMIT");
//mysql_query("ROLLBACK");
/////////////////////////////
mysql_close();
$data = mysql_fetch_assoc($res);
if( $data[ "c" ] <= 1 ) {
    // Delete file
    $images_path = $CONFIG[ "imagesdir" ] . "/p/" . implode( "/", str_split( $data[ "id_image" ], 1 ) );
    //echo "id_image: " . $data[ "id_image" ] . "\n";
    if ( strtoupper( substr( PHP_OS, 0, 3 ) === "WIN" ) ) {
        //echo "WINDOWS [" . $images_path . "]\n";
        var_dump( exec( "rd /s /q \"{$images_path}\"" ) );
    } else {
        //echo "*NiX: " . PHP_OS . $images_path . "]\n";
        var_dump( exec("rm -rf \"{$images_path}\"") );
    }
} else {
    die( "Image is in use" );
}
/*
            // Сохраняем изображение на срвере
            // Добавить проверки
            $images_path = $CONFIG[ "imagesdir" ] . "/p/" . implode( "/", str_split( $image_id, 1 ) );
            print( "mkdir: " . $images_path . "<br>\n" );
            mkdir( $images_path, 0777, true );
            $images_path .= "/";
            print( "Перемещаем файл " . $images_path . $image_id . ".jpg : \n" );
            if( move_uploaded_file( $_FILES[ "new_image" ][ "tmp_name" ], $images_path . $image_id . ".jpg" ) ) {
                // Создаём миниатюры
                $thumb = PhpThumbFactory::create( $images_path . $image_id . ".jpg" );
                $thumb->adaptiveResize(124, 124);
                $thumb->save( $images_path . $image_id . "-home_default.jpg", "jpg" );

                $thumb = PhpThumbFactory::create( $images_path . $image_id . ".jpg" );
                $thumb->adaptiveResize(264, 264);
                $thumb->save( $images_path . $image_id . "-large_default.jpg", "jpg" );

                $thumb = PhpThumbFactory::create( $images_path . $image_id . ".jpg" );
                $thumb->adaptiveResize(58, 58);
                $thumb->save( $images_path . $image_id . "-medium_default.jpg", "jpg" );

                $thumb = PhpThumbFactory::create( $images_path . $image_id . ".jpg" );
                $thumb->adaptiveResize(45, 45);
                $thumb->save( $images_path . $image_id . "-small_default.jpg", "jpg" );

                $thumb = PhpThumbFactory::create( $images_path . $image_id . ".jpg" );
                $thumb->adaptiveResize(600, 600);
                $thumb->save( $images_path . $image_id . "-thickbox_default.jpg", "jpg" );
            }
*/

?>