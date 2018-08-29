$(document).ready(function () {

    let hidden= true;

    $("#seeMediaList").click(function (e) {

        if (hidden === true) {
            $("#mediasList").addClass('d-flex').addClass('flex-column').removeClass('d-none');
            $("#seeMediaList").text('Cacher les images et les vidéos');
            hidden = false;
        } else {
            $('#mediasList').removeClass('d-flex').removeClass('flex-column').addClass('d-none');
            $("#seeMediaList").text('Voir les images et les vidéos');
            hidden = true;
        }

        e.preventDefault();
        return false;
    });
});