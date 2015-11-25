<?php

class Ionic_User_Database_Manager {

    const LOG_TABLE_NAME = 'iup_push_log';

    const USER_ID_TABLE_NAME = 'iup_user_ids';
    CONST USER_ID_FIELD_USER_ID = 'userId';
    const USER_ID_FIELD_LAST_TOUCHED = 'lastTouched';
    const USER_ID_FIELD_CREATED = 'created';

    /**
     * @param string $userId
     * @return bool|int
     */
    public function delete_userId($userId) {
        global $wpdb;

        $sql = "
            DELETE FROM `{$wpdb->prefix}" . self::USER_ID_TABLE_NAME . "`
            WHERE `" . self::USER_ID_FIELD_USER_ID . "` = %s
        ";

        $sql = $wpdb->prepare($sql, $userId);
        return $wpdb->query($sql);
    }

    /**
     * @param string $userId
     * @return bool|int
     */
    public function store_userId($userId) {
        global $wpdb;

        $sql = "
            INSERT INTO `{$wpdb->prefix}" . self::USER_ID_TABLE_NAME . "`
            (`" . self::USER_ID_FIELD_USER_ID . "`, `" . self::USER_ID_FIELD_CREATED . "`, `" . self::USER_ID_FIELD_LAST_TOUCHED . "`)
            VALUES (%s,%s,%s)
            ON DUPLICATE KEY UPDATE `" . self::USER_ID_FIELD_LAST_TOUCHED . "` = %s
        ";

        $currentDate = date('Y-m-d H:i:s');
        $sql = $wpdb->prepare($sql, $userId, $currentDate, $currentDate, $currentDate, $currentDate);
        return $wpdb->query($sql);
    }
}