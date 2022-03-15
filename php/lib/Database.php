<?php
class DB {
    public static function database() {
        global $config;
        $db = new SQLite3($config['database']['sqliteFile']);
        return $db;
    }
}
?>