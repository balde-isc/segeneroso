function login() { alert("Redirigiendo al login..."); }
function donate() { alert("Gracias por tu donación ❤️"); }
function solicitarApoyo() { alert("Formulario de solicitud de apoyo..."); }
function donarApoyar() { alert("Opción de donar y apoyar seleccionada"); }
function donar() { alert("Haz donado con éxito 💚"); }
function apoyar() { alert("Haz apoyado con éxito 💚"); }
$(document).ready(function () {
    // Función para enviar formularios
    function submitForm(formId, apiEndpoint) {
        const form = $('#' + formId);
        const resultDiv = $('#result' + formId.slice(-1));
        const submitBtn = form.find('button[type="submit"]');

        form.on('submit', function (e) {
            e.preventDefault();

            // Mostrar loading
            submitBtn.find('.submit-text').hide();
            submitBtn.find('.loading').show();
            submitBtn.prop('disabled', true);

            // Limpiar resultados anteriores
            resultDiv.html('');

            // Obtener datos del formulario
            const formData = form.serialize();

            // Enviar con AJAX
            $.ajax({
                url: 'api/' + apiEndpoint,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        resultDiv.html(`
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i> 
                                        ${response.message || '¡Formulario enviado correctamente!'}
                                    </div>
                                `);
                        form[0].reset(); // Limpiar formulario
                    } else {
                        resultDiv.html(`
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        ${response.message || 'Error al procesar el formulario'}
                                    </div>
                                `);
                    }
                },
                error: function () {
                    resultDiv.html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle"></i> 
                                    Error de conexión. Por favor, intenta nuevamente.
                                </div>
                            `);
                },
                complete: function () {
                    // Ocultar loading
                    submitBtn.find('.submit-text').show();
                    submitBtn.find('.loading').hide();
                    submitBtn.prop('disabled', false);
                }
            });
        });
    }

    // Configurar formularios
    submitForm('form1', 'form1.php');
    submitForm('form2', 'form2.php');
});