$(function() {
    $('#enemyField td').click(function() {
        var uncovered = $(this).hasClass('uncovered');

        if (uncovered) {
            var x = $(this).data('x');
            var y = $(this).data('y');
            window.location = 'http://' + document.location.host + '?x=' + x + '&y=' + y;
        }
    });
});