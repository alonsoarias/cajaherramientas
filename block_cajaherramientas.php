<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/renderer.php');
require_once($CFG->dirroot . '/theme/edumy/ccn/course_handler/ccn_course_handler.php');
require_once($CFG->dirroot . '/theme/edumy/ccn/block_handler/ccn_block_handler.php');

class block_cajaherramientas extends block_base
{
    public function init()
    {
        $this->title = get_string('pluginname', 'block_cajaherramientas');
    }

    public function get_content()
    {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = $this->generate_content();
        $this->content->footer = '';

        return $this->content;
    }

    private function generate_content()
    {
        global $OUTPUT;

        $config = $this->config;
        $output = '';

        // Título y subtítulo del bloque.
        $title = isset($config->title) ? format_string($config->title) : 'Caja de Herramientas';
        $subtitle = isset($config->subtitle) ? format_string($config->subtitle) : 'Mejora tus habilidades con los mejores cursos online';

        // Comienza el contenedor principal.
        $output .= html_writer::start_div('block_cajaherramientas_container');

        // Imagen de fondo.
        $bgstyle = '';
        if (!empty($this->config)) {
            $fs = get_file_storage();
            $files = $fs->get_area_files($this->context->id, 'block_cajaherramientas', 'backgroundimage', 0, 'itemid, filepath, filename', false);
            if ($files) {
                $file = reset($files);
                $bgurl = moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename()
                );
                $bgstyle = "background-image: url('{$bgurl}'); background-size: cover; background-position: center; background-blend-mode: overlay;";
            }
        }

        // Aplicar estilo de fondo si hay una imagen.
        if ($bgstyle) {
            $output = str_replace('class="block_cajaherramientas_container"', 'class="block_cajaherramientas_container" style="' . $bgstyle . '"', $output);
        }

        // Encabezado del bloque.
        $output .= html_writer::start_div('block_cajaherramientas_header');
        $output .= html_writer::tag('h2', $title, array('class' => 'block_cajaherramientas_title'));
        $output .= html_writer::tag('p', $subtitle, array('class' => 'block_cajaherramientas_subtitle'));
        $output .= html_writer::end_div(); // Fin de block_cajaherramientas_header.

        // Contenedor de tarjetas.
        $output .= html_writer::start_div('block_cajaherramientas_cards_container');

        // Cursos destacados.
        $coursecount = isset($config->coursecount) ? $config->coursecount : 1;
        $coursecount = max(1, min(3, $coursecount)); // Asegura que el número de cursos esté entre 1 y 3
        for ($i = 1; $i <= $coursecount; $i++) {
            $coursetitle = isset($config->{"coursename$i"}) ? format_string($config->{"coursename$i"}) : "Curso $i";
            $coursetype = isset($config->{"coursetype$i"}) ? format_string($config->{"coursetype$i"}) : "Tipo de curso";
            $coursetext1 = isset($config->{"coursetext1_$i"}) ? format_string($config->{"coursetext1_$i"}) : "Electiva/Seminario/Cátedra/Práctica";
            $coursetext2 = isset($config->{"coursetext2_$i"}) ? format_string($config->{"coursetext2_$i"}) : "Docente: Nombre N. Apellido A";
            $courseurl = isset($config->{"courseurl$i"}) ? $config->{"courseurl$i"} : '#';

            // Comienza la tarjeta.
            $output .= html_writer::start_div('block_cajaherramientas_card');

            // Encabezado de la tarjeta.
            $output .= html_writer::start_div('block_cajaherramientas_card_header');
            $output .= html_writer::div($coursetype, 'block_cajaherramientas_card_header_text');
            $output .= html_writer::end_div(); // Fin de block_cajaherramientas_card_header.

            // Cuerpo de la tarjeta.
            $output .= html_writer::start_div('block_cajaherramientas_card_body');
            $output .= html_writer::tag('h3', $coursetitle, array('class' => 'block_cajaherramientas_card_title'));
            $output .= html_writer::tag('p', $coursetext1, array('class' => 'block_cajaherramientas_card_text'));
            $output .= html_writer::tag('p', $coursetext2, array('class' => 'block_cajaherramientas_card_text'));
            $output .= html_writer::end_div(); // Fin de block_cajaherramientas_card_body.

            // Botón "Ingresar".
            $output .= html_writer::link($courseurl, 'Ingresar', array('class' => 'block_cajaherramientas_card_button'));

            $output .= html_writer::end_div(); // Fin de block_cajaherramientas_card.
        }

        $output .= html_writer::end_div(); // Fin de block_cajaherramientas_cards_container.

        // Botón "Ver el portafolio".
        $portfolio_url = isset($config->portfolio_url) ? $config->portfolio_url : '#';
        $output .= html_writer::link($portfolio_url, 'Ver el portafolio', array('class' => 'block_cajaherramientas_portfolio_button'));

        $output .= html_writer::end_div(); // Fin de block_cajaherramientas_container.

        return $output;
    }

    function applicable_formats()
    {
        $ccnBlockHandler = new ccnBlockHandler();
        return $ccnBlockHandler->ccnGetBlockApplicability(array('all'));
    }

    public function instance_config_save($data, $nolongerused = false)
    {
        // Manejar la imagen de fondo.
        $context = $this->context;
        if (isset($data->backgroundimage)) {
            $draftitemid = $data->backgroundimage;
            file_save_draft_area_files(
                $draftitemid,
                $context->id,
                'block_cajaherramientas',
                'backgroundimage',
                0,
                array('subdirs' => 0, 'maxbytes' => 1048576, 'maxfiles' => 1, 'accepted_types' => 'web_image')
            );
            $data->backgroundimage = '';
        }

        parent::instance_config_save($data, $nolongerused);
    }

    public function html_attributes()
    {
        global $CFG;
        $attributes = parent::html_attributes();
        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/attributes.php');
        return $attributes;
    }

    public function instance_allow_multiple()
    {
        return false;
    }

    public function has_config()
    {
        return false;
    }

    public function cron()
    {
        return true;
    }
}
