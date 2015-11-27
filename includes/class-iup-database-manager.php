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

    /**
     * @param int $pagenum
     * @param int $limit
     * @return array
     */
    public function get_userIds_page_links($pagenum, $limit = 25) {
        global $wpdb;

        $offset = ( $pagenum - 1 ) * $limit;
        $total = $wpdb->get_var( "SELECT COUNT(`" . self::USER_ID_FIELD_USER_ID . "`) FROM `{$wpdb->prefix}" . self::USER_ID_TABLE_NAME . "`" );
        $num_of_pages = ceil( $total / $limit );
        $results = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}" . self::USER_ID_TABLE_NAME . "` LIMIT $offset, $limit" );

        $paginate_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( '&laquo;', 'text-domain' ),
            'next_text' => __( '&raquo;', 'text-domain' ),
            'total' => $num_of_pages,
            'current' => $pagenum
        ) );

        return array(
            'paginate_links' => $paginate_links,
            'results' => $results
        );
    }
}