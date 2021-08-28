$('.custom-file-input').on('change', function(e) {
    var inputFile = e.currentTarget;
    $(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
});

//******************* Load More Trick **********************//
$(document).ready(function() {
    $(".content").slice(0, 5).show();
    $("#loadMore").on("click", function(e) {
        e.preventDefault();
        $(".content:hidden").slice(0, 5).slideDown();
        if ($(".content:hidden").length == 0) {
            $("#loadMore").fadeOut("slow");
        }
    });
})

//******************* Load More Comment **********************//
$(document).ready(function() {
    $(".contentCom").slice(0, 2).show();
    $("#loadMoreC").on("click", function(e) {
        e.preventDefault();
        $(".contentCom:hidden").slice(0, 2).slideDown();
        if ($(".contentCom:hidden").length == 0) {
            $("#loadMoreC").fadeOut("slow");
        }
    });
})

//******************* See More media **********************//
// $(document).ready(function() {
//     $(".seeMedia").slice(0, 0).show();
//     $("#loadMedia").on("click", function(e) {
//         e.preventDefault();
//         $(".seeMedia:hidden").slice(0, 5).slideDown();
//         if ($(".seeMedia:hidden").length == 0) {
//             $("#loadMedia").fadeOut("slow");
//         }
//     });
// })

if (window.matchMedia("(max-width: 769px)").matches) {
    $(document).ready(function() {
        $(".seeMedia").slice(0, 0).show();
        $("#loadMedia").on("click", function(e) {
            e.preventDefault();
            $(".seeMedia:hidden").slice(0, 5).slideDown();
            if ($(".seeMedia:hidden").length == 0) {
                $("#loadMedia").fadeOut("slow");
            }
        });
    })
} else {
    $(document).ready(function() {
        $(".seeMedia").show();
        if ($(".seeMedia:hidden").length == 0) {
            $("#loadMedia").fadeOut("slow");
        }
    })
}