<?php
defined('MOODLE_INTERNAL') || die();

class block_cajaherramientas extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_cajaherramientas');
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = $this->generate_content();
        $this->content->footer = '';

        return $this->content;
    }

    private function generate_content() {
        global $OUTPUT;

        $config = $this->config;
        $output = '';

        // Título y subtítulo del bloque.
        $title = isset($config->title) ? format_string($config->title) : 'Caja de Herramientas';
        $subtitle = isset($config->subtitle) ? format_string($config->subtitle) : 'Mejora tus habilidades con los mejores cursos online';

        // Comienza el contenedor principal.
        $output .= html_writer::start_div('caja-herramientas');

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
            $output = str_replace('class="caja-herramientas"', 'class="caja-herramientas" style="' . $bgstyle . '"', $output);
        }

        // Encabezado del bloque.
        $output .= html_writer::start_div('caja-header');
        $output .= html_writer::tag('h2', $title, array('class' => 'caja-title'));
        $output .= html_writer::tag('p', $subtitle, array('class' => 'caja-subtitle'));
        $output .= html_writer::end_div(); // Fin de caja-header.

        // Contenedor de tarjetas.
        $output .= html_writer::start_div('cards-container');

        // Cursos destacados.
        $coursecount = isset($config->coursecount) ? $config->coursecount : 3;
        for ($i = 1; $i <= $coursecount; $i++) {
            $coursetitle = isset($config->{"coursename$i"}) ? format_string($config->{"coursename$i"}) : "Curso $i";
            $coursetype = isset($config->{"coursetype$i"}) ? format_string($config->{"coursetype$i"}) : "Tipo de curso";
            $coursetext1 = isset($config->{"coursetext1_$i"}) ? format_string($config->{"coursetext1_$i"}) : "Electiva/Seminario/Cátedra/Práctica";
            $coursetext2 = isset($config->{"coursetext2_$i"}) ? format_string($config->{"coursetext2_$i"}) : "Docente: Nombre N. Apellido A";
            $courseurl = isset($config->{"courseurl$i"}) ? $config->{"courseurl$i"} : '#';

            // Comienza la tarjeta.
            $output .= html_writer::start_div('card');

            // Encabezado de la tarjeta.
            $output .= html_writer::start_div('card-header');
            $output .= html_writer::div($coursetype, 'cocoon-card-header');
            $output .= html_writer::end_div(); // Fin de card-header.

            // Cuerpo de la tarjeta.
            $output .= html_writer::start_div('card-body');
            $output .= html_writer::tag('h3', $coursetitle, array('class' => 'card-title'));
            $output .= html_writer::tag('p', $coursetext1, array('class' => 'card-text'));
            $output .= html_writer::tag('p', $coursetext2, array('class' => 'card-text'));
            $output .= html_writer::end_div(); // Fin de card-body.

            // Botón "Ingresar".
            $output .= html_writer::link($courseurl, 'Ingresar', array('class' => 'card-button'));

            $output .= html_writer::end_div(); // Fin de card.
        }

        $output .= html_writer::end_div(); // Fin de cards-container.

        // Botón "Ver el portafolio".
        $portfolio_url = isset($config->portfolio_url) ? $config->portfolio_url : '#';
        $output .= html_writer::link($portfolio_url, 'Ver el portafolio', array('class' => 'portfolio-button'));

        $output .= html_writer::end_div(); // Fin de caja-herramientas.

        return $output;
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return false;
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    public function instance_config_save($data, $nolongerused = false) {
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
                array('subdirs' => false)
            );
            $data->backgroundimage = '';
        }

        parent::instance_config_save($data, $nolongerused);
    }
}
