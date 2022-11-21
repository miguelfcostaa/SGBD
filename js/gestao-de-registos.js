var $registrationForm = $('#registo');
if($registrationForm.length){
    $registrationForm.validate({
        rules: {
            nome_completo: {
                required: true
            },
            data_de_nascimento: {
                required: true
            }
        },
        messages: {
            nome_completo: {
                required: 'Obrigatório!'
            },
            data_de_nascimento: {
                required: 'Obrigatorio!'
            }
        }
    });
}
