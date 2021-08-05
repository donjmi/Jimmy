$('.custom-file-input').on('change', function (e) {
    var inputFile = e.currentTarget;
    $(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
});

//******************* Load More Trick **********************//
$(document).ready(function () {
    $(".content").slice(0, 5).show();
    $("#loadMore2").on("click", function (e) {
        e.preventDefault();
        $(".content:hidden").slice(0, 5).slideDown();
        if ($(".content:hidden").length == 0) {
            $("#loadMore2").fadeOut("slow");
        }
    });
})

//******************* Load More Comment **********************//
$(document).ready(function () {
    $(".contentCom").slice(0, 2).show();
    $("#loadMoreC").on("click", function (e) {
        e.preventDefault();
        $(".contentCom:hidden").slice(0, 2).slideDown();
        if ($(".contentCom:hidden").length == 0) {
            $("#loadMoreC").fadeOut("slow");
        }
    });
})
