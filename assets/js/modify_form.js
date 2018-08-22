$(document).ready(function () {
    var $prototype = $('div#trick_medias');
    var $container = $('#medias');
    let index = ($('#medias').find(':input').length)/2;

    $('#add_media').click(function (e) {
        addMediaForm($container);

        e.preventDefault();
        return false;
    });

    if (index !== 0) {
        $container.children('div').each(function () {
            addDeleteLink($(this));
        })
    }

    function addMediaForm($container) {
        try {
            var template = $prototype.attr('data-prototype')
                .replace("___name__", '_'+index)
                .replace("[__name__]",'['+index+']')
                .replace("___name___", '_'+index+'_')
                .replace("[__name__]",'['+index+']')
            ;
        } catch (e) {
            console.log("YO", e)
        }


        var $newform = $(template);

        addDeleteLink($newform);

        $container.append($newform);

        index++;
    }

    function addDeleteLink($prototype) {
        var $deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');

        $prototype.append($deleteLink);

        $deleteLink.click(function (e) {
            $prototype.remove();

            e.preventDefault();
            return false;
        });
    }
});

$(document).ready(function () {
    var $container = $('div#videos');
    let index = $container.find(':input').length;

    $('#add_videos').click(function (e) {
        addVideoForm($container);

        e.preventDefault();
        return false;
    });

    if (index !== 0) {

        $container.children('div').each(function () {
            addDeleteLink($(this));
        })
    }

    function addVideoForm($container) {
        try {
            var template = $('#videos').attr('data-prototype')
                .replace(/trick_videos___name__/,'trick_videos_'+ index )
                .replace(/trick_videos___name___url/,'trick_videos_'+ index +'_url')
                .replace("[__name__]",'['+index+']')
                .replace("[__name__]",'['+index+']')
            ;
        } catch (e) {
            console.log("Problem", e)
        }


        var $prototype = $(template);

        addDeleteLink($prototype);

        $container.append($prototype);

        index++;
    }

    function addDeleteLink($prototype) {
        var $deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');

        $prototype.append($deleteLink);

        $deleteLink.click(function (e) {
            $prototype.remove();

            e.preventDefault();
            return false;
        });
    }
});