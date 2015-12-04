<?php

require_once __DIR__ . '/class-iup-history-manager.php';
require_once __DIR__ . '/class-iup-scheduled-manager.php';
require_once __DIR__ . '/class-iup-userId-manager.php';
require_once __DIR__ . '/class-iup-admin.php';

class Ionic_User_Send_Push {

    /**
     * @return WP_Error
     */
    public function send_scheduled_push_notification() {
        $scheduledManager = new Ionic_User_Scheduled_Manager();
        $result = $scheduledManager->get_passed_scheduled();

        foreach ($result as $row) {
            if ($row->userIds === 'all') {
                $userIds = Ionic_User_UserId_Manager::get_all_userIds();
            } else {
                $userIds = explode(';', $row->userIds);
            }

            return $this->send_push_notification(
                $row->text,
                $userIds,
                $this->load_options()
            );

            // TODO: Delete push
            // TODO: Doku um den Cron erweitern
        }
    }

    /**
     * @param string $text
     * @param array $userIds
     * @param array $options
     * @return WP_Error
     */
    public function send_push_notification($text, array $userIds, array $options) {
        if (empty($text)) {
            return new WP_Error( 'broke', __( "Missing text to send push notification!", "menu" ) );
        }

        if (count($userIds) === 0 || empty($userIds[0]) === true) {
            return new WP_Error( 'broke', __( "Missing users ids to send push notification!", "menu" ) );
        }

        if (empty($options['appId']) || empty($options['privateApiKey'])) {
            return new WP_Error( 'broke', __( "Please check basic settings and enter valid data!", "menu" ) );
        }

        $data = array(
            'user_ids' => $userIds,
            'notification' => array('alert' => $text),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://push.ionic.io/api/v1/push');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_USERPWD, $options['privateApiKey'] . ":" );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-Ionic-Application-Id: ' . $options['appId']
        ));
        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        $historyManager = new Ionic_User_History_Manager();
        $historyManager->store_history($text, count($userIds), $result);

        if ($result->result === 'error') {
            return new WP_Error( 'broke', __( $result->message, "menu" ) );
        }

        return $result;
    }


    /**
     * @return mixed
     */
    private function load_options() {
        $option_string = get_option(Ionic_User_Push_Admin::OPTION_NAME);

        if ($option_string === false) {
            return array();
        }

        return json_decode($option_string, true);
    }

}