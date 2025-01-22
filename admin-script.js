jQuery(document).ready(function($) {
    // Manipulador para o botão de upload de imagem
    $(document).on('click', '.upload-image-button', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var imageInput = button.siblings('.image-url-input');
        
        // Cria o frame de mídia
        var frame = wp.media({
            title: floatingBalloonAdmin.mediaTitle,
            multiple: false,
            library: {
                type: 'image'
            },
            button: {
                text: floatingBalloonAdmin.mediaBtnText
            }
        });

        // Quando uma imagem for selecionada
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            imageInput.val(attachment.url);
        });

        // Abre o frame de mídia
        frame.open();
    });

    // Função para aplicar máscara de telefone
    function applyPhoneMask(input) {
        $(input).on('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 13) {
                value = value.replace(/^(\d{2})(\d{2})(\d{1})(\d{4})(\d{4}).*/, '+55 ($2) $3 $4-$5');
                e.target.value = value;
            }
        });
    }

    // Aplicar máscara nos campos existentes
    $('.phone-mask').each(function() {
        applyPhoneMask(this);
    });

    // Manipulador para alternar entre campos de link e telefone
    $(document).on('change', '.action-type-select', function() {
        const row = $(this).closest('.balloon-item');
        const linkField = row.find('.action-field-link');
        const phoneField = row.find('.action-field-phone');

        if (this.value === 'phone') {
            linkField.addClass('hidden');
            phoneField.removeClass('hidden');
        } else {
            linkField.removeClass('hidden');
            phoneField.addClass('hidden');
        }
    });

    // Atualizar o manipulador de adicionar novo balão
    $('#add-balloon').click(function() {
        var count = $('.balloon-item').length;
        var template = `
            <div class="balloon-item">
                <h4>Balão ${count + 1}</h4>
                <table class="form-table">
                    <tr>
                        <th>Imagem</th>
                        <td>
                            <input type="text" class="image-url-input" name="floating_balloon_settings[additional_balloons][${count}][image]" value="" />
                            <button type="button" class="button upload-image-button">Upload</button>
                        </td>
                    </tr>
                    <tr>
                        <th>Cor de Fundo</th>
                        <td>
                            <input type="color" name="floating_balloon_settings[additional_balloons][${count}][background_color]" value="#ffffff" />
                        </td>
                    </tr>
                    <tr>
                        <th>Tipo de Ação</th>
                        <td>
                            <select name="floating_balloon_settings[additional_balloons][${count}][action_type]" class="action-type-select">
                                <option value="link">Link</option>
                                <option value="phone">Telefone</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="action-field-link">
                        <th>Link</th>
                        <td>
                            <input type="text" name="floating_balloon_settings[additional_balloons][${count}][link]" value="" />
                        </td>
                    </tr>
                    <tr class="action-field-phone hidden">
                        <th>Telefone</th>
                        <td>
                            <input type="text" class="phone-mask" name="floating_balloon_settings[additional_balloons][${count}][phone]" value="" placeholder="+55 (00) 0 0000-0000" />
                        </td>
                    </tr>
                </table>
                <button type="button" class="button remove-balloon">Remover</button>
            </div>
        `;
        const $newBalloon = $(template);
        $('#additional-balloons').append($newBalloon);
        
        // Aplicar máscara no novo campo de telefone
        applyPhoneMask($newBalloon.find('.phone-mask'));
    });

    // Manipulador para remover balão
    $(document).on('click', '.remove-balloon', function() {
        $(this).closest('.balloon-item').remove();
        // Atualiza os números dos balões
        $('.balloon-item h4').each(function(index) {
            $(this).text('Balão ' + (index + 1));
        });
    });
}); 