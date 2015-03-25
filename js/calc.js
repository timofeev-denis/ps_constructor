$( document ).ready( function() {
    var item_num = 2;
    $( ".add_item" ).click( function() {
        console.log( "More!" );
        $.ajax({
            //dataType: "json",
            url: "common.php",
            data: {
                num: item_num
            },
            success: function(data) {
                item_num++;
                $( "#parts" ).append( data );
            }
        });
    });
    
    //$( ".item" ).change( function() {
    $( document.body ).on( "change", ".item", function() {
        var num = $(this).data( "number" );
        // Сохраняем в скрытом поле наименование запчасти (без цены)
        $(".item_name[data-number='" + num + "']").val( $( this ).find("option:selected").data( "partname" ) );
        $(".item_tid[data-number='" + num + "']").val( $( this ).find("option:selected").data( "parttid" ) );
    });
    
    $( "#new_category" ).change(function() {
        //console.log( $(this).val() );
//      $("#new_subcategory option").remove();
        
        var num = $(this).data( "number" );
        if( num == "0" ) {
            var table_name = "goods_table_name";
        } else {
            var table_name = "parts_table_name";
        }
        $.ajax({
            dataType: "json",
            url: "get_subcategories.php",
            data: {
                category: $(this).val(),
                table: table_name
            },
            success: function(data) {
                var options = "";
                for( var i = 0; i < data.length; i++ ) {
                    //console.log( data[ i ] );
                    options += "<option value='" + data[ i ][ "id" ] + "'>" + data[ i ][ "name" ] + "</option>";              
                }
                $("#add_subcategory").prop( "disabled", false );
                $(".subcategory[data-number='" + num + "']").prop('disabled', false);
                $(".subcategory[data-number='" + num + "'] option").remove();
                $(".subcategory[data-number='" + num + "']").append(options);
            }
        });
    });
    
    //$( ".item_category" ).change(function() {
    $( document.body ).on( "change", ".item_category", function() {
        console.log( $(this).val() );
//      $("#new_subcategory option").remove();
        
        var num = $(this).data( "number" );
        if( num == "0" ) {
            var table_name = "goods_table_name";
        } else {
            var table_name = "parts_table_name";
        }
        $.ajax({
            dataType: "json",
            url: "get_item_subcategories.php",
            data: {
                category: $(this).val(),
                table: table_name
            },
            success: function(data) {
                var options = "";
                for( var i = 0; i < data.length; i++ ) {
                    //console.log( data[ i ] );
                    options += "<option value='" + data[ i ] + "'>" + data[i] + "</option>";              
                }
                $("#add_subcategory").prop( "disabled", false );
                $(".subcategory[data-number='" + num + "']").prop('disabled', false);
                $(".subcategory[data-number='" + num + "'] option").remove();
                $(".subcategory[data-number='" + num + "']").append(options);
            }
        });
    });
    
    //$( ".subcategory" ).change(function() {
    $( document.body ).on( "change", ".subcategory", function() {
        //console.log( $(this).val() );
        var num = $(this).data( "number" );
        $.ajax({
            dataType: "json",
            url: "get_parts.php",
            data: {subcategory: $(this).val()},
            success: function(data) {
                var options = "";
                for( var i = 0; i < data.length; i++ ) {
                    //console.log( data[ i ] );
                    options += "<option value='" + data[ i ][ "price" ] + "' data-parttid='" + data[ i ][ "tid" ] + "' data-partname='" + data[ i ][ "name" ] + "'>" + data[ i ][ "item" ] + "</option>";              
                }
                $(".item[data-number='" + num + "']").prop('disabled', false);
                $(".item[data-number='" + num + "'] option").remove();
                $(".item[data-number='" + num + "']").append(options);
            }
        });
    });
   
    $("#add_category").click(function() {
       $("#new_category").css( "display", "none" );
       $("#new_category").prop( "disabled", true );
       $("#new_custom_category").prop( "disabled", false );
       $("#new_custom_category").css( "display", "inline" );
       $("#new_custom_category").focus();
       // Показываем поле ввода наименования новой подкатегории
       $("#new_subcategory").css( "display", "none" );
       $("#new_subcategory").prop( "disabled", true );
       $("#new_custom_subcategory").prop( "disabled", false );
       $("#new_custom_subcategory").css( "display", "inline" );
    });
    
    $("#add_subcategory").click(function() {
       $("#new_subcategory").css( "display", "none" );
       $("#new_subcategory").prop( "disabled", "true" );
       $("#new_custom_subcategory").css( "display", "inline" );
       $("#new_custom_subcategory").focus();
    });
    
    $( document.body ).on( "click", ".remove_item", function() {
        var num = $(this).data( "number" );
        $( ".component[data-number='" + num + "']" ).remove();
    });
    
    $(".delete").click(function() {
        var id_product = $(this).data( "id_product" );
//        console.log( num );
    $('<div></div>').appendTo('body')
        .html( "<div>Данный товар будет удалён:<h4>[" + $(this).data( "product_reference" ) + "] " + $(this).data( "product_name" ) + "</h4>Продолжить?</div>" )
        .dialog({
            modal: true,
            title: 'Удаление товара',
            zIndex: 10000,
            autoOpen: true,
            width: 'auto',
            resizable: false,
            buttons: {
                Да: function () {
                    $.ajax({
                        //dataType: "json",
                        url: "delete_goods.php",
                        data: {id: id_product},
                        success: function(data) {
                            console.log( data );
                            location.reload();
                            //console.log( "DELETED: " + data );
                            //$(".item[data-number='" + num + "'] option").remove();
                        }
                    });
                    $(this).dialog("close");
                },
                Нет: function () {
                    $(this).dialog("close");
                }
            },
            close: function (event, ui) {
                $(this).remove();
            }
        });            
    });
});
