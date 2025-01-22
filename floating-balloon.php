<?php
/*
Plugin Name: Balão Flutuante
Plugin URI: 
Description: Um plugin que adiciona balões flutuantes configuráveis no canto inferior direito do site.
Version: 2.4
Author: Bruno Macedo
Author URI: 
Author Email: salmacedo12@gmail.com
Contact: +55 (22) 99804-9118
License: GPL2
Text Domain: floating-balloon
Domain Path: /languages
*/

// Se este arquivo for chamado diretamente, aborta
if (!defined('WPINC')) {
    die;
}

// Define constantes do plugin
define('FLOATING_BALLOON_VERSION', '1.0.0');
define('FLOATING_BALLOON_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FLOATING_BALLOON_PLUGIN_URL', plugin_dir_url(__FILE__));

// Função de ativação do plugin
function floating_balloon_activate() {
    // Adiciona opções padrão se não existirem
    if (!get_option('floating_balloon_settings')) {
        add_option('floating_balloon_settings', array());
    }
}
register_activation_hook(__FILE__, 'floating_balloon_activate');

// Função de desativação do plugin
function floating_balloon_deactivate() {
    // Limpa qualquer cache ou dados temporários se necessário
}
register_deactivation_hook(__FILE__, 'floating_balloon_deactivate');

// Carrega os arquivos necessários
require_once FLOATING_BALLOON_PLUGIN_DIR . 'floating-balloon.php';

// Adicionar scripts e estilos no admin
function floating_balloon_enqueue_scripts($hook) {
    // Só carrega na página do plugin
    if ('toplevel_page_floating-balloon' !== $hook) {
        return;
    }

    // Carrega os scripts necessários
    wp_enqueue_media();
    wp_enqueue_script('jquery');
    
    // Registra e carrega o CSS admin
    wp_enqueue_style(
        'floating-balloon-admin-style',
        plugins_url('css/admin-style.css', __FILE__),
        array(),
        '1.0.0'
    );
    
    // Registra e carrega o JS admin
    wp_enqueue_script(
        'floating-balloon-admin-script',
        plugins_url('js/admin-script.js', __FILE__),
        array('jquery'),
        '1.0.0',
        true
    );

    // Adiciona as variáveis necessárias para o script
    wp_localize_script('floating-balloon-admin-script', 'floatingBalloonAdmin', array(
        'mediaTitle' => __('Selecionar ou Enviar Imagem', 'floating-balloon'),
        'mediaBtnText' => __('Usar esta imagem', 'floating-balloon')
    ));
}
add_action('admin_enqueue_scripts', 'floating_balloon_enqueue_scripts');

// Adicionar menu no admin
function floating_balloon_admin_menu() {
    add_menu_page(
        'Configurações do Balão Flutuante',
        'Balão Flutuante',
        'manage_options',
        'floating-balloon',
        'floating_balloon_admin_page'
    );
}
add_action('admin_menu', 'floating_balloon_admin_menu');

// Página de admin
function floating_balloon_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['submit'])) {
        update_option('floating_balloon_settings', $_POST['floating_balloon_settings']);
        echo '<div class="updated"><p>Configurações salvas!</p></div>';
    }

    $options = get_option('floating_balloon_settings', array());
    ?>
    <div class="wrap floating-balloon-admin">
        <h1>Configurações do Balão Flutuante</h1>
        <form method="post" action="">
            <h2>Balão Principal</h2>
            <table class="form-table">
                <tr>
                    <th>Imagem do Balão (Fechado)</th>
                    <td>
                        <input type="text" class="image-url-input" name="floating_balloon_settings[main_image]" value="<?php echo esc_attr($options['main_image'] ?? ''); ?>" />
                        <button type="button" class="button upload-image-button">Upload</button>
                    </td>
                </tr>
                <tr>
                    <th>Imagem do Balão (Aberto)</th>
                    <td>
                        <input type="text" class="image-url-input" name="floating_balloon_settings[main_image_active]" value="<?php echo esc_attr($options['main_image_active'] ?? ''); ?>" />
                        <button type="button" class="button upload-image-button">Upload</button>
                    </td>
                </tr>
                <tr>
                    <th>Link</th>
                    <td>
                        <input type="text" name="floating_balloon_settings[main_link]" value="<?php echo esc_attr($options['main_link'] ?? ''); ?>" />
                    </td>
                </tr>
                <tr>
                    <th>Cor de Fundo</th>
                    <td>
                        <input type="color" name="floating_balloon_settings[main_background_color]" value="<?php echo esc_attr($options['main_background_color'] ?? '#ffffff'); ?>" />
                    </td>
                </tr>
                <tr>
                    <th>Tooltip</th>
                    <td>
                        <label>
                            <input type="checkbox" name="floating_balloon_settings[show_tooltip]" value="1" <?php checked(isset($options['show_tooltip']) && $options['show_tooltip']); ?> />
                            Ativar tooltip
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>Texto do Tooltip</th>
                    <td>
                        <input type="text" name="floating_balloon_settings[tooltip_text]" value="<?php echo esc_attr($options['tooltip_text'] ?? ''); ?>" />
                    </td>
                </tr>
                <tr>
                    <th>Delay para Aparecer (ms)</th>
                    <td>
                        <input type="number" name="floating_balloon_settings[tooltip_delay_show]" value="<?php echo esc_attr($options['tooltip_delay_show'] ?? '1000'); ?>" min="0" step="100" />
                    </td>
                </tr>
                <tr>
                    <th>Delay para Sumir (ms)</th>
                    <td>
                        <input type="number" name="floating_balloon_settings[tooltip_delay_hide]" value="<?php echo esc_attr($options['tooltip_delay_hide'] ?? '500'); ?>" min="0" step="100" />
                    </td>
                </tr>
                <tr>
                    <th>Tamanho do Balão (px)</th>
                    <td>
                        <input type="number" 
                               name="floating_balloon_settings[main_balloon_size]" 
                               value="<?php echo esc_attr($options['main_balloon_size'] ?? '60'); ?>" 
                               min="40" 
                               max="200" 
                               step="1" /> px
                    </td>
                </tr>
                <tr>
                    <th>Tamanho da Imagem (%)</th>
                    <td>
                        <input type="number" 
                               name="floating_balloon_settings[main_image_size]" 
                               value="<?php echo esc_attr($options['main_image_size'] ?? '70'); ?>" 
                               min="10" 
                               max="100" 
                               step="5" /> %
                        <p class="description">Porcentagem em relação ao tamanho do balão</p>
                    </td>
                </tr>
            </table>

            <h2>Configurações Globais dos Balões Adicionais</h2>
            <table class="form-table">
                <tr>
                    <th>Tamanho dos Balões Adicionais (px)</th>
                    <td>
                        <input type="number" 
                               name="floating_balloon_settings[additional_balloon_size]" 
                               value="<?php echo esc_attr($options['additional_balloon_size'] ?? '50'); ?>" 
                               min="40" 
                               max="200" 
                               step="1" /> px
                        <p class="description">Define o tamanho padrão para todos os balões adicionais</p>
                    </td>
                </tr>
                <tr>
                    <th>Tamanho das Imagens (%)</th>
                    <td>
                        <input type="number" 
                               name="floating_balloon_settings[additional_image_size]" 
                               value="<?php echo esc_attr($options['additional_image_size'] ?? '70'); ?>" 
                               min="10" 
                               max="100" 
                               step="5" /> %
                        <p class="description">Porcentagem em relação ao tamanho dos balões adicionais</p>
                    </td>
                </tr>
            </table>

            <h2>Balões Adicionais</h2>
            <div id="additional-balloons">
                <?php
                if (!empty($options['additional_balloons'])) {
                    foreach ($options['additional_balloons'] as $index => $balloon) {
                        ?>
                        <div class="balloon-item">
                            <h4>Balão <?php echo $index + 1; ?></h4>
                            <table class="form-table">
                                <tr>
                                    <th>Imagem</th>
                                    <td>
                                        <input type="text" class="image-url-input" name="floating_balloon_settings[additional_balloons][<?php echo $index; ?>][image]" value="<?php echo esc_attr($balloon['image']); ?>" />
                                        <button type="button" class="button upload-image-button">Upload</button>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Cor de Fundo</th>
                                    <td>
                                        <input type="color" name="floating_balloon_settings[additional_balloons][<?php echo $index; ?>][background_color]" value="<?php echo esc_attr($balloon['background_color'] ?? '#ffffff'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tipo de Ação</th>
                                    <td>
                                        <select name="floating_balloon_settings[additional_balloons][<?php echo $index; ?>][action_type]" class="action-type-select">
                                            <option value="link" <?php selected($balloon['action_type'] ?? 'link', 'link'); ?>>Link</option>
                                            <option value="phone" <?php selected($balloon['action_type'] ?? 'link', 'phone'); ?>>Telefone</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="action-field-link <?php echo ($balloon['action_type'] ?? 'link') === 'phone' ? 'hidden' : ''; ?>">
                                    <th>Link</th>
                                    <td>
                                        <input type="text" name="floating_balloon_settings[additional_balloons][<?php echo $index; ?>][link]" value="<?php echo esc_attr($balloon['link'] ?? ''); ?>" />
                                    </td>
                                </tr>
                                <tr class="action-field-phone <?php echo ($balloon['action_type'] ?? 'link') === 'link' ? 'hidden' : ''; ?>">
                                    <th>Telefone</th>
                                    <td>
                                        <input type="text" class="phone-mask" name="floating_balloon_settings[additional_balloons][<?php echo $index; ?>][phone]" value="<?php echo esc_attr($balloon['phone'] ?? ''); ?>" placeholder="+55 (00) 0 0000-0000" />
                                    </td>
                                </tr>
                            </table>
                            <button type="button" class="button remove-balloon">Remover</button>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <p>
                <button type="button" class="button" id="add-balloon">Adicionar Novo Balão</button>
            </p>
            <?php submit_button('Salvar Alterações'); ?>
        </form>
    </div>
    <?php
}

// Adicionar scripts e estilos no frontend
function floating_balloon_scripts() {
    if (!wp_script_is('jquery', 'enqueued')) {
        wp_enqueue_script('jquery');
    }

    wp_enqueue_style(
        'floating-balloon', 
        FLOATING_BALLOON_PLUGIN_URL . 'css/style.css', 
        array(), 
        FLOATING_BALLOON_VERSION . '.' . time() // Força recarregamento do CSS
    );
    
    wp_enqueue_script(
        'floating-balloon', 
        FLOATING_BALLOON_PLUGIN_URL . 'js/script.js', 
        array('jquery'), 
        FLOATING_BALLOON_VERSION . '.' . time(), // Força recarregamento do JS
        true
    );

    // Debug
    error_log('Floating Balloon Debug: Scripts enqueued');
}
add_action('wp_enqueue_scripts', 'floating_balloon_scripts');

// Adicionar HTML dos balões no footer
function floating_balloon_html() {
    $options = get_option('floating_balloon_settings');
    
    // Debug
    error_log('Floating Balloon Debug: Options loaded');
    error_log('Floating Balloon Debug: ' . print_r($options, true));
    
    // Verificação mais detalhada das condições
    if (empty($options)) {
        error_log('Floating Balloon Debug: No options found');
        return;
    }

    // Verifica se tem pelo menos uma das configurações necessárias
    if (!isset($options['main_image']) && empty($options['additional_balloons'])) {
        error_log('Floating Balloon Debug: No balloons configured');
        return;
    }

    $main_background_color = esc_attr($options['main_background_color'] ?? '#ffffff');
    
    echo '<div id="floating-balloons" class="floating-balloons-container">';
    
    // Adicione uma classe para debug visual
    echo '<!-- Floating Balloon Plugin Active -->';
    
    // Seta para rolar para cima
    echo '<div class="scroll-to-top">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">';
    echo '<path d="M12 4l-8 8h6v8h4v-8h6z" fill="currentColor"/>';
    echo '</svg>';
    echo '</div>';
    
    // Balões adicionais (inicialmente ocultos)
    if (!empty($options['additional_balloons'])) {
        $additional_balloon_size = esc_attr($options['additional_balloon_size'] ?? '50');
        $additional_image_size = esc_attr($options['additional_image_size'] ?? '70');
        
        echo '<div class="additional-balloons-container">';
        foreach ($options['additional_balloons'] as $balloon) {
            $action_type = $balloon['action_type'] ?? 'link';
            $href = $action_type === 'phone' 
                ? 'tel:' . preg_replace('/[^0-9+]/', '', $balloon['phone'])
                : esc_url($balloon['link']);
            $balloon_background_color = esc_attr($balloon['background_color'] ?? '#ffffff');
                
            echo '<a href="' . $href . '" 
                     class="floating-balloon additional-balloon" 
                     style="background-color: ' . $balloon_background_color . ';
                            width: ' . $additional_balloon_size . 'px;
                            height: ' . $additional_balloon_size . 'px;">';
            echo '<img src="' . esc_url($balloon['image']) . '" 
                     alt="Balão Adicional" 
                     style="max-width: ' . $additional_image_size . '%; 
                            max-height: ' . $additional_image_size . '%;">';
            echo '</a>';
        }
        echo '</div>';
    }
    
    // Balão principal
    if (isset($options['main_image'])) {
        $balloon_size = esc_attr($options['main_balloon_size'] ?? '60');
        $image_size = esc_attr($options['main_image_size'] ?? '70');
        
        echo '<div class="main-balloon-wrapper">';
        echo '<a href="' . esc_url($options['main_link']) . '" 
                 class="floating-balloon main-balloon" 
                 style="background-color: ' . $main_background_color . '; 
                        width: ' . $balloon_size . 'px; 
                        height: ' . $balloon_size . 'px;">';
        echo '<img src="' . esc_url($options['main_image']) . '" 
                 alt="Balão Principal" 
                 class="balloon-image default" 
                 style="max-width: ' . $image_size . '%; 
                        max-height: ' . $image_size . '%;">';
        if (!empty($options['main_image_active'])) {
            echo '<img src="' . esc_url($options['main_image_active']) . '" 
                     alt="Balão Principal Ativo" 
                     class="balloon-image active" 
                     style="max-width: ' . $image_size . '%; 
                            max-height: ' . $image_size . '%;">';
        }
        echo '</a>';
        
        // Tooltip
        if (!empty($options['show_tooltip'])) {
            echo '<div class="balloon-tooltip" data-delay-show="' . esc_attr($options['tooltip_delay_show'] ?? '1000') . '" data-delay-hide="' . esc_attr($options['tooltip_delay_hide'] ?? '500') . '">';
            echo esc_html($options['tooltip_text'] ?? '');
            echo '</div>';
        }
        echo '</div>';
    }
    
    echo '</div>';
}
add_action('wp_footer', 'floating_balloon_html');

// Adicione esta função para verificar se o plugin está ativo e configurado
function floating_balloon_check_setup() {
    $options = get_option('floating_balloon_settings');
    
    if (empty($options)) {
        add_action('admin_notices', function() {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>O plugin Balão Flutuante está ativo mas não foi configurado. <a href="<?php echo admin_url('admin.php?page=floating-balloon'); ?>">Clique aqui para configurar</a>.</p>
            </div>
            <?php
        });
    }
}
add_action('admin_init', 'floating_balloon_check_setup');

// Adicione esta função para verificar conflitos de z-index
function floating_balloon_add_inline_css() {
    ?>
    <style type="text/css">
        /* Garante que o balão ficará sobre outros elementos */
        #floating-balloons {
            z-index: 999999 !important;
        }
        
        /* Força visibilidade */
        #floating-balloons.floating-balloons-container {
            display: flex !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'floating_balloon_add_inline_css');

// Adicione esta função para verificar o funcionamento dos cookies
function floating_balloon_check_cookies() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        try {
            localStorage.setItem('floating_balloon_test', 'test');
            localStorage.removeItem('floating_balloon_test');
        } catch (e) {
            console.log('Floating Balloon: Local Storage não disponível');
            // Adiciona classe para funcionamento sem localStorage
            $('#floating-balloons').addClass('no-storage');
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'floating_balloon_check_cookies', 9); // Prioridade 9 para rodar antes do HTML dos balões

// Modifique o JavaScript para funcionar sem localStorage
function floating_balloon_no_storage_support() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        if ($('#floating-balloons').hasClass('no-storage')) {
            // Força exibição do tooltip sem depender do localStorage
            var tooltip = $('.balloon-tooltip');
            if (tooltip.length) {
                setTimeout(function() {
                    tooltip.addClass('show');
                    setTimeout(function() {
                        tooltip.removeClass('show');
                    }, 5000);
                }, parseInt(tooltip.data('delay-show')) || 1000);
            }
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'floating_balloon_no_storage_support', 11); // Prioridade 11 para rodar depois do HTML dos balões

// Adicione uma mensagem de aviso no admin
function floating_balloon_admin_notices() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        try {
            localStorage.setItem('floating_balloon_test', 'test');
            localStorage.removeItem('floating_balloon_test');
        } catch (e) {
            $('div.wrap.floating-balloon-admin').prepend(
                '<div class="notice notice-warning is-dismissible">' +
                    '<p><strong>Atenção:</strong> Cookies ou Local Storage parecem estar bloqueados no seu navegador. ' +
                    'Isso pode afetar algumas funcionalidades do plugin Balão Flutuante no frontend do site. ' +
                    'Recomendamos testar o site em diferentes navegadores.</p>' +
                '</div>'
            );
        }
    });
    </script>
    <?php
}
add_action('admin_footer-toplevel_page_floating-balloon', 'floating_balloon_admin_notices');
