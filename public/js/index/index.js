$(document).ready(function() {

    $('ul#choosePlatform > li > a').click(function(e) {
        e.preventDefault();
        $('#platform').val($(this).data('value'));

        var caretHtml = '<span class="caret"></span>';
        $('#btn-choosePlatform')
            .text($(this).text() + ' ')
            .append(caretHtml)
            .attr('aria-expanded', 'false')
            .parent().removeClass('open');
        return false;
    });
});