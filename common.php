<?php
@session_start();
function print_table_header() {
    print( "<h3>Последние добавленные товары:</h3>\n" );
    print( "<table width=100% border=1>\n" );
    print( "<th width=4%%></th>\n" );
    print( "<th width=6%%>Артикул</th>\n" );
    print( "<th width=15%%>Название</th>\n" );
    print( "<th width=48%%>Состав</th>\n" );
    print( "<th width=15%%>Категория</th>\n" );
//    print( "<th>Подкатегория</th>\n" );
//    print( "<th>Закупочная</th>\n" );
    print( "<th width=6%%>Цена без НДС</th>\n" );
    print( "<th width=6%%>Розница</th>\n" );
}
function print_table_footer() {
    print( "</table>\n" );
}
// Выводит строку таблицы с записью из tovars_new
function print_table_row($row) {
    printf( "
<tr>
    <td>
        <a href='add.php?product_id=%s&duplicate=1' target='_blank'><i class='fa fa-files-o duplicate' title='Дублировать'></i></a>
        <i class='fa fa-trash-o delete' data-tid='%s' title='Удалить'></i>
    </td>
    <td>%s</td>
    <td><a href='add.php?product_id=%s' target='_blank'>%s</a></td>
    <td>%s</td>
    <td>%s</td>
    <td>%.2f</td>
    <td>%.2f</td>
</tr>\n\n", $row[ "id_product" ], $row[ "id_product" ], $row[ "reference" ], $row[ "id_product" ], $row[ "pname" ], $row[ "content_desc" ], $row[ "cname" ]
            , $row[ "price" ], $row[ "price" ] * 1.18 );
}

function print_item_selector( &$parts_categories, $tid, $qty, $num ) {
    global $CONFIG;
?>
    <div class="component" data-number='<?=$num?>'>
        <!-- Компонент №1 -->
        <div class="param w200">
            <select class='item_category new' data-number='<?=$num?>' name="item_category[]">
                <option value='-'>-</option>
            <?php
            if( intval( $tid ) > 0 ) {
                $res = mysql_query( "SELECT category, subcategory FROM {$CONFIG[ "parts_table_name" ]} WHERE tid=" . intval( $tid ) );
                if( $res ) {
                    $categs = mysql_fetch_assoc( $res );
                }
            }

            foreach ($parts_categories as $value) {
                if( $value == $categs[ "category" ] ) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                printf( "<option value='%s' %s>%s</option>\n", $value, $selected, $value );
            }
            ?>
            </select>
        </div>
        <div class="param w200">
            <?php
            if( intval( $tid ) > 0 ) {
                // Есть компонент №1
                print( "<select class='subcategory new' data-number='$num' name='item_subcategory[]'>\n" );
                print( "<option value='-'>-</option>" );
                if( $res = mysql_query( "SELECT DISTINCT subcategory FROM {$CONFIG[ "parts_table_name" ]} WHERE category='{$categs['category']}'" ) ) {
                    while( $data = mysql_fetch_assoc( $res ) ) {
                        if( $data[ "subcategory" ] == $categs[ "subcategory" ] ) {
                            $selected = "selected";
                        } else {
                            $selected = "";
                        } 
                        printf( "<option value='%s' %s>%s</option>\n", $data[ "subcategory" ], $selected, $data[ "subcategory" ] );
                    }
                }
            } else {
                print( "<select class='subcategory new' data-number='$num' name='item_subcategory[]' disabled>\n" );
                print( "<option value='-'>Выберите категорию</option>" );
                
            }
            ?>
            </select>
        </div>
        <div class="param w200">
            <?php
            $item_name_value = "";
            if( intval( $tid ) > 0 ) {
                // Есть компонент №1
                print( "<select name='item[]' class='item new' data-number='$num'>\n" );
                print( "<option value='0' data-parttid='undefined' data-partname='-'>-</option>\n" );
                $res = mysql_query( "SELECT tid, name, CONCAT(name, \" - \", retail_price) item, retail_price FROM {$CONFIG['parts_table_name']} WHERE subcategory='{$categs['subcategory']}' ORDER BY 1" );
                if( $res ) {
                    while ($data = mysql_fetch_array($res)) {
                        //$result[] = array( "tid" => $data[ "tid" ], "name" => str_replace( "'", "\"", $data[ "name" ] ), "item" => $data[ "item" ], "price" => $data[ "retail_price" ] );
                        if( $data[ "tid" ] == $tid ) {
                            $selected = "selected";
                            $item_name_value = $data[ "name" ];
                        } else {
                            $selected = "";
                        }
                        printf( "<option value='%s' data-parttid='%s' data-partname='%s' %s>%s</option>\n", $data[ "retail_price" ], $data[ "tid" ], $data[ "name" ], $selected, $data[ "item" ] );
                    }
                }
            } else {
                print( "<select name='item[]' class='item new' data-number='$num' disabled>\n" );
                print( "<option value='-'>Выберите подкатегорию</option>\n" );
            }
            ?>
            </select>
            <input type="hidden" class="item_name" name="item_name[]" data-number='<?=$num?>' value="<?=$item_name_value?>" />
            <input type="hidden" class="item_tid" name="item_tid[]" data-number='<?=$num?>' value="<?=$tid?>" />
        </div>
        <div class="param w100">
            <input type='text' name='qty[]' class='item_qty' value="<?=$qty?>">
        </div>
        <div class="param w100">
            <input type='button' class='remove_item' data-number='<?=$num?>' value="-">
        </div>
        <div class="clearfix"></div>
    </div>
<?php
}

function print_add_form( $product_id = 0 ) {
    global $CONFIG;
    $product = array();
    if( $product_id == 0 ) {
        $product_id = filter_input( INPUT_GET, "product_id", FILTER_VALIDATE_INT );
    }
    if( $product_id ) {
        // Редактирование
        $res = mysql_query("SELECT p.*, pl.name pname, pl.description, cl.name cname 
        FROM {$CONFIG['ps_product']} p, {$CONFIG['ps_product_lang']} pl, {$CONFIG['ps_category_lang']} cl 
        WHERE p.id_product = pl.id_product 
              AND pl.id_lang=1 
              AND cl.id_category=p.id_category_default
              AND p.id_product=" . $product_id );
        if( !$res ) {
            print( "Товар не найден." );
            return;
        }
        $product = mysql_fetch_array($res);
        $parts = unserialize( $product[ "parts" ] );
        $items = array();
        while( list( $k, $v ) = @each( $parts ) ) {
            if( $k != "multiplier" ) {
                $items[] = $k;
            }
        }
        @reset( $parts );
        //$items = array_keys( $parts );
        $product[ "multiplier" ] = $parts[ "multiplier" ];
        // Определение подкатегории
        $res = mysql_query( "SELECT id_parent FROM {$CONFIG[ "ps_category" ]} "
        . " WHERE id_category={$product["id_category_default"]} "
        . " AND id_parent <> (SELECT id_category FROM ps_category WHERE is_root_category=1)" );
        if( !$res ) {
            print( "При определении параметров товала произошла ошибка." );
            return;
        }
        $data = mysql_fetch_assoc( $res );
        if( intval( $data[ "id_parent" ] ) > 0 ) {
            // Категория товара находится не на первом уровне иерархии
            $product[ "subcategory" ] = $product[ "id_category_default" ];
            $product[ "category" ] = intval( $data[ "id_parent" ] );
        } else {
            $product[ "subcategory" ] = "-";
            $product[ "category" ] = $product[ "id_category_default" ];
        }
    } else {
        $product[ "pname" ] = "";
        $product[ "description" ] = "";
    }
    ?>
    <FORM enctype="multipart/form-data" action="add.php" method="POST">
    <div class="part">        
        <input type="hidden" name="product_id" value="<?=$product_id?>" />
        <input type="hidden" name="duplicate" value="<?=intval( filter_input( INPUT_GET, "duplicate", FILTER_VALIDATE_INT ) )?>" />
        <h3>Параметры нового товара:</h3>
        <div class="param w200">
            <label for='new_name'>Название: </label><br>
            <input type='text' id='new_name' name='new_name' class='new' value="<?=$product[ "pname" ]?>"/><br>
        </div>
        <div class="param w200">
            <label for='new_articul'>Артикул: </label><br>
            <input type='text' id='new_articul' name='new_articul' class='new'  value="<?=$product[ "reference" ]?>"/><br>
        </div>
        <div class="param w200">
            <label for='new_category'>Категория: </label><br>
            <input type="button" value="+" id="add_category" title="Нажмите, чтобы создать новую категорию" />
            <input type="text" id="new_custom_category" name="new_category" class="hidden" disabled/>
            <select id='new_category' name='new_category' class='new category' data-number='0'>
                <option value='-' class='new'>-</option>
        <?php
        //$res = mysql_query( "SELECT DISTINCT category FROM {$CONFIG['goods_table_name']} ORDER BY 1" );
        $res = mysql_query( "SELECT cl.id_category, cl.name FROM {$CONFIG[ "ps_category_lang" ]} cl, ps_category c WHERE cl.id_lang=1 AND cl.id_category = c.id_category AND c.id_parent=2 AND c.active=1" );
        if( $res ) {
            while ($data = mysql_fetch_array($res)) {
                $selected = "";
                if( $data[ "id_category" ] == $product[ "category" ] || $data[ "id_category" ] == $product[ "subcategory" ] ) {
                    $selected = "selected";
                }
                printf( "<option value='%s' class='new' %s>%s</option>\n", $data[ "id_category" ], $selected, $data[ "name" ] );
            }
        } else {
            printf( "<option value='ОШИБКА' class='new'>%s</option>\n", mysql_error() );
        }
        ?>
            </select>
            
        </div>
        <div class="param w200">
            <label for="new_subcategory">Подкатегория: </label><br>
            <input type="button" value="+" id="add_subcategory" title="Нажмите, чтобы создать новую подкатегорию" disabled />
            <input type="text" id="new_custom_subcategory" name="new_subcategory" class="hidden" disabled />
            
                <?php
                if( @intval( $_REQUEST[ "product_id" ] ) == 0 ) {
                    // Добавление товара
                    print( "<select id='new_subcategory' name='new_subcategory' class='new subcategory' data-number='0' disabled>" );
                    print( "<option value='-' class='new'>Выберите категорию</option>" );
                } else {
                    // Редактирование товара
                    print( "<select id='new_subcategory' name='new_subcategory' class='new subcategory' data-number='0'>" );
                    print( "<option value='-' class='new'>-</option>" );
                    $res = mysql_query( "SELECT cl.id_category, cl.name FROM {$CONFIG[ "ps_category_lang" ]} cl, ps_category c WHERE cl.id_lang=1 AND cl.id_category = c.id_category AND c.id_parent={$product[ "category" ]} AND c.active=1" );
                    if( $res ) {
                        while ($data = mysql_fetch_array($res)) {
                            $selected = "";
                            if( $data[ "id_category" ] == $product[ "subcategory" ] ) {
                                $selected = "selected";
                            }
                            printf( "<option value='%s' class='new' %s>%s</option>\n", $data[ "id_category" ], $selected, $data[ "name" ] );
                        }
                    } else {
                        printf( "<option value='ОШИБКА' class='new'>%s</option>\n", mysql_error() );
                    }
                }
                ?>
                
            </select>
        </div>
        <div class="param w200">
            <label for='new_articul'>Наценка: </label><br>
            <input type='text' id='new_multiplier' name='new_multiplier' class='item_qty' value="<?=(isset( $product[ "multiplier" ] ) ? $product[ "multiplier" ] : $CONFIG[ "margin" ])?>" />%
        </div>
        <div class="param w200">
            <label for='new_image'>Изображение: </label><br>
            <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
            <input type="file" id="new_image" name="new_image" />
        </div>
        <div class="clearfix"></div>
        <br>
        <label for='new_description'>Описание: </label><br>
        <textarea name="new_description" id="new_description">
            <?=$product[ "description" ]?>
        </textarea>
        <script type="text/javascript">
            CKEDITOR.replace( 'new_description' );
        </script>
        <div class="clearfix"></div>
        <br>
    </div>
    <?php
    // Категории из parts_table
    $parts_categories = array();
    $res = mysql_query( "SELECT DISTINCT category FROM {$CONFIG['parts_table_name']} ORDER BY 1" );
    if( $res ) {
        while ($data = mysql_fetch_array($res)) {
            $parts_categories[] = $data[ "category" ];
        }
        $_SESSION[ "parts_categories" ] = $parts_categories;
    } else {
        $parts_categories[] = mysql_error();
    }
    ?>
    
    <h3>Состав нового товара:</h3>
    <div id="parts">
        <div class="components_title">
                <div class="param w200">
                        Категория:<br>
                </div>
                <div class="param w200">
                        Подкатегория:<br>
                </div>
                <div class="param w200">
                        Название - цена:<br>
                </div>
                <div class="param w100">
                        Кол-во:<br>
                </div>
                <div class="param w100">
                        Удалить<br>
                </div>
                <div class="clearfix"></div>
        </div>
    <?php
    //print_r( $parts );
    //print_r( $items );
    $i = 1;
    $k = "";
    if( @count( $items ) > 0 ) {
        foreach( $items as $k => $v ) {
            //print( $k . " / " . $v . " / " . $parts[ $v ] . " / " . ++$i . "<br>\n" );
            print_item_selector( $parts_categories, $v, @$parts[ $v ], $i++ );
        }
    } else {
        print_item_selector( $parts_categories, "", "", $i++ );
    }
    /*
    do {
        list( $k, $v ) = @each( $items );
        //print_item_selector($parts_categories, @$items[ $i ], @$parts[ @$items[ $i ] ], ++$i);
        print( $k . " / " . $v . " / " . $parts[ $v ] . " / " . ++$i . "<br>\n" );
        
    } while( count( $items ) );
     */
    ?>
    </div>
    <input type="button" class="btn add_item" value="Ещё">
    
    <div class="clearfix"></div>

    <div class="buttons11">
        <input type='submit' id="create" name="create" value='Сохранить' />
        <input type="submit" id="duplicate" name="duplicate" value='Копировать' />
        <input type="submit" value="TEST" />
    </div>
    </form>
    <?php
//    print( "" );
//    print( "<select class='item_category' data-number='1'>\n" );
//    foreach ($parts_categories as $value) {
//        printf( "<option value='%s'>%s</option>\n", $value, $value );
//    }
//    print( "</select><br>\n" );
    // Подкатегории
//    print( "<select class='subcategory' data-number='1' disabled>\n" );
//    print( "<option value='-' class='new'>Выберите категорию</option>\n" );
//    print( "</select><br>\n" );
    // Запчасти
//    print( "<select id='item1' name='item[]' class='item' data-number='1' disabled>\n" );
//    print( "<option value='-' class='new'>Выберите подкатегорию</option>\n" );
//    print( "</select> x <input type='text' name='qty[]' class='item_qty'></TD></TR>\n" );
    //
    // Компонент №2
    //
}
function save() {
    global $CONFIG;
    if( $_REQUEST[ "new_name" ] == "" || $_REQUEST[ "new_articul" ] == "" || 
        $_REQUEST[ "new_category" ] == "" ) {
        print( "Товар не сохранён, так как не указаны его параметры." );
        return false;
    }
    $categoryId = intval( $_REQUEST[ "new_subcategory" ] );
    
    if( $categoryId == 0 ) {
        // Если передан "кривой" id подкатегории - используем id категории
        $categoryId = intval( $_REQUEST[ "new_category" ] );
        if( $categoryId == 0 ) {
            // Если передан "кривой" id категории - используем значение по умолчанию
            $categoryId = $CONFIG[ "ps_default_category" ];
        }
    }
    // Расчёт стоимости нового товара
    $final_price = 0;
    //$content_description = "Состав изделия:<br><br>\n";
    $content_description = "";
    $parts = array();
    foreach( $_REQUEST[ "item" ] as $k => $v ) {
        printf( "%s * %s + \n", $v, intval( $_REQUEST[ "qty" ][ $k ] ));
        $final_price += $v * intval( $_REQUEST[ "qty" ][ $k ] );
        $content_description .= $_REQUEST[ "item_name" ][ $k ] . " x " . intval( $_REQUEST[ "qty" ][ $k ] ) . "<br>\n";
        if(intval( $parts[ $_REQUEST[ "item_tid" ][ $k ] ] ) == 0 ) {
            $parts[ $_REQUEST[ "item_tid" ][ $k ] ] = intval( $_REQUEST[ "qty" ][ $k ] );
        } else {
            //error_log( "Компонент с tid=" . $_GET[ "item_tid" ][ $k ] . " добавлен несколько раз." );
            print( "ВНИМАНИЕ! Компонент с tid=" . $_REQUEST[ "item_tid" ][ $k ] . " добавлен несколько раз." );
        }
    }
    //printf( " = " . $final_price . "<br />\n" );
    //printf( "%s / 100 * %s<br>\n", $final_price, $final_price, $CONFIG[ "margin" ] );
    // Если множитель > 1, то умножаем на него, иначе - умножаем на 1
    $final_price += $final_price / 100 * ( intval( $_REQUEST[ "new_multiplier" ] ) > 0 ? intval( $_REQUEST[ "new_multiplier" ] ) : 1 );
    $final_price = round( $final_price , 2 );
    print( $CONFIG[ "margin" ] . "% = ". $final_price . "<br />\n" );
    $parts[ "multiplier" ] = intval( $_REQUEST[ "new_multiplier" ] );
    $parts = serialize( $parts );
    if( $final_price == 0 ) {
        print( "Товар не сохранён, так как не указаны его составные части." );
        return false;
    }
    //$q = sprintf( "INSERT INTO goods (articul, category, subcategory, name, retail_price, price, small_scale_price, parts) VALUES ( '%s', '%s', '%s', '%s', '%s', 0, 0, '%s' )",
    //    $_GET[ "new_articul" ], $_GET[ "new_category" ], $_GET[ "new_subcategory" ], $_GET[ "new_name" ], $final_price, $parts );

    $product_id = intval( filter_input( INPUT_POST, "product_id", FILTER_VALIDATE_INT ) );
    $duplicate = intval( filter_input( INPUT_POST, "duplicate", FILTER_VALIDATE_INT ) );
    if( $product_id == 0 || $duplicate == 1 ) {
        echo "<h2>Добавление</h2>\n";
        // Добавление категории и подкатегории
        $q = sprintf( "INSERT INTO ps_product (id_supplier, id_manufacturer, id_category_default, id_tax_rules_group, active, price, reference, redirect_type, unity, ean13, upc, supplier_reference, location, indexed, cache_default_attribute, date_add, date_upd, parts, content_desc ) 
    VALUES(1, 1, {$categoryId}, 1, 1, {$final_price}, '{$_REQUEST[ "new_articul" ]}', '', '', 0, '', '', '', 1, 0, '2015-03-18 21:40:59', '2015-03-18 21:40:59', '{$parts}', '{$content_description}')" );
        if( !mysql_query( $q ) ) {
            print( "Ошибка при добавлении в ps_product: " . mysql_error() . "<br>\n");
            print( "Текст запроса: " . $q . "<br>\n");
            return false;
        }
        $product_id = mysql_insert_id();
        print( "Добавлена запись в ps_product, id = " . $product_id . "<br>\n");

        $q = sprintf( "INSERT INTO `ps_category_product` (`id_category`,`id_product`,`position`) VALUES ({$categoryId},{$product_id},0)" );
        if( !mysql_query( $q ) ) {
            print( "Ошибка при добавлении в ps_category_product: " . mysql_error() );
            return false;
        }
        print( "Добавлена запись в ps_category_product<br>\n" );

        $q = sprintf( "INSERT INTO ps_product_lang 
    (id_product, id_shop, id_lang, description, description_short, link_rewrite, meta_description, meta_keywords, meta_title, name, available_now, available_later)
    VALUES ({$product_id}, 1, 1, '{$_REQUEST[ "new_description" ]}', 'Short desc', '', '', '', '', '{$_REQUEST[ "new_name" ]}', '', '')" );
        if( !mysql_query( $q ) ) {
            print( "Ошибка при добавлении в ps_product_lang: " . mysql_error() );
            return false;
        }
        print( "Добавлена запись в ps_product_lang<br>\n" );

        $q = sprintf( "INSERT INTO `ps_product_shop` 
    (`id_product`,`id_shop`,`id_category_default`,`id_tax_rules_group`,`on_sale`,`online_only`,`ecotax`,`minimal_quantity`,`price`,`wholesale_price`,`unity`,`unit_price_ratio`,`additional_shipping_cost`,`customizable`,`uploadable_files`,`text_fields`,`active`,`redirect_type`,`id_product_redirected`,`available_for_order`,`available_date`,`condition`,`show_price`,`indexed`,`visibility`,`cache_default_attribute`,`advanced_stock_management`,`date_add`,`date_upd`) 
    VALUES ({$product_id},1,{$categoryId},1,0,0,0,1,{$final_price},0,'',0,0,0,0,0,1,'',0,1,'0000-00-00','new',1,1,'both','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
    " );
        if( !mysql_query( $q ) ) {
            print( "Ошибка при добавлении в ps_product_shop: " . mysql_error() );
            return false;
        }
        print( "Добавлена запись в ps_product_shop<br>\n" );

        // Добавление изображения товара
        if( count( $_FILES ) > 0 ) {
            // Добвляем необходимую информацию в БД
            $q = sprintf( "INSERT INTO `ps_image` ( `id_image`, `id_product`, `position`,`cover`) VALUES (NULL, {$product_id},'1','1')" );
            if( !mysql_query( $q ) ) {
                print( "Ошибка при добавлении в ps_image: " . mysql_error() );
                return false;
            }
            print( "Добавлена запись в ps_image<br>\n" );
            // Запоминаем id нового изображения
            $image_id = mysql_insert_id();

            $q = sprintf( "INSERT INTO `ps_image_lang` ( `id_image`, `id_lang`,`legend`) VALUES ({$image_id},'1',NULL)" );
            if( !mysql_query( $q ) ) {
                print( "Ошибка при добавлении в ps_image_lang: " . mysql_error() );
                return false;
            }
            print( "Добавлена запись в ps_image_lang<br>\n" );

            $q = sprintf( "INSERT INTO `ps_image_shop` ( `id_image`, `id_shop`,`cover`) VALUES ({$image_id},'1','1')" );
            if( !mysql_query( $q ) ) {
                print( "Ошибка при добавлении в ps_image_shop: " . mysql_error() );
                return false;
            }
            print( "Добавлена запись в ps_image_shop<br>\n" );

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
        }
    } else { // Конец добавления
        // Обновление (редактирование) товара '2015-03-18 21:40:59'
        echo "<h2>Обновление</h2>\n";
        $date_upd = date( "Y-m-d H:i:s" );
        $q = "UPDATE ps_product "
            . "SET "
                . "id_category_default={$categoryId}, "
                . "price={$final_price}, "
                . "reference='{$_REQUEST[ "new_articul" ]}', "
                . "date_upd='{$date_upd}', "
                . "parts='{$parts}', "
                . "content_desc='{$content_description}' "
            . "WHERE id_product={$product_id}";
        if( !mysql_query( $q ) ) {
            print( "Ошибка при обновлении ps_product: " . mysql_error() . "<br>\n" );
            print( "Текст запроса: " . $q . "<br>\n");
            return false;
        }
        print( "Обновлена запись в ps_product, id = " . $product_id . "<br>\n" );

        $q = "UPDATE `ps_category_product` SET id_category={$categoryId} WHERE id_product={$product_id}";
        if( !mysql_query( $q ) ) {
            print( "Ошибка при обновлении ps_category_product: " . mysql_error() );
            return false;
        }
        print( "Обновлена запись в ps_category_product<br>\n" );

        $q = "UPDATE ps_product_lang "
                . "SET "
                    . "description='{$_REQUEST[ "new_description" ]}', "
                    . "name='{$_REQUEST[ "new_name" ]}' "
                . "WHERE id_product={$product_id}";
        
        if( !mysql_query( $q ) ) {
            print( "Ошибка при обновлении ps_product_lang: " . mysql_error() );
            return false;
        }
        print( "Обновлена запись в ps_product_lang<br>\n" );
        
        $q = "UPDATE ps_product_shop "
                . "SET "
                    . "id_category_default={$categoryId}, "
                    . "price={$final_price}, "
                    . "date_upd='{$date_upd}' "
                . "WHERE id_product={$product_id}";
        if( !mysql_query( $q ) ) {
            print( "Ошибка при обновлении ps_product_shop: " . mysql_error() );
            return false;
        }
        print( "Обновлена запись в ps_product_shop<br>\n" );
        
    }
    return $product_id;
}

if( $num = filter_input( INPUT_GET, "num", FILTER_VALIDATE_INT ) ) {
    //echo "Session: ";
    //print_r( $_SESSION[ "parts_categories" ] );
    print_item_selector( $_SESSION[ "parts_categories" ], "", "", $num );
}
?>

