function change(value){
    var id = $(value).attr("id");
    var selectElement = undefined;

    if($("#" + id + " input[type='radio']:checked").length){
        var selectElement = $("#" + id + " input[type='radio']:checked").val();
        insert(selectElement,"#insert_" + id,value)
    }else{
        $("#" + id + " input[type='checkbox']").each(function(index,inputValue){
            var insertZoneName = "#insert_" + id + "_" + $(inputValue).val();

            if($(insertZoneName).length){
                if($(inputValue).is(":checked") && $(insertZoneName).html() == ''){
                    insert($(inputValue).val(),insertZoneName,value);
                }else{
                    if(!$(inputValue).is(":checked")){
                        $(insertZoneName).html('');
                    }
                }
            }
        });
    }

}

function insert(elementId,idName,parent){
    var routeId = $(parent).attr("data-route-"+elementId);

    if(routeId == null){
        $(idName).html(null);
    }else{
        $("#form_Suivant").attr("disabled", "disabled").html("Le questionnaire s'adapte, merci de patienter ...");
        $.ajax({
            url: "/get/questions/for/" + elementId,
            dataType: "html",
            success: function(data){
                $(idName).html(data);
                $(idName).find('[data-expensed]').each(function(index,value){
                    handler(value);
                });
                $("#form_Suivant").removeAttr("disabled").html("Suivant");
            }
        })
    }
}

function handler(value){
    var id = $(value).attr("id");
    if($(value).attr("data-multiple") != undefined && $(value).attr("data-multiple") != false){
            $("#" + id + " input[type='checkbox']").each(function(index,inputValue){
                var reference = $(inputValue).val();
                if($(value).attr("data-route-" + reference) != undefined){
                    $(value).parent().after("<div id ='insert_" + id + "_" + reference +"'></div>");
                }
            });
    }else{
        $(value).parent().after("<div id ='insert_" + id + "'></div>");
    }

    $(value).on("change",function () {
        change(value);
    });
    change(value);
}

$(document).ready(function(){
    $.each($('[data-expensed]'),function(index,value){
        handler(value);
    });
});