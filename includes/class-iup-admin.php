<?php

class Ionic_User_Push_Admin {

    public function admin_menu() {
        add_options_page('Ionic User Push Notifications', 'Ionic User Push', 'manage_options', 'ionic-user-push', array('Ionic_User_Push_Admin', 'admin_menu_options'));
    }

    public function admin_menu_options () {
        $option_name = 'ionic_user_push';
        if (!current_user_can('manage_options')) {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        // Store / load data from / to options
        $storeData = self::store_option($option_name, $_POST);
        $options = self::load_options($option_name);

        if (empty(filter_input(INPUT_POST, 'send-push')) === false) {
            // Send push notification
            $return = Ionic_User_Send_Push::sendPushNotification(filter_input(INPUT_POST, 'send-text'), explode(';', filter_input(INPUT_POST, 'send-user-ids')), $options);
            if ( is_wp_error( $return ) ) {
                $error = $return->get_error_message();
            }
        }

        $tab = $_REQUEST['tab'] ? $_REQUEST['tab'] : 'settings';

        if ($tab === 'userIds') {
            $pagenum = filter_input(INPUT_GET, 'pagenum') ? absint(filter_input(INPUT_GET, 'pagenum')) : 1;
            $userIdsPages = Ionic_User_Database_Manager::get_userIds_page_links($pagenum);
        }

        $template = IUP_PLUGIN_DIR_PATH . 'assets/html/iup-admin-' . $tab . '.html';
        if (is_file($template) === true) {
            require $template;
        }
    }

    /**
     * @param string $option_name
     * @return mixed
     */
    private function load_options($option_name) {
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
    private function store_option($option_name, array $post) {
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