<?php
defined('MOODLE_INTERNAL') || die();

class block_cajaherramientas_edit_form extends block_edit_form
{
    protected function specific_definition($mform)
    {
        // Sección para el título y subtítulo.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Campo para el título.
        $mform->addElement('text', 'config_title', get_string('title', 'block_cajaherramientas'));
        $mform->setDefault('config_title', 'Caja de Herramientas');
        $mform->setType('config_title', PARAM_TEXT);

        // Campo para el subtítulo.
        $mform->addElement('text', 'config_subtitle', get_string('subtitle', 'block_cajaherramientas'));
        $mform->setDefault('config_subtitle', 'Mejora tus habilidades con los mejores cursos online');
        $mform->setType('config_subtitle', PARAM_TEXT);

        // Selector de archivo para la imagen de fondo.
        $mform->addElement('filemanager', 'config_backgroundimage', get_string('backgroundimage', 'block_cajaherramientas'), null, array(
            'subdirs' => 0,
            'maxbytes' => 1048576,
            'maxfiles' => 1,
            'accepted_types' => 'web_image'
        ));
        $mform->setType('config_backgroundimage', PARAM_INT);

        // Control de la cantidad de cursos.
        $coursecount_options = array(
            1 => '1',
            2 => '2',
            3 => '3'
        );
        $mform->addElement('select', 'config_coursecount', get_string('coursecount', 'block_cajaherramientas'), $coursecount_options);
        $mform->setDefault('config_coursecount', 1);
        $mform->setType('config_coursecount', PARAM_INT);

        // URL del portafolio.
        $mform->addElement('text', 'config_portfolio_url', get_string('portfolio_url', 'block_cajaherramientas'));
        $mform->setType('config_portfolio_url', PARAM_URL);

        // Campos para cada curso.
        for ($i = 1; $i <= 3; $i++) {
            $mform->addElement('header', "courseheader$i", get_string('course', 'block_cajaherramientas') . " $i");

            // Título del curso.
            $mform->addElement('text', "config_coursename$i", get_string('coursename', 'block_cajaherramientas'));
            $mform->setType("config_coursename$i", PARAM_TEXT);

            // Tipo de curso.
            $mform->addElement('text', "config_coursetype$i", get_string('coursetype', 'block_cajaherramientas'));
            $mform->setType("config_coursetype$i", PARAM_TEXT);

            // Texto adicional 1.
            $mform->addElement('text', "config_coursetext1_$i", get_string('coursetext1', 'block_cajaherramientas'));
            $mform->setType("config_coursetext1_$i", PARAM_TEXT);

            // Texto adicional 2.
            $mform->addElement('text', "config_coursetext2_$i", get_string('coursetext2', 'block_cajaherramientas'));
            $mform->setType("config_coursetext2_$i", PARAM_TEXT);

            // URL del curso.
            $mform->addElement('text', "config_courseurl$i", get_string('courseurl', 'block_cajaherramientas'));
            $mform->setType("config_courseurl$i", PARAM_URL);
        }
    }

    public function set_data($defaults)
    {
        // Manejar la imagen de fondo.
        if (isset($this->block->config->backgroundimage)) {
            $draftitemid = file_get_submitted_draft_itemid('config_backgroundimage');
            file_prepare_draft_area(
                $draftitemid,
                $this->block->context->id,
                'block_cajaherramientas',
                'backgroundimage',
                0,
                array('subdirs' => 0, 'maxbytes' => 1048576, 'maxfiles' => 1, 'accepted_types' => 'web_image')
            );
            $defaults->config_backgroundimage = $draftitemid;
        }

        parent::set_data($defaults);
    }
}
