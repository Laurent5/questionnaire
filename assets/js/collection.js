var $collectionHolder;
var prefix;

// setup an "add a tag" link
var $addReponse = $('<a href="#" id="add_reponse"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Ajouter une réponse</a>');

function addForm($collectionHolder, $newLink) {
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


}

function addFormDelete($form) {
    var $removeFormLink = $('<a href="#" id="add_reponse"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Supprimer cette réponse</a>');
    $form.append($removeFormLink);



    $($removeFormLink).on('click', function(e) {
        e.preventDefault();
        // remove the li for the tag form
        $form.remove();
    });
}



jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $("div[data-collection='data-collection']").first();
    prefix = $collectionHolder.attr('id') + "_";

    // add a delete link to all of the existing tag form li elements
    $collectionHolder.find("div[id^='" + prefix + "']").each(function() {
        addFormDelete($(this));
    });

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($addReponse);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addReponse.on('click', function(e) {
        e.preventDefault();
        // add a new tag form (see next code block)
        addForm($collectionHolder, $addReponse);
    });
});