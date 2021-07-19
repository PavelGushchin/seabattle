"use strict";

$(function() {
    $('#shootingBoard td').click(function() {
        let empty = $(this).hasClass('empty');

        if (empty) {
            let x = $(this).data('x');
            let y = $(this).data('y');

            let url = window.location.href.split('?')[0];
            window.location = url + '?x=' + x + '&y=' + y;
        }
    });
});