<?php

require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once __DIR__ . '/class-iup-database-manager.php';

class Ionic_User_Push {

    public function plugin_activation() {
        flush_rewrite_rules();

        self::create_user_id_table();
        self::create_push_log_table();
    }

    /**
     * Store ionic user id to database and echo json result
     */
    public function process_parameter() {
        $params = $_REQUEST;

        if (empty($params['ionic-user-id']) === false) {

            if (empty($params['action']) === true) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Missing action'
                ));
                exit;
            }

            $userIdManager = new Ionic_User_Database_Manager();

            switch ($params['action']) {
                case 'store':
                    $result = $userIdManager->store_userId($params['ionic-user-id']);
                    self::echo_result($result, $params['action']);
                    exit;
                case 'delete':
                    $result = $userIdManager->delete_userId($params['ionic-user-id']);
                    self::echo_result($result, $params['action']);
                    exit;
                default:
                    echo json_encode(array(
                        'success' => false,
                        'message' => 'Not allowed action'
                    ));
                    exit;
            }
        }
    }

    /**
     * @param bool $result
     * @param string $action
     */
    private function echo_result($result, $action) {
        if ($result === false) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Error while do ' . $action
            ));
        } else {
            echo json_encode(array('success' => true));
        }
    }

    private function create_push_log_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . Ionic_User_Database_Manager::LOG_TABLE_NAME;

        $sql = "CREATE TABLE $table_name (
          `text` text NOT NULL,
          `userIds` text DEFAULT NULL,
          `status` varchar(255) DEFAULT NULL,
          `send` datetime DEFAULT NULL
        );";

        dbDelta( $sql );
    }

    private function create_user_id_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . Ionic_User_Database_Manager::USER_ID_TABLE_NAME;

        $sql = "CREATE TABLE $table_name (
          `" . Ionic_User_Database_Manager::USER_ID_FIELD_USER_ID . "` varchar(50) NOT NULL,
          `" . Ionic_User_Database_Manager::USER_ID_FIELD_CREATED . "` datetime DEFAULT NULL,
          `" . Ionic_User_Database_Manager::USER_ID_FIELD_LAST_TOUCHED . "` datetime DEFAULT NULL,
          PRIMARY KEY  (`" . Ionic_User_Database_Manager::USER_ID_FIELD_USER_ID . "`)
        );";

        dbDelta( $sql );
    }
}