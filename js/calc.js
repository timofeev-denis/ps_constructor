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
    
    $( "#duplicate" ).click( function() {
        $( ".item" ).each( function() {
            var num = $( this ).data( "number" );
            //console.log(num);
            console.log( $( this ).find("option:selected").text() );
            $(".item_name[data-number='" + num + "']").val( $( this ).find("option:selected").text() );
        });
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
});

function styling() {
    // Styling
    $( "input[type=submit]" ).button();
    $( "#accordion" ).accordion();

    var availableTags = [
            "ActionScript",
            "AppleScript",
            "Asp",
            "BASIC",
            "C",
            "C++",
            "Clojure",
            "COBOL",
            "ColdFusion",
            "Erlang",
            "Fortran",
            "Groovy",
            "Haskell",
            "Java",
            "JavaScript",
            "Lisp",
            "Perl",
            "PHP",
            "Python",
            "Ruby",
            "Scala",
            "Scheme"
    ];
    $( "#autocomplete" ).autocomplete({
            source: availableTags
    });


    $( "#radioset" ).buttonset();



    $( "#tabs" ).tabs();



    $( "#dialog" ).dialog({
            autoOpen: false,
            width: 400,
            buttons: [
                    {
                            text: "Ok",
                            click: function() {
                                    $( this ).dialog( "close" );
                            }
                    },
                    {
                            text: "Cancel",
                            click: function() {
                                    $( this ).dialog( "close" );
                            }
                    }
            ]
    });

    // Link to open the dialog
    $( "#dialog-link" ).click(function( event ) {
            $( "#dialog" ).dialog( "open" );
            event.preventDefault();
    });



    $( "#datepicker" ).datepicker({
            inline: true
    });



    $( "#slider" ).slider({
            range: true,
            values: [ 17, 67 ]
    });



    $( "#progressbar" ).progressbar({
            value: 20
    });



    $( "#spinner" ).spinner();



    $( "#menu" ).menu();



    $( "#tooltip" ).tooltip();

    $( "select" ).selectmenu();


    // Hover states on the static widgets
    $( "#dialog-link, #icons li" ).hover(
            function() {
                    $( this ).addClass( "ui-state-hover" );
            },
            function() {
                    $( this ).removeClass( "ui-state-hover" );
            }
    );

}

