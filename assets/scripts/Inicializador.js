var Inicializador = (function ($) {

    var formInit = $('#formInit');

    /**
     * Realiza o bind de eventos para submissão do formulário
     */
    var bindForm = function () {
        formInit.on('click','#btnSubmit', function () {
            formInit.validate({
                rules: rules(),
                messages: messages(),
                errorPlacement: function ( error, element ) {
                    element.attr('title',error.text());
                },
                highlight: function ( element, errorClass, validClass ) {
                    $( element ).parents( "div" ).addClass( "has-error" ).removeClass( "has-success" );
                },
                unhighlight: function (element, errorClass, validClass) {
                    $( element ).parents( "div" ).addClass( "has-success" ).removeClass( "has-error" ).removeAttr('title');
                }

            });

            if (formInit.valid()) {
                $.ajax({
                    type: 'POST',
                    url: 'init',
                    data: formInit.serialize(),
                    success: function ( data ) {
                        alert(JSON.stringify(data));
                    },
                    error: function () {
                        alert('Falha ao submeter formulário');
                    }
                });
            }

        });
    };

    /**
     * Rules para validate
     */
    var rules = function () {
        return {
            "nome": {
                required: true
            },
            "sigla": {
                required: true
            },
            "hz": {
                required: true,
                number: true
            },
            "host": {
                required: true
            },
            "usuario": {
                required: true
            },
            "senha": {
                required: true
            },
            "porta": {
                required: true,
                number: true
            }
        };
    };

    /**
     * Mensagens
     */
    var messages = function () {
        return {
            "nome": {
                required: "Informe o nome da instância"
            },
            "sigla": {
                required: "Informe a sigla"
            },
            "hz": {
                required: "Informe a frequência de corrente",
                number: "Informe apenas números para frequência"
            },
            "host": {
                required: "Informe o host do local"
            },
            "usuario": {
                required: "Informe o usuário do BD"
            },
            "senha": {
                required: "Informe a senha do BD"
            },
            "porta": {
                required: "Informe a porta do BD",
                number: "Informe apenas números para a porta"
            }
        };
    };


    return {
        init: function () {
            bindForm();
        }
    };

})(jQuery);


$(document).ready(function () {
    Inicializador.init();
});