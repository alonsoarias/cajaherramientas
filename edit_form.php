<?php
class block_cajaherramientas_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Sección para el título y subtítulo
        $mform->addElement('header', 'configheader', get_string('pluginname', 'block_cajaherramientas'));

        $mform->addElement('text', 'config_title', get_string('title', 'block_cajaherramientas'));
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('text', 'config_subtitle', get_string('subtitle', 'block_cajaherramientas'));
        $mform->setType('config_subtitle', PARAM_TEXT);

        // Campos para cada curso
        for ($i = 1; $i <= 3; $i++) {
            $mform->addElement('header', "courseheader$i", "Curso $i");

            $mform->addElement('text', "config_coursename$i", get_string('coursename', 'block_cajaherramientas'));
            $mform->setType("config_coursename$i", PARAM_TEXT);

            $mform->addElement('text', "config_coursetype$i", get_string('coursetype', 'block_cajaherramientas'));
            $mform->setType("config_coursetype$i", PARAM_TEXT);

            $mform->addElement('text', "config_teachername$i", get_string('teachername', 'block_cajaherramientas'));
            $mform->setType("config_teachername$i", PARAM_TEXT);

            $mform->addElement('text', "config_courseurl$i", get_string('courseurl', 'block_cajaherramientas'));
            $mform->setType("config_courseurl$i", PARAM_URL);
        }

        // Selector de archivo para la imagen de fondo
        $mform->addElement('filemanager', 'config_backgroundimage', get_string('backgroundimage', 'block_cajaherramientas'), null, array(
            'maxfiles' => 1,
            'accepted_types' => array('image')
        ));

        // URL del portafolio
        $mform->addElement('text', 'config_portfolio_url', get_string('portfolio_url', 'block_cajaherramientas'));
        $mform->setType('config_portfolio_url', PARAM_URL);
    }

    public function set_data($defaults) {
        if (isset($this->block->config->backgroundimage)) {
            $draftitemid = file_get_submitted_draft_itemid('config_backgroundimage');
            file_prepare_draft_area(
                $draftitemid,
                $this->block->context->id,
                'block_cajaherramientas',
                'backgroundimage',
                0,
                array('subdirs' => false)
            );
            $defaults->config_backgroundimage = $draftitemid;
        }
        parent::set_data($defaults);
    }
}
