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
        $title = isset($config->title) ? format_string($config->title) : get_string('defaulttitle', 'block_cajaherramientas');
        $subtitle = isset($config->subtitle) ? format_string($config->subtitle) : get_string('defaultsubtitle', 'block_cajaherramientas');

        // Comienza el contenedor principal.
        $output .= html_writer::start_div('block_cajaherramientas_container');

        // Imagen de fondo.
        $bgstyle = '';
        if (!empty($this->config->backgroundimage)) {
            $fs = get_file_storage();
            $files = $fs->get_area_files($this->context->id, 'block_cajaherramientas', 'backgroundimage', 0, 'sortorder,filepath,filename', false);
            if ($files) {
                $file = reset($files);
                $bgurl = moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    null,
                    $file->get_filepath(),
                    $file->get_filename()
                );
                $bgstyle = "background-image: url('{$bgurl}'); background-size: cover; background-position: center;";
            }
        }

        // Aplicar estilo de fondo si hay una imagen.
        if ($bgstyle) {
            $output .= html_writer::div('', 'block_cajaherramientas_background', ['style' => $bgstyle]);
        }

        // Superposición azul semitransparente
        $output .= html_writer::div('', 'block_cajaherramientas_overlay');

        // Contenido del bloque
        $output .= html_writer::start_div('block_cajaherramientas_content');

        // Encabezado del bloque.
        $output .= html_writer::start_div('block_cajaherramientas_header');
        $output .= html_writer::tag('h2', $title, array('class' => 'block_cajaherramientas_title'));
        $output .= html_writer::tag('p', $subtitle, array('class' => 'block_cajaherramientas_subtitle'));
        $output .= html_writer::end_div(); // Fin de block_cajaherramientas_header.

        // Contenedor de tarjetas.
        $output .= html_writer::start_div('block_cajaherramientas_cards_container');

        // Cursos destacados.
        $coursecount = isset($config->coursecount) ? $config->coursecount : 1;
        $coursecount = max(1, min(6, $coursecount)); // Asegura que el número de cursos esté entre 1 y 6
        for ($i = 1; $i <= $coursecount; $i++) {
            $coursetitle = isset($config->{"coursename$i"}) ? format_string($config->{"coursename$i"}) : get_string('defaultcoursename', 'block_cajaherramientas', $i);
            $coursetype = isset($config->{"coursetype$i"}) ? format_string($config->{"coursetype$i"}) : get_string('defaultcoursetype', 'block_cajaherramientas');
            $coursetext1 = isset($config->{"coursetext1_$i"}) ? format_string($config->{"coursetext1_$i"}) : get_string('defaultcoursetext1', 'block_cajaherramientas');
            $coursetext2 = isset($config->{"coursetext2_$i"}) ? format_string($config->{"coursetext2_$i"}) : get_string('defaultcoursetext2', 'block_cajaherramientas');
            $courseurl = isset($config->{"courseurl$i"}) ? $config->{"courseurl$i"} : '';

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

            // Botón "Ingresar" solo si hay una URL válida.
            if (!empty($courseurl) && $courseurl !== '#') {
                $output .= html_writer::link($courseurl, get_string('enter', 'block_cajaherramientas'), array('class' => 'block_cajaherramientas_card_button'));
            }

            $output .= html_writer::end_div(); // Fin de block_cajaherramientas_card.
        }

        $output .= html_writer::end_div(); // Fin de block_cajaherramientas_cards_container

        // Botón "Ver el portafolio" solo si hay una URL válida.
        $portfolio_url = isset($config->portfolio_url) ? $config->portfolio_url : '';
        if (!empty($portfolio_url) && $portfolio_url !== '#') {
            $output .= html_writer::link($portfolio_url, get_string('viewportfolio', 'block_cajaherramientas'), array('class' => 'block_cajaherramientas_portfolio_button'));
        }

        $output .= html_writer::end_div(); // Fin de block_cajaherramientas_content
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
        if (!empty($data->config_backgroundimage)) {
            $context = $this->context;
            $draftitemid = file_get_submitted_draft_itemid('config_backgroundimage');
            file_save_draft_area_files($draftitemid, $context->id, 'block_cajaherramientas', 'backgroundimage', 0);
            $data->backgroundimage = $draftitemid;
        }
        return parent::instance_config_save($data, $nolongerused);
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
