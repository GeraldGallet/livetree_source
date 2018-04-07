$(document).ready(function () {
    $('.inscription').on('click',function (e) {
        e.preventDefault();
        var $link = $(e.currentTarget);
        $link.toggleClass("highlight");

        $.ajax({
            methode:'POST',
            url: $link.attr('href')
        }).done(function(data){
            $('.inscription-number').html(data.hearts)

        })

            });
});