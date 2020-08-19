<?php
/**
 * Plugin Name:       Almost useless plugin for Talkin tech
 * Plugin URI:        https://github.com/Darciro/almost-useless-plugin-for-talkin-tech
 * Description:       Plugin de teste desenvolvido durante a Talkin tech de Agosto
 * Version:           1.0.0
 * Author:            Ricardo Carvalho
 * Author URI:        https://github.com/darciro/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Se este arquivo for chamado diretamente, abortar!
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('AUPTT')) :

    class AUPTT
    {

        public function __construct()
        {
            // Adicionando ações para eventos específicos do WP
            add_action('add_meta_boxes', array($this, 'add_subtitle_meta_box'));
            add_action('save_post', array($this, 'save_meta_boxes'));
            add_action('wp_enqueue_scripts', array($this, 'auptt_style'));

            // Filtrando o conteúdo antes de que ele seja renderizado
            add_filter('the_title', array($this, 'show_post_subtitle'), 10, 2);
            add_filter('the_content', array($this, 'add_talkin_tech_link'), 10, 2);
        }

        /**
         * Este método é responsável por criar o Meta box
         *
         * @link https://developer.wordpress.org/reference/functions/add_meta_box/
         */
        public static function add_subtitle_meta_box()
        {
            // Definimos aqui em quais os tipos de conteúdo desejamos adicionar nosso meta-box
            $screens = array('post');

            foreach ($screens as $screen) {
                add_meta_box(
                    'auptt_box_id',                       // ID do meta-box, deve ser único
                    'Almost useless fields',            // Título do meta-box visível na área administrativa
                    array(self::class, 'subtitle_meta_box'), // Função Callback, responsável por renderizar o HTML do meta-box
                    $screen,                                 // O tipo de post ou qual esse meta-box deve ser visível
                    'advanced',                       // Local onde o box deve ser visível
                    'high'                            // Prioridade dentro do contexto
                );
            }
        }

        /**
         * Método responsável por renderizar o HTML do nosso meta-box
         *
         * @param $post
         * @link https://developer.wordpress.org/reference/functions/get_post_meta/
         */
        public static function subtitle_meta_box($post)
        {
            // Buscamos a nossa meta_key, com a informação que foi salva no banco de dados
            $post_subtitle = get_post_meta($post->ID, '_post_subtitle', true); ?>

            <label for="post_subtitle">Subtítulo</label>
            <input type="text" name="post_subtitle" id="post_subtitle" class="regular-text" value="<?php echo $post_subtitle; ?>">

            <?php
        }

        /**
         * Método responsável por realizar o processamento dos ao salvar o post
         *
         * @param $post_id
         * @link https://developer.wordpress.org/reference/functions/update_post_meta/
         */
        public static function save_meta_boxes($post_id)
        {
            if (array_key_exists('post_subtitle', $_POST)) {
                update_post_meta(
                    $post_id,
                    '_post_subtitle',
                    $_POST['post_subtitle']
                );
            }
        }

        /**
         * Este é filtro responsável pela renderização do nosso subtítulo, salvo pelo meta-box
         *
         * @param $title
         * @param null $id
         * @return string
         */
        public function show_post_subtitle($title, $id = null)
        {
            // Evitamos filtrar o conteúdo que não seja o executado numa página interna
            if(!is_single())
                return $title;

            // Buscamos a nossa meta_key, com a informação que foi salva no banco de dados
            $post_subtitle = get_post_meta($id, '_post_subtitle', true);
            if (!empty($post_subtitle)) {
                // Adicionamos junto ao título, o nosso subtítulo, encapsulado por uma tag HTML
                $post_subtitle = '<h2>' . $post_subtitle . '</h2>';
                return $title . $post_subtitle;
            }

            return $title;
        }

        /**
         * Este é filtro responsável por adicionar um texto ao final do nosso post
         *
         * @param $content
         * @param null $id
         * @return string
         */
        public function add_talkin_tech_link($content, $id = null)
        {
            // Fazemos uma verificação, pois só queremos adicionar nosso texto em conteúdos do tipo 'post'
            if ( !in_array(get_post()->post_type, array('post') ) )
                return $content;

            $talkin_tech_link = '<p class="auptt-box"><a href="https://www.facebook.com/integerconsulting/">Talkin Tech by Integer Consulting</a></p>';
            return $content . $talkin_tech_link;
        }

        /**
         * Ação responsável por adicionar nossa folha de estilo, à fila de arquivos CSS carregados
         *
         * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
         */
        public function auptt_style()
        {
            wp_enqueue_style('auptt-style', plugin_dir_url(__FILE__) . 'assets/style.css');
        }

    }

    // Initialize our plugin
    $auptt = new AUPTT();

endif;