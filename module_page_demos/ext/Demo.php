<?php
/**
 * @author Anastasia Sidak <m0st1ce.nastya@gmail.com>
 *
 * @link https://steamcommunity.com/profiles/76561198038416053
 * @link https://github.com/M0st1ce
 *
 * @license GNU General Public License Version 3
 */

class Demo {

    /**
     * @var string
     */
    public $demo_server = '';

    /**
     * @var string
     */
    public $demo_name = '';

    /**
     * @var string
     */
    public $server_name = '';

    /**
     * @var string
     */
    public $demo_score = '';

    /**
     * @var int
     */
    public $demo_score_ct = 0;

    /**
     * @var int
     */
    public $demo_score_t = 0;

    /**
     * @var int
     */
    public $demo_time = '';

    /**
     * @var array
     */
    public $demo_data = [];

    /**
     * @var int
     */
    public $server_id = 0;

    /**
     * @var int
     */
    public $match_id = 0;

    /**
     * @var array
     */
    public $match_player_kills = [];

    /**
     * @var array
     */
    public $match_player_deaths = [];

    /**
     * @var array
     */
    public $join_ids = [];

    /**
     * @var array
     */
    public $player_name = [];

    /**
     * @var array
     */
    public $log_final = [];

    /**
     * @var array
     */
    public $steam_id_kills = [];

    /**
     * @var array
     */
    public $steam_id_death = [];

    /**
     * @var array
     */
    public $player_team = [];

    function __construct() {

        // Кодировка.
        header('Content-Type: text/html; charset=utf-8');

        // Получение основных функций.
        require  '../../../../app/includes/functions.php';

        // Получение информации о демо.
        $this->get_data();

        // Проверка WEB API KEY.
        $this->check_web_key();

        // Проверка файлов демо.
        $this->check_demo_files();

        // Проверка папок связанных с кэшэм демо файлов.
        $this->check_demo_folders();

        // Получение информации о демо записи.
        $this->get_demo_data('../temp/stage/' . $this->demo_name . '.dem');

        // Парсинг лог файла.
        $this->parse_log_file('../temp/stage/' . $this->demo_name . '.log');

        // Подключение к базе данных.
        $this->db_connect();

        // Проверка существования таблиц.
        $this->db_check_table();

        // Получение id сервера.
        $this->get_server_id();

        // Получение id матча.
        $this->get_match_id();

        // Заполнение таблицы с матчами.
        $this->db_edit_matches_table();

        // Архивация демо файла.
        $this->archive_demo_file();

        // Удаление старых файлов.
        $this->delete_old_files();

        // Очистка кэша.
        $this->clear_cache();

        // Всё прошло успешно.
        echo "OK";

        // Закрытие соединения с БД.
        $this->mysqli->close();
    }

    public function get_data() {
        $check = [];
        $this->demo_name = $_GET['demo'] ?? $check[] = 0;
        if( ! empty ( $this->demo_name ) ):
            $type = "/auto-(.*)-([0-9]+)-([0-9]+)-(.*)/u";
            preg_match_all( $type, $this->demo_name, $arr, PREG_SET_ORDER);
            $this->server_name = $arr[0][1];
        endif;
        $this->demo_score = $_GET['score'] ?? $check[] = 0;
        $demo_score_temp = explode('-', $this->demo_score, 2);
        $this->demo_score_ct = $demo_score_temp[0];
        $this->demo_score_t = $demo_score_temp[1];
        $this->demo_time = $_GET['time'] ?? $check[] = 0;
        if( in_array( 0, $check ) ):
            $this->logs( 'Основная инициализация - Неудача' );
            die();
        else:
            $this->logs( 'Основная инициализация - Успешно' );
        endif;
    }

    public function check_web_key() {
        if( $_GET['key'] != ( require  '../../../../storage/cache/sessions/options.php' )['web_key'] ):
            $this->logs( 'Проверка ключа доступа - Неудача' );
            die();
        else:
            $this->logs( 'Проверка ключа доступа - Успешно' );
        endif;
    }

    public function logs( $text ) {
        file_put_contents('../temp/logs/log.log', date('Y-m-d' ) . ' - ' . date('H:i' ) . ' [ ' .$_SERVER['REMOTE_ADDR'] . ' ] ' . 'Демо запись - ' . $_GET['demo'] . ' - ' . $text . "\n" , FILE_APPEND);
    }

    public function check_demo_files() {
        if( ! file_exists( '../temp/stage/' . $this->demo_name . '.dem' ) || ! file_exists( '../temp/stage/' . $this->demo_name . '.log' ) ):
            $this->logs( 'Проверка на существования демо записи и лог файла - Неудача' );
            die();
        else:
            $this->logs( 'Проверка на существования демо записи и лог файла - Успешно' );
        endif;
    }

    public function check_demo_folders() {
        if( ! file_exists( '../temp/demos' ) ) mkdir( '../temp/demos', 0777, true );
        if( ! file_exists( '../temp/logs' ) ) mkdir( '../temp/logs', 0777, true );
    }

    public function get_demo_data( $demo ) {
        $all = [];
        $file = fopen( $demo, "rb" );
        $data = trim(fread( $file, 8 ));
        if( $data != "HL2DEMO" ) return;
        $data = unpack( "idp/inp", fread( $file, 8 ) );
        $all['hostname'] = trim(fread($file,260));
        $all['client'] = trim(fread($file,260));
        $all['map'] = trim(fread($file,260));
        $all['game'] = trim(fread($file,260));
        $all['duration'] = unpack( "f", fread($file,4))[1];
        $this->demo_data = $all;
        $this->logs( 'Чтение демо файла - Успешно' );
    }

    public function db_connect() {
        $db = (require '../../../../storage/cache/sessions/db.php')['Core'][0];
        $this->mysqli = new mysqli( $db['HOST'], $db['USER'], $db['PASS'], $db['DB'][0]['DB'], $db['PORT'] );
        $this->mysqli->set_charset('utf8');

        empty( $this->mysqli->connect_errno ) ? $this->logs( 'Подключение к базе данных - Успешно' ) : $this->logs( 'Подключение к базе данных - Неудача' );
    }

    public function db_check_table() {

        $this->mysqli->query('CREATE TABLE IF NOT EXISTS lvl_base_matches_scoretotal (
                            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                            `server_id` INT(11) NOT NULL,
                            `demoname` VARCHAR(128) NOT NULL,
                            `game` VARCHAR(16) NOT NULL,
                            `CT` int(32) NOT NULL,
                            `T` int(32) NOT NULL,
                            `map` VARCHAR(128) NOT NULL,
                            `time` int(64) NOT NULL,
                            `duration` decimal(10,0) NOT NULL) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;');

        $this->mysqli->query('CREATE TABLE IF NOT EXISTS lvl_base_matches (
                            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                            `match_id` INT(6) NOT NULL,
                            `name` VARCHAR(64) NOT NULL,
                            `steamid` VARCHAR(64) NOT NULL,
                            `team` VARCHAR(16) NOT NULL,
                            `kills` int(11) NOT NULL,
                            `deaths` int(11) NOT NULL) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;');

        $this->mysqli->query('CREATE TABLE IF NOT EXISTS lvl_web_servers (
                            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
							`ip` VARCHAR(64) NOT NULL,
                            `fakeip` VARCHAR(64) NOT NULL,
                            `name` VARCHAR(64) NOT NULL,
                            `name_custom` VARCHAR(128) NOT NULL,
                            `rcon` VARCHAR(64) NOT NULL,
                            `server_stats` VARCHAR(64) NOT NULL,
                            `server_vip` VARCHAR(64) NOT NULL,
                            `server_vip_id` INT(11) NOT NULL,
                            `server_sb` VARCHAR(64) NOT NULL,
                            `server_shop` VARCHAR(64) NOT NULL,
                            `server_warnsystem` VARCHAR(64) NOT NULL,
                            `server_lk` VARCHAR(64) NOT NULL) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;');

        $this->logs( 'Проверка существования таблиц - Успешно' );
    }

    public function get_server_id() {

        $res = $this->mysqli->query("SELECT `id`, `name` FROM `lvl_web_servers` WHERE `name` = '{$this->server_name}' LIMIT 1");
        $id = $res->fetch_assoc();

        if( empty( $id ) ):
            unset( $res );
	$this->mysqli->query("INSERT INTO `lvl_web_servers` (`name`) VALUES ('{$this->server_name}');");
            $res = $this->mysqli->query("SELECT `id`, `name` FROM `lvl_web_servers` WHERE `name` = '{$this->server_name}' LIMIT 1");
            $id = $res->fetch_assoc();
            $this->server_id = $id['id'];
        else:
            $this->server_id = $id['id'];
        endif;
	
	$a = action_text_clear_before_slash( utf8_decode( $this->demo_data["map"] ) );
	$game = $this->demo_data['game'];
	$duration = $this->demo_data['duration'];
	$this->mysqli->query("INSERT INTO `lvl_base_matches_scoretotal` ( `id`, `server_id`, `demoname`, `game`, `CT`, `T`, `map`, `time`, `duration`) VALUES ( NULL, '{$this->server_id}', '{$this->server_name}', '{$game}', '{$this->demo_score_ct}', '{$this->demo_score_t}','{$a}','{$this->demo_time}','{$duration}')");
    }

    public function get_match_id() {

        $res = $this->mysqli->query('SELECT `id` FROM `lvl_base_matches_scoretotal` order by `id` desc LIMIT 1');

        $this->match_id = (int) $res->fetch_assoc()['id'];
    }

    public function archive_demo_file() {
        $zip = new ZipArchive;
        $zip->open( '../temp/demos/'. $this->match_id .'.zip', ZipArchive::CREATE );
        $zip->addFile( '../temp/stage/' . $this->demo_name . '.dem', $this->demo_name . '.dem' );
        $zip->close();
    }

    public function db_edit_matches_table() {

        $steam_kills = array_count_values( $this->match_player_kills );
        $steam_death = array_count_values( $this->match_player_deaths );

        $this->steam_id_kills = array_keys( $steam_kills );
        $this->steam_id_death = array_keys( $steam_death );

        $players = array_unique( $this->steam_id_kills + $this->steam_id_death + $this->join_ids );

        $match_player_size = sizeof( $players );

        for ($i = 0; $i <= $match_player_size; $i++) {
			$p_n = $this->player_name[ $players[ $i ] ];
			$pl = $players[ $i ];
			$p_t = $this->player_team[ $players[ $i ] ];
			$s_k = $steam_kills [ $players[ $i ] ];
			$s_d = $steam_death [ $players[ $i ] ];
            $this->mysqli->query("INSERT INTO `lvl_base_matches` ( `id`, `match_id`, `name`, `steamid`, `team`, `kills`, `deaths` ) VALUES ( NULL, '{$this->match_id}', '{$p_n}', '{$pl}', '{$p_t}', '{$s_k}', '{$s_d}')");
        }

        file_put_contents( '../temp/logs/'.$this->match_id.'.json', json_encode( $this->log_final ) );
    }

    public function delete_old_files() {
        unlink( '../temp/stage/' . $this->demo_name . '.dem' );
        unlink( '../temp/stage/' . $this->demo_name . '.log' );
    }

    public function clear_cache() {

        # ПОЛУЧЕНИЕ НАСТРОЕК МОДУЛЯ

        $module_description = json_decode( file_get_contents( '../description.json') , true);

        $max_count_demos = (int) $module_description['extra']['count_demo_save'];
        $max_count_match_history = (int) $module_description['extra']['count_match_history_save'];

        # ПРОВЕРКА / УДАЛЕНИЕ СТРАНЫХ ДЕМОК / ЛОГОВ

        if( ! empty( $max_count_demos ) ) {

            $demos = array_diff( scandir( '../temp/demos/', 1 ), array( '..', '.' ) );

            sort( $demos, SORT_NUMERIC );

            $demos_fix = $demos;

            $count_demos = sizeof( $demos );

            if( $count_demos > $max_count_demos ) {

                $del = $count_demos - $max_count_demos;

                for ($i = 0; $i <= $del; $i++) {
                    unlink( '../temp/demos/' . $demos_fix[ $i ] );
                }
            }
        }

        if( ! empty( $max_count_match_history ) ) {

            $logs = array_diff( scandir( '../temp/logs/', 1 ), array( '..', '.' ) );

            sort( $logs, SORT_NUMERIC );

            $logs_fix = $logs;

            $count_logs = sizeof( $logs );

            if( $count_logs > $max_count_match_history ) {

                $del = $count_logs - $max_count_match_history;

                for ($i = 0; $i <= $del; $i++) {
                    unlink( '../temp/logs/' . $logs_fix[ $i ] );

                    $num = ( int ) substr( $logs_fix[ $i ], 0, -5 );

                    $sql_1 = "DELETE FROM `lvl_base_matches` WHERE `match_id` = '{$num}'";

                    $this->mysqli->query( $sql_1 );

                    $sql_2 = "DELETE FROM `lvl_base_matches_scoretotal` WHERE `id` = '{$num}'";

                    $this->mysqli->query( $sql_2 );
                }
            }
        }
    }

    public function parse_log_file( $log ) {

        $file_arr = file( $log );

        $file_size = sizeof( $file_arr );

        $match_start = [];
        $match_end = [];
        $this->steam_id_kills = [];
        $this->steam_id_death = [];
        $this->join_ids = [];

        # ПАРСЕРОМ ЧИТАЕМ ЛОГ

        for ( $i = 0; $i <= $file_size; $i++ ) {

            if ( preg_match('/: World triggered "Match_Start" on /', $file_arr[$i] ) ) {
                $type = '/([0-9]{2}\\/[0-9]{2}\\/[0-9]{4}) - ([0-9]{2}:[0-9]{2}:[0-9]{2}): World triggered "Match_Start" on "(.+)"/u';
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);
                $final[$i] = [
                    'date' => $arr[0][1],
                    'time' => $arr[0][2],
                    'match_map' => $arr[0][3],
                ];

                $match_start = [
                    'match_start_date' => $arr[0][1],
                    'match_start_time' => $arr[0][2],
                    'match_start_map' => $arr[0][3]
                ];
            }

            if ( preg_match('/ "Round_Start"/', $file_arr[$i] ) ) {
                $type = "/([0-9]{2}\\/[0-9]{2}\\/[0-9]{4}) - ([0-9]{2}:[0-9]{2}:[0-9]{2})/u";
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);
                $final[$i] = [
                    'date' => $arr[0][1],
                    'time' => $arr[0][2],
                    'type' => 'world',
                    'act' => 'Round_Start'
                ];
            }

            if ( preg_match('/ Team "[A-Z]+" triggered /', $file_arr[$i] ) ) {
                $type = '/ Team "([A-Z]+)" triggered "([a-zA-Z_]+)" [(]CT "([0-9]+)"[)] [(]T "([0-9]+)"[)]/u';
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);
                $final[$i] = [
                    'team_win' => $arr[0][1],
                    'win_type' => $arr[0][2],
                    'CT_scored' => $arr[0][3],
                    'TERRORIST_scored' => $arr[0][4]
                ];
            }

            if ( preg_match('/ Team "CT" scored /', $file_arr[$i] ) ) {
                $type = '/with "([0-9]+)"/u';
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);
                $final[$i-1] += [
                    'CT_players' => $arr[0][1]
                ];
            }

            if ( preg_match('/ Team "TERRORIST" scored /', $file_arr[$i] ) ) {
                $type = '/with "([0-9]+)"/u';
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);
                $final[$i-2] += [
                    'TERRORIST_players' => $arr[0][1]
                ];
            }

            if ( preg_match('/ "Round_End"/', $file_arr[$i] ) ) {
                $type = "/([0-9]{2}\\/[0-9]{2}\\/[0-9]{4}) - ([0-9]{2}:[0-9]{2}:[0-9]{2})/u";
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);
                $final[$i-3] += [
                    'date' => $arr[0][1],
                    'time' => $arr[0][2],
                    'type' => 'world',
                    'act' => 'Round_End'
                ];
            }

            if ( preg_match('/ killed "/', $file_arr[$i] ) ) {
                $type = '/([0-9]{2}\\/[0-9]{2}\\/[0-9]{4}) - ([0-9]{2}:[0-9]{2}:[0-9]{2}): "(.+)<[0-9]+><(.+)><(.+)>" \\[(.+)\\] killed "(.+)<[0-9]+><(.+)><(.+)>" \\[(.+)\\] with "(.+)"/u';
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);

                if ( preg_match('/ with ".+" [(](.+)[)]/u', $file_arr[$i] ) ) {
                    $type = '/ with ".+" [(](.+)[)]/u';
                    preg_match_all($type, $file_arr[$i], $killer_type_kill, PREG_SET_ORDER);
                } else {
                    $killer_type_kill[0][1] = '';
                }

                $final[$i] = [
                    'date' => $arr[0][1],
                    'time' => $arr[0][2],
                    'killer_name' => $arr[0][3],
                    'killer_id' => $arr[0][4],
                    'killer_team' => $arr[0][5],
                    'killer_coordinates' => $arr[0][6],
                    'killed_name' => $arr[0][7],
                    'killed_id' => $arr[0][8],
                    'killed_team' => $arr[0][9],
                    'killed_coordinates' => $arr[0][10],
                    'killer_weapon' => $arr[0][11],
                    'killer_type_kill' => $killer_type_kill[0][1],
                    'type' => 'killed'
                ];

                $this->match_player_kills[] = $arr[0][4];
                $this->match_player_deaths[] = $arr[0][8];
                $this->player_team[ $arr[0][4] ] = $arr[0][5];
                $this->player_team[ $arr[0][8] ] = $arr[0][9];
                $this->player_name[ $arr[0][4] ] = $arr[0][3];
                $this->player_name[ $arr[0][8] ] = $arr[0][7];
            }

            if ( preg_match('/ Game Over: /', $file_arr[$i] ) ) {
                $type = '/([0-9]{2}\\/[0-9]{2}\\/[0-9]{4}) - ([0-9]{2}:[0-9]{2}:[0-9]{2}): Game Over: (.+) .+ (.+) score (.+):(.+) after (.+) min/u';
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);
                $final[$i] = [
                    'act' => 'Game Over',
                    'date' => $arr[0][1],
                    'time' => $arr[0][2],
                    'game_type' => $arr[0][3],
                    'match_map' => $arr[0][4],
                    'match_score_ct' => $arr[0][5],
                    'match_score_t' => $arr[0][6],
                    'match_time' => $arr[0][7]
                ];

                $match_end = [
                    'match_end_date' => $arr[0][1],
                    'match_end_time' => $arr[0][2],
                    'match_end_score_t' => $arr[0][5],
                    'match_end_score_ct' => $arr[0][6],
                    'match_end_map' => $arr[0][4]
                ];
            }

            if ( preg_match('/ entered the game/', $file_arr[$i] ) ) {
                $type = '/([0-9]{2}\\/[0-9]{2}\\/[0-9]{4}) - ([0-9]{2}:[0-9]{2}:[0-9]{2}): "(.+)<[0-9]+><(.+)><>" entered the game/u';
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);

                if( $arr[0][4] !== 'BOT') {

                    $final[$i] = [
                        'act' => 'join',
                        'date' => $arr[0][1],
                        'time' => $arr[0][2],
                        'name' => $arr[0][3],
                        'player_id' => $arr[0][4],
                    ];

                    $this->join_ids[] = $arr[0][4];

                }

            }

            if ( preg_match('/ to <Unassigned>/', $file_arr[$i] ) ) {
                $type = '/([0-9]{2}\\/[0-9]{2}\\/[0-9]{4}) - ([0-9]{2}:[0-9]{2}:[0-9]{2}): "(.+)<[0-9]+><(.+)>" switched from team <.+> to <Unassigned>/u';
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);

                if( $arr[0][4] !== 'BOT') {

                    $final[$i] = [
                        'act' => 'quit',
                        'date' => $arr[0][1],
                        'time' => $arr[0][2],
                        'name' => $arr[0][3],
                        'player_id' => $arr[0][4],
                    ];

                }

            }

            if ( preg_match('/T>" say "/', $file_arr[$i] ) ) {
                $type = '/([0-9]{2}\\/[0-9]{2}\\/[0-9]{4}) - ([0-9]{2}:[0-9]{2}:[0-9]{2}): "(.+)<[0-9]+><(.+)><(.+)>" say "(.+)"/u';
                preg_match_all($type, $file_arr[$i], $arr, PREG_SET_ORDER);

                $final[$i] = [
                    'act' => 'say',
                    'date' => $arr[0][1],
                    'time' => $arr[0][2],
                    'name' => $arr[0][3],
                    'id' => $arr[0][4],
                    'team' => $arr[0][5],
                    'say' => $arr[0][6]
                ];

            }
        }

        $this->log_final = array_reverse( array_values( $final ) );

        $match[] = $match_start + $match_end;

        $this->logs( 'Парсинг лога - Успешно' );
    }
}