$(document).ready(function () {
    var $container = $('div#trick_medias');
    let index = $container.find(':input').length;

    $('#add_media').click(function (e) {
        addMediaForm($container);

        e.preventDefault();
        return false;
    });

    if (index != 0) {
        $container.children('div').each(function () {
            addDeleteLink($(this));
        })
    }

    function addMediaForm($container) {
        try {
            var template = $container.attr('data-prototype')
                .replace("__name__label__", '')
                .replace("[__name__]",'['+index+']')
                .replace("[__name__]",'['+index+']')
            ;
        } catch (e) {
            console.log("YO", e)
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

$(document).ready(function () {
    var $container = $('div#trick_videos');
    let index = $container.find(':input').length;

    $('#add_videos').click(function (e) {
        addVideoForm($container);

        e.preventDefault();
        return false;
    });

    if (index != 0) {

        $container.children('div').each(function () {
            addDeleteLink($(this));
        })
    }

    function addVideoForm($container) {
        try {
            var template = $container.attr('data-prototype')
                .replace(/__name__label__/, '')
                .replace(/trick_videos___name__/,'trick_videos_'+ index )
                .replace(/trick_videos___name___url/,'trick_videos_url" placeholder="Url"'+ index )
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