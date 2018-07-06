$(document).ready(function(){
    $("select#sous_question_ouverte_question_pre_requis").on('change',function(){
        var idSelected = $(this).find(':selected').val();
        var oldSelected = $("select#sous_question_ouverte_reponse").find(':selected').val();
        if(idSelected !== ''){
            $.ajax({
                url: window.location.protocol + '//' + window.location.host + '/admin/reponses/get/' + idSelected,
                success: function (data) {
                    $("select#sous_question_ouverte_reponse").html(data);
                    if(oldSelected != undefined && $("select#sous_question_ouverte_reponse").find("option[value='"+oldSelected+"']").length > 0)
                    {
                        $("select#sous_question_ouverte_reponse").val(oldSelected);
                    }

                }
            });
        }else{
            $("select#sous_question_ouverte_reponse").html('');
        }
    });


    $("select#sous_question_ouverte_question_pre_requis").trigger('change');

});