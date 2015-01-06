$(document).ready(function() {

    var ellipsisElements = $('*[data-ellipsis]');
    if (ellipsisElements.length != 0) {
        ellipsisElements.each(function(i, elt) {
            var options = {
                row: $(elt).data('ellipsis-row') || 1,
                onlyFullWords: true,
                char: $(elt).data('ellipsis-char') || '…',
                allowFullText: true,
                position: $(elt).data('ellipsis-position') || 'tail'
            };
            $(elt).ellipsis(options);
        });
    }
});