'use strict';

$(function() {
    $('#enemyField td').click(function() {
        var uncovered = $(this).hasClass('uncovered');

        if (uncovered) {
            var x = $(this).data('x');
            var y = $(this).data('y');

            var url = window.location.href.split('?')[0];
            window.location = url + '?x=' + x + '&y=' + y;
        }
    });
});