var listesChoixQuestion;
var listesChoixReponse;

$(document).ready(function(){

    listesChoixQuestion = $("select[data-selected='data-selected']").first();
    listesChoixReponse = $("select[data-reponses='data-reponses']").first();


    listesChoixQuestion.on('change',function(){
        var idSelected = $(this).find(':selected').val();
        var oldSelected = listesChoixReponse.find(':selected').val();
        if(idSelected !== ''){
            $.ajax({
                url: window.location.protocol + '//' + window.location.host + '/admin/reponses/get/' + idSelected,
                success: function (data) {
                    listesChoixReponse.html(data);
                    if(oldSelected != undefined && listesChoixReponse.find("option[value='"+oldSelected+"']").length > 0)
                    {
                        listesChoixReponse.val(oldSelected);
                    }

                }
            });
        }else{
            listesChoixReponse.html('');
        }
    });


    listesChoixQuestion.trigger('change');

});