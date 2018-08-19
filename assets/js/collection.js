// setup an "add a tag" link

var collection = {};

var listesChoixQuestion;

function selectedDoIt(){
    listesChoixQuestion = $("select[data-selected='data-selected']");


    listesChoixQuestion.each(function() {
        $(this).on('change',function(){
            var idSelected = $(this).find(':selected').val();

            const regex = /\d+/;
            let m;

            var reponse = null;
            if ((m = regex.exec($(this).attr('id'))) !== null) {
                m.forEach((match, groupIndex) => {
                    reponse = $("select[id$='_" + match + "_reponse'][data-reponses='data-reponses']").first()
                });
            }else{
                reponse = $("select[data-reponses='data-reponses']").first();
            }

            var oldSelected = reponse.find(':selected').val();

            if(idSelected !== ''){
                $.ajax({
                    url: window.location.protocol + '//' + window.location.host + '/admin/reponses/get/' + idSelected,
                    success: function (data) {
                        reponse.html(data);
                        if(oldSelected != undefined && reponse.find("option[value='"+oldSelected+"']").length > 0)
                        {
                            reponse.val(oldSelected);
                        }

                    }
                });
            }else{
                reponse.html('');
            }
        });
    });

    listesChoixQuestion.trigger('change');
}

function addForm($collectionHolder, $newLink, prefix) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your tags field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    $newLink.before($(newForm));

    addFormDelete($('div#' + prefix + index));
    selectedDoIt();


}

function addFormDelete($form) {
    var $removeFormLink = $('<a href="#" id="remove_reponse"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Supprimer cette réponse</a>');
    $form.append($removeFormLink);



    $($removeFormLink).on('click', function(e) {
        e.preventDefault();
        // remove the li for the tag form
        $form.remove();
    });
}

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $("div[data-collection='data-collection']").each(function (index) {

        var $addReponse = $('<a href="#" id="add_reponse_' + index + '"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Ajouter une réponse</a>');
        collection[index] = $(this);

        var prefix = collection[index].attr('id') + "_";

        // add a delete link to all of the existing tag form li elements
        collection[index].find("div[id^='" + prefix + "']").each(function () {
            addFormDelete($(this))
        });

        // add the "add a tag" anchor and li to the tags ul
        collection[index].append($addReponse);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        collection[index].data('index', $(this).find(':input').length);

        $addReponse.on('click', function (e) {
            e.preventDefault();

            // add a new tag form (see next code block)
            addForm(collection[index], $(this), prefix);
        });

    });

    selectedDoIt();
});