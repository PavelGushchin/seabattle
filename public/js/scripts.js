"use strict";

$(function() {
    $("#shootingBoard td").click(function() {
        let isEmpty = $(this).hasClass("empty")

        if (isEmpty) {
            let x = $(this).data("x")
            let y = $(this).data("y")

            window.location = window.location.origin + "?x=" + x + "&y=" + y
        }
    })
})