<?php

function print_table_header() {
    print( "<table>\n" );
    print( "<th>Артикул</th>\n" );
    print( "<th>Название</th>\n" );
    print( "<th>Категория</th>\n" );
    print( "<th>Подкатегория</th>\n" );
    print( "<th>Закупочная</th>\n" );
    print( "<th>Мелкий опт</th>\n" );
    print( "<th>Розница</th>\n" );
}
function print_table_footer() {
    print( "</table>\n" );
}
// Выводит строку таблицы с записью из tovars_new
function print_table_row($row) {
    printf( "
<tr>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
</tr>\n\n", $row[ "articul" ], $row[ "name" ], $row[ "category" ], $row[ "subcategory" ]
            , $row[ "price" ], $row[ "small_scale_price" ], $row[ "retail_price" ] );
}

function print_add_form() {
    global $CONFIG;
    ?>
    <FORM>
    <p><b>Параметры нового товара</b></p>
    <div class="param w200">
        <label for='new_name'>Название: </label><br>
        <input type='text' id='new_name' name='new_name' class='new' /><br>
    </div>
    <div class="param w200">
        <label for='new_articul'>Артикул: </label><br>
        <input type='text' id='new_articul' name='new_articul' class='new' /><br>
    </div>
    <div class="param w200">
        <label for='new_category'>Категория: </label><br>
        <select id='new_category' name='new_category' class='new category' data-number='0'>
            <option value='-' class='new'>-</option>
    <?php
    $res = mysql_query( "SELECT DISTINCT category FROM {$CONFIG['goods_table_name']} ORDER BY 1" );
    if( $res ) {
        while ($data = mysql_fetch_array($res)) {
            printf( "<option value='%s' class='new'>%s</option>\n", $data[ "category" ], $data[ "category" ] );
        }
    } else {
        printf( "<option value='ОШИБКА' class='new'>%s</option>\n", mysql_error() );
    }
    ?>
        </select>
    </div>
    <div class="param w200">
        <label for="new_subcategory">Подкатегория: </label><br>
        <select id="new_subcategory" name='new_subcategory' class='new subcategory' data-number='0'>
            <option value='-' class='new'>Выберите категорию</option>
        </select>
    </div>
    <?php
//    print( "</select><br>\n" );
//    print( "<div clas=\"param col50\"><label for=\"new_subcategory\">Подкатегория: </label><br><select id='new_subcategory' name='new_subcategory' class='new subcategory' data-number='0' disabled>\n" );
//    print( "<option value='ПУСТО' class='new'>Выберите категорию</option>\n" );
//    print( "</select><br>\n" );
//    print( "</TD><TD>\n" );
    //print( "<label for='new_price'>Закупочная: </label><input type='text' id='new_price' name='new_price' class='new' /><br>\n" );
    //print( "<label for='new_small_scale_price'>Мелкий опт: </label><input type='text' id='new_small_scale_price' name='new_small_scale_price' class='new' /><br>\n" );
    //print( "<label for='new_retail_price'>Розница: </label><input type='text' id='new_retail_price' name='new_retail_price' class='new' /><br>\n" );
//    print( "<TR><TD colspan=2 align=left><p><b>Состав нового товара</b></p></TD></TR>\n" );

    // Категории из parts_table
    $parts_categories = array();
    $res = mysql_query( "SELECT DISTINCT category FROM {$CONFIG['parts_table_name']} ORDER BY 1" );
    if( $res ) {
        while ($data = mysql_fetch_array($res)) {
            $parts_categories[] = $data[ "category" ];
        }
    } else {
        $parts_categories[] = mysql_error();
    }
    
    //
    // Компонент №1
    //
    // Категории
    print( "<TR><TD colspan=2 align=left><label for='item1'>Компонент: </label>" );
    print( "<select class='item_category' data-number='1'>\n" );
    foreach ($parts_categories as $value) {
        printf( "<option value='%s'>%s</option>\n", $value, $value );
    }
    print( "</select><br>\n" );
    // Подкатегории
    print( "<select class='subcategory' data-number='1' disabled>\n" );
    print( "<option value='-' class='new'>Выберите категорию</option>\n" );
    print( "</select><br>\n" );
    // Запчасти
    print( "<select id='item1' name='item[]' class='item' data-number='1' disabled>\n" );
    print( "<option value='-' class='new'>Выберите подкатегорию</option>\n" );
    print( "</select> x <input type='text' name='qty[]' class='item_qty'></TD></TR>\n" );
    //
    // Компонент №2
    //
    print( "<TR><TD colspan=2 align=left><label for='item2'>Компонент: </label>"
            . "<select id='item2' name='item[]' class='new_item'>\n" );
    $res = mysql_query( "SELECT CONCAT(name, \" - \", retail_price) item, retail_price FROM {$CONFIG['parts_table_name']} ORDER BY 1 LIMIT 25, 10" );
    if( $res ) {
        while ($data = mysql_fetch_array($res)) {
            printf( "<option value='%s' class='new'>%s</option>\n", $data[ "retail_price" ], $data[ "item" ] );
        }
    } else {
        printf( "<option value='ОШИБКА' class='new'>%s</option>\n", mysql_error() );
    }
    print( "</select> x <input type='text' name='qty[]' class='item_qty'></TD></TR>\n" );
    // Компонент №3
    print( "<TR><TD colspan=2 align=left><label for='item3'>Компонент: </label>"
            . "<select id='item3' name='item[]' class='new_item'>\n" );
    $res = mysql_query( "SELECT CONCAT(name, \" - \", retail_price) item, retail_price FROM {$CONFIG['parts_table_name']} ORDER BY 1 LIMIT 25, 10" );
    if( $res ) {
        while ($data = mysql_fetch_array($res)) {
            printf( "<option value='%s' class='new'>%s</option>\n", $data[ "retail_price" ], $data[ "item" ] );
        }
    } else {
        printf( "<option value='ОШИБКА' class='new'>%s</option>\n", mysql_error() );
    }
    print( "</select> x <input type='text' name='qty[]' class='item_qty'></TD></TR>\n" );
    // Кнопки 
    print( "<TR><TD colspan=2 class='buttons'><input type='submit' name='create' value='Сохранить' />"
            . "<input type='submit' name='copy' value='Копировать' /></TD></TR>\n" );
    print( "\n" );
    print( "\n" );
    print( "\n" );
    print( "\n" );
    print( "</TD></TABLE>\n" );
    print( "\n" );
    print( "\n" );
    print( "\n" );
    print( "\n" );
    print( "\n" );
    print( "\n" );
    print( "\n" );
    print( "</FORM>\n" );
}
function save() {
    global $CONFIG;
    if( $_REQUEST[ "new_name" ] == "" || $_REQUEST[ "new_articul" ] == "" || 
        $_REQUEST[ "new_category" ] == "" || $_REQUEST[ "new_subcategory" ] == "" ) {
        print( "Товар не добавлен, так как не указаны его параметры." );
        return false;
    }
    // Расчёт стоимости нового товара
    $final_price = 0;
    foreach( $_REQUEST[ "item" ] as $k => $v ) {
        printf( "%s * %s +<br>\n", $v, $_REQUEST[ "qty" ][ $k ]);
        $final_price += $v * $_REQUEST[ "qty" ][ $k ];
    }
    printf( "= " . $final_price . "<br />\n" );
    //printf( "%s / 100 * %s<br>\n", $final_price, $final_price, $CONFIG[ "margin" ] );
    $final_price += $final_price / 100 * $CONFIG[ "margin" ];
    print( "+" . $CONFIG[ "margin" ] . "% = ". $final_price . "<br />\n" );
    if( $final_price == 0 ) {
        print( "Товар не добавлен, так как не указаны его составные части." );
        return false;
    }
    $q = sprintf( "INSERT INTO goods (articul, category, subcategory, name, retail_price, price, small_scale_price) VALUES ( '%s', '%s', '%s', '%s', '%s', 0, 0 )",
        $_REQUEST[ "new_articul" ], $_REQUEST[ "new_category" ], $_REQUEST[ "new_subcategory" ], $_REQUEST[ "new_name" ], $final_price );
    if( mysql_query( $q ) ) {
        print( "Добавлен новый товар с tid = " . mysql_insert_id() );
    } else {
        print( "Товар не добавлен: " . mysql_error() );
    }
}
?>