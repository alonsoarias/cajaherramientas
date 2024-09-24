<?php
defined('MOODLE_INTERNAL') || die();

class block_cajaherramientas_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG;

        // Sección para el título y subtítulo.
        $mform->addElement('header', 'config_header', get_string('config_header', 'block_cajaherramientas'));

        // Campo para el título.
        $mform->addElement('text', 'config_title', get_string('title', 'block_cajaherramientas'));
        $mform->setDefault('config_title', get_string('defaulttitle', 'block_cajaherramientas'));
        $mform->setType('config_title', PARAM_TEXT);

        // Campo para el subtítulo.
        $mform->addElement('text', 'config_subtitle', get_string('subtitle', 'block_cajaherramientas'));
        $mform->setDefault('config_subtitle', get_string('defaultsubtitle', 'block_cajaherramientas'));
        $mform->setType('config_subtitle', PARAM_TEXT);

        // Selector de archivo para la imagen de fondo.
        $mform->addElement('filemanager', 'config_backgroundimage', get_string('backgroundimage', 'block_cajaherramientas'), null, array(
            'subdirs' => 0,
            'maxbytes' => $CFG->maxbytes,
            'areamaxbytes' => 10485760,  // 10MB
            'maxfiles' => 1,
            'accepted_types' => array('web_image')
        ));

        // Control de la cantidad de cursos.
        $coursecount_options = array(
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
        );
        $mform->addElement('select', 'config_coursecount', get_string('coursecount', 'block_cajaherramientas'), $coursecount_options);
        $mform->setDefault('config_coursecount', 1);
        $mform->setType('config_coursecount', PARAM_INT);

        // URL del portafolio.
        $mform->addElement('text', 'config_portfolio_url', get_string('portfolio_url', 'block_cajaherramientas'));
        $mform->setType('config_portfolio_url', PARAM_URL);

        // Campos para cada curso.
        for ($i = 1; $i <= 6; $i++) {
            $mform->addElement('header', "courseheader$i", get_string('course', 'block_cajaherramientas') . " $i");

            // Título del curso.
            $mform->addElement('text', "config_coursename$i", get_string('coursename', 'block_cajaherramientas'));
            $mform->setDefault("config_coursename$i", get_string('defaultcoursename', 'block_cajaherramientas', $i));
            $mform->setType("config_coursename$i", PARAM_TEXT);

            // Tipo de curso.
            $mform->addElement('text', "config_coursetype$i", get_string('coursetype', 'block_cajaherramientas'));
            $mform->setDefault("config_coursetype$i", get_string('defaultcoursetype', 'block_cajaherramientas'));
            $mform->setType("config_coursetype$i", PARAM_TEXT);

            // Texto adicional 1.
            $mform->addElement('text', "config_coursetext1_$i", get_string('coursetext1', 'block_cajaherramientas'));
            $mform->setDefault("config_coursetext1_$i", get_string('defaultcoursetext1', 'block_cajaherramientas'));
            $mform->setType("config_coursetext1_$i", PARAM_TEXT);

            // Texto adicional 2.
            $mform->addElement('text', "config_coursetext2_$i", get_string('coursetext2', 'block_cajaherramientas'));
            $mform->setDefault("config_coursetext2_$i", get_string('defaultcoursetext2', 'block_cajaherramientas'));
            $mform->setType("config_coursetext2_$i", PARAM_TEXT);

            // URL del curso.
            $mform->addElement('text', "config_courseurl$i", get_string('courseurl', 'block_cajaherramientas'));
            $mform->setType("config_courseurl$i", PARAM_URL);
        }
    }

    function set_data($defaults) {
        if (!empty($this->block->config) && is_object($this->block->config)) {
            // Preparar el área de archivo para la imagen de fondo
            $draftitemid = file_get_submitted_draft_itemid('config_backgroundimage');
            file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_cajaherramientas', 'backgroundimage', 0,
                array('subdirs' => 0, 'maxbytes' => 10485760, 'maxfiles' => 1));
            $defaults->config_backgroundimage = $draftitemid;
        }

        parent::set_data($defaults);
    }
}