jQuery(document).ready(function($) {
    // Adicionar novo balão
    $('#add-balloon').on('click', function(e) {
        e.preventDefault();
        
        var count = $('.balloon-item').length;
        
        var template = `
            <div class="balloon-item">
                <h4>Balão ${count + 1}</h4>
                <table class="form-table">
                    <tr>
                        <th>Imagem</th>
                        <td>
                            <input type="text" name="floating_balloon_settings[additional_balloons][${count}][image]" value="" />
                            <button type="button" class="button upload-image">Upload</button>
                        </td>
                    </tr>
                    <tr>
                        <th>Link</th>
                        <td>
                            <input type="text" name="floating_balloon_settings[additional_balloons][${count}][action]" value="" />
                        </td>
                    </tr>
                </table>
                <button type="button" class="button remove-balloon">Remover</button>
            </div>
        `;
        
        $('#additional-balloons').append(template);
    });

    // Upload de imagem
    $(document).on('click', '.upload-image', function(e) {
        e.preventDefault();
        var button = $(this);
        var customUploader = wp.media({
            title: 'Selecionar Imagem',
            button: {
                text: 'Usar esta imagem'
            },
            multiple: false
        });

        customUploader.on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            button.prev('input').val(attachment.url);
        });

        customUploader.open();
    });

    // Remover balão
    $(document).on('click', '.remove-balloon', function() {
        $(this).closest('.balloon-item').remove();
    });
});
