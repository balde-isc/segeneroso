function login() { window.location('') }
function donate() { window.location.href = '/donar.html' }
function solicitarApoyo() { window.location.href = '/apoyar.html' }
function donarApoyar() { window.location.href = '/donar.html' }
function donar() { window.location.href = '/donar.html' }
function apoyar() { window.location.href = '/apoyar.html' }
$(document).ready(function () {
    // Función para enviar formularios
    function submitForm(formId, apiEndpoint) {
        const form = $('#' + formId);
        const resultDiv = $('#result' + formId.slice(-1));
        const submitBtn = form.find('button[type="submit"]');

        form.on('submit', function (e) {
            e.preventDefault();

            // Validación Bootstrap 5
            if (!this.checkValidity()) {
                e.stopPropagation();
                form.addClass('was-validated');
                return;
            }

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
                        Swal.fire({
                            title: "¡Solicitud enviada correctamente!",
                            text: "Hemos recibido tu solicitud de apoyo. Nuestro equipo la revisará y te contactaremos para conectarte lo más pronto posible.",
                            icon: "success"
                        });
                        resultDiv.html(`
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> 
                                ${response.message || '¡Formulario enviado correctamente!'}
                            </div>
                        `);
                        form[0].reset(); // Limpiar formulario
                        form.removeClass('was-validated'); // Quitar clase de validación
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
                    Swal.fire({
                        title: "No pudimos enviar tu solicitud",
                        text: "Lo sentimos, experimentamos dificultades técnicas. Tu información es importante para nosotros, por favor intenta nuevamente o contáctanos directamente.",
                        icon: "error"
                    });
                },
                complete: function (e) {
                    e.preventDefault();
                    // Ocultar loading
                    submitBtn.find('.submit-text').show();
                    submitBtn.find('.loading').hide();
                    submitBtn.prop('disabled', false);
                }
            });
        });
    }

    submitForm('form1', 'form1.php'); //DONAR
    submitForm('form2', 'form2.php'); //APOYO
    $('#telefono3').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10);
        }
    });

});
