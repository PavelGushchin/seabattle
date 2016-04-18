$(function() {
    $('#enemyField td').click(function() {
        var x = $(this).data('x');
        var y = $(this).data('y');
        window.location = 'http://' + document.location.host + '?x=' + x + '&y=' + y;
    });

    $('#startGame').click(function() {
        //console.log($(this).parent());
    });


});