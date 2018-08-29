$(document).ready(function () {
    var $container = $('div#medias');
    let index = $container.find(':input').length;

    function addMediaForm($container) {
        try {
            var template = $container.attr('data-prototype')
                .replace("__name__label__", '')
                .replace("[__name__]",'['+index+']')
                .replace("[__name__]",'['+index+']')
            ;
            var $prototype = $(template);

        } catch (e) {
            console.log("YO", e)
        }

        addDeleteLink($prototype);

        $container.append($prototype);

        index++;
    }

    function addDeleteLink($prototype) {
        var $deleteLink = $('<a href="#" class="btn btn-danger ml-4 mr-4">Supprimer</a>');

        $prototype.append($deleteLink);

        $deleteLink.click(function (e) {
            $prototype.remove();

            e.preventDefault();
            return false;
        });
    }

    $('#add_media').click(function (e) {
        addMediaForm($container);

        e.preventDefault();
        return false;
    });

    if (index > 0) {
        $container.find("legend").each(function () {
            $(this).hide();
        });
        $container.children(".media").each(function(){
            addDeleteLink($(this));
            var allInputs = $(this).find(":input:file");
            allInputs.parent("div").hide();
            $(this).find('label').hide();
        });
    }
});

$(document).ready(function () {
    var $container = $('div#videos');
    let index = $container.find(':input').length;

    function addVideoForm($container) {
        try {
            var template = $container.attr('data-prototype')
                .replace("__name__label__", '')
                .replace("trick_videos___name__",'trick_videos_'+ index)
                .replace("trick_videos___name___url",'trick_videos_url" placeholder="Url"'+ index )
                .replace("[__name__]",'['+index+']')
                .replace("[__name__]",'['+index+']')
            ;

            var $prototype = $(template);

        } catch (e) {
            console.log("Problem", e)
        }

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

    $('#add_videos').click(function (e) {
        addVideoForm($container);

        e.preventDefault();
        return false;
    });

    if (index > 0) {
        $container.find("legend").each(function () {
            $(this).hide();
        });
        $container.children(".video").each(function(){
            addDeleteLink($(this));
        });
    }
});

$(document).ready(function(){
   var $videos = $('div#trick_videos');
   var $medias = $('div#trick_medias');

   $medias.parent("fieldset").hide();
   $videos.parent("fieldset").hide();
});