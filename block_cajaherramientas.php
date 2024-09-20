<?php
class block_cajaherramientas extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_cajaherramientas');
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $OUTPUT;

        $this->content = new stdClass();

        // Generar el contenido del bloque
        $this->content->text = $this->generate_content();
        $this->content->footer = '';

        return $this->content;
    }

    private function generate_content() {
        global $OUTPUT;

        // Obtener la configuración
        $config = $this->config;
        $output = '';

        // Título y subtítulo
        $title = isset($config->title) ? $config->title : '';
        $subtitle = isset($config->subtitle) ? $config->subtitle : '';

        $output .= html_writer::start_div('cajaherramientas-block');

        // Imagen de fondo
        if (isset($config->backgroundimage)) {
            $bgurl = moodle_url::make_pluginfile_url(
                $this->context->id,
                'block_cajaherramientas',
                'backgroundimage',
                0,
                '/',
                $config->backgroundimage
            );
            $style = "background-image: url('$bgurl');";
            $output .= html_writer::start_div('background', array('style' => $style));
        } else {
            $output .= html_writer::start_div('background');
        }

        // Superposición oscura
        $output .= html_writer::div('', 'overlay');

        // Contenido
        $output .= html_writer::start_div('content');

        $output .= html_writer::tag('h2', $title, array('class' => 'title'));
        $output .= html_writer::tag('p', $subtitle, array('class' => 'subtitle'));

        // Cursos destacados
        $output .= html_writer::start_div('courses');
        for ($i = 1; $i <= 3; $i++) {
            $coursename = isset($config->{"coursename$i"}) ? $config->{"coursename$i"} : '';
            $coursetype = isset($config->{"coursetype$i"}) ? $config->{"coursetype$i"} : '';
            $teachername = isset($config->{"teachername$i"}) ? $config->{"teachername$i"} : '';
            $courseurl = isset($config->{"courseurl$i"}) ? $config->{"courseurl$i"} : '#';

            $output .= html_writer::start_div('course-card');
            $output .= html_writer::tag('h3', $coursename, array('class' => 'course-name'));
            $output .= html_writer::tag('p', $coursetype, array('class' => 'course-type'));
            $output .= html_writer::tag('p', $teachername, array('class' => 'teacher-name'));
            $output .= html_writer::link($courseurl, get_string('enter', 'block_cajaherramientas'), array('class' => 'enter-button'));
            $output .= html_writer::end_div(); // course-card
        }
        $output .= html_writer::end_div(); // courses

        // Botón "Ver el portafolio"
        $portfolio_url = isset($config->portfolio_url) ? $config->portfolio_url : '#';
        $output .= html_writer::link($portfolio_url, get_string('viewportfolio', 'block_cajaherramientas'), array('class' => 'portfolio-button'));

        $output .= html_writer::end_div(); // content
        $output .= html_writer::end_div(); // background
        $output .= html_writer::end_div(); // cajaherramientas-block

        return $output;
    }

    public function instance_config_save($data, $nolongerused = false) {
        $context = $this->context;

        // Manejar la imagen de fondo
        if (!empty($data->backgroundimage)) {
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

    public function applicable_formats() {
        return array('all' => true);
    }

    public function instance_allow_multiple() {
        return false;
    }
}
