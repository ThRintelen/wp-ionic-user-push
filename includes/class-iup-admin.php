<?php

class Ionic_User_Push_Admin {

    public function admin_menu() {
        add_options_page('Ionic User Push Notifications', 'Ionic User Push', 'manage_options', 'iup', array('Ionic_User_Push_Admin', 'admin_menu_options'));
    }

    public function admin_menu_options () {
        $option_name = 'ionic_user_push';
        if (!current_user_can('manage_options')) {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        $storeData = Ionic_User_Push_Admin::storeOption($option_name, $_POST);
        $option = Ionic_User_Push_Admin::loadOptins($option_name);

        require IUP_PLUGIN_DIR_PATH . 'assets/html/iup-admin.html';
    }

    /**
     * @param string $option_name
     * @return mixed
     */
    private function loadOptins($option_name) {
        $option_string = get_option($option_name);

        if ($option_string === false) {
            return array();
        }

        return json_decode($option_string, true);
    }

    /**
     * @param string $option_name
     * @param array $post
     * @return bool
     */
    private function storeOption($option_name, array $post) {
        $option = array();
        $storeData = false;

        $options = array('appId', 'privateApiKey');

        foreach ($options as $name) {
            if (isset($post[$name])) {
                $storeData = true;
                $option[$name] = esc_html($post[$name]);
            }
        }

        if ($storeData === true) {
            update_option($option_name, json_encode($option));
        }

        return $storeData;
    }
}