function clickDiv() {
    $(".click-div").click(function () {
        if ($(this).find("a").length) {
            window.location.href = $(this).find("a:first").attr("href");
        }
    });
}
;

$(document).ready(function () {
    $(function () {
        $.nette.init();
    });

    var $loading = $('#loading').hide();
    $(document)
            .ajaxStart(function () {
                $loading.show();
            })
            .ajaxStop(function () {
                $loading.hide();
            });
    clickDiv();

    $('.navbar-header button').click(function () {
        $(this).toggleClass('open');
    });

    $('.main-finder area').hover(function () {
        var title = $(this).attr('title');
        $('#name-region h3').remove();
        $('#name-region').append('<h3>' + title + '</h3>');
    }, function () {
        $('#name-region h3').remove();
        $('#name-region').append('<h3>Nevybrali jste žádný kraj...</h3>');
    });
    
    $('footer li a').hover(function() {
        var i = '<i class="fa fa-arrow-right animation-bot" aria-hidden="true"></i>';
        $('.animation-bot').animate({opacity: 1});
        $(this).children('span').append(i);
    }, function() {
        $(this).find('i').remove();
    });
});

