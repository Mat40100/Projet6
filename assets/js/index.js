document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});


$(document).ready(function () {
    $(function() {
        $('#loadMore').click(function() {

            var tricksNumber = $('.Trick').length;

            $.ajax({
                url : '/loadMore',
                dataType : 'html',
                type: 'GET',
                data: {
                    tricksNumber: tricksNumber
                },
                success : function(code_html, statut){
                    $('#Tricks').html(code_html);
                    tricksNumber = tricksNumber + 5;
                },
                error : function(resultat, statut, erreur){
                    console.log(resultat);
                    console.log("Ajax error");
                }
            });
        })
    });
});