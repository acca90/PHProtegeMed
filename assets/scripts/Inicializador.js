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

                var data = formInit.serialize();
                var disableds = formInit.find('input').attr('disabled','disabled');

                $.ajax({
                    type: 'POST',
                    url: 'init',
                    data: data,
                    success: function ( data ) {
                        if (notEmpty(data) && data.error == 'sucesso') {
                            disableds.removeAttr('disabled').val('');
                            alert('Instância replicada com sucesso');
                        } else {
                            disableds.removeAttr('disabled');
                            alert('Falha na inicialização da replicação');
                        }
                    },
                    error: function () {
                        disableds.removeAttr('disabled');
                        alert('Falha ao submeter formulário');
                    }
                });
            }

        });
    };

    /**
     * Verifica se o valor informado não é nulo ou vazio
     *
     * @param dado
     * @returns {boolean}
     */
    var notEmpty = function ( dado ) {
        return dado !== null && dado !== undefined && dado !== '';
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
            },
            "arqlog": {
                required: true
            },
            "poslog": {
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
            },
            "arqlog": {
                required: "Informe o arquivo de logs do MySQL"
            },
            "poslog": {
                required: "Informe a posição inicial de leitura do log",
                number: "Informe apenas numeros para posição de leitura do log"
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