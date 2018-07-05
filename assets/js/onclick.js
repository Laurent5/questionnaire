function change(value){
    var id = $(value).attr("id");
    var selectElement = $("#" + id + " input[type='radio']:checked").val();
    var routeId = $(value).attr("data-route-"+selectElement);

    if(routeId == null){
        $("#insert_" + id).html(null);
    }else{
        $.ajax({
            url: "/get/questions/for/" + selectElement,
            dataType: "html",
            success: function(data){
                $("#insert_" + id).html(data);
                $("#insert_" + id).find('[data-expensed]').each(function(index,value){
                    handler(value);
                });
            }
        })
    }

}

function handler(value){
    var id = $(value).attr("id");
    $(value).after("<div id ='insert_" + id + "'></div>");
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