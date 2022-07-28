<?php
/**
 * @author Anastasia Sidak <m0st1ce.nastya@gmail.com>
 *
 * @link https://steamcommunity.com/profiles/76561198038416053
 * @link https://github.com/M0st1ce
 *
 * @license GNU General Public License Version 3
 */

// Количество демок на странице.
define('DEMOS_ON_PAGE', '80');

$match_id = (int) intval ( get_section( 'id', '1' ) );

// Номер страницы.
$page_num = (int) intval ( get_section( 'num', '1' ) );

// Номер страницы.
$server_num = (int) intval ( get_section( 'server', '1' ) );

// Подсчёт кол-ва страниц
$page_max = ceil($Db->queryNum('Core', 0, 0, "SELECT COUNT(*) FROM lvl_base_matches_scoretotal ")[0]/DEMOS_ON_PAGE);

$page_num_min = ($page_num - 1) * DEMOS_ON_PAGE;

( $page_num > $page_max || $page_num <= '0' ) && header('Location: ' . $General->arr_general['site']);

if( empty( $_GET['section'] ) ) {

    // Получение всех sid
    $server_ids = $Db->queryAll( 'Core', 0, 0, "SELECT `server_id` FROM lvl_base_matches_scoretotal");

    foreach ($server_ids as $key => $value):
        $res_server_ids[] = $value['server_id'];
    endforeach;
    $res_server_ids = array_unique( $res_server_ids );

    asort( $res_server_ids );

    $res_server_ids = array_values( $res_server_ids );

    ! in_array( $server_num, $res_server_ids) && $server_num = $res_server_ids[0];

    ! in_array( $server_num, $res_server_ids ) && get_iframe( '009', 'Данная страница не существует' );

    $servers = action_array_keep_keys( $General->server_list, ['id','name_custom'] );

    $res = $Db->queryAll('Core', 0, 0, "SELECT lvl_base_matches_scoretotal.id,
											   lvl_base_matches_scoretotal.server_id,
											   lvl_base_matches_scoretotal.CT,
											   lvl_base_matches_scoretotal.T,
											   lvl_base_matches_scoretotal.map,
											   lvl_base_matches_scoretotal.time,
											   lvl_base_matches_scoretotal.duration,
											   lvl_web_servers.name
											   FROM lvl_base_matches_scoretotal
											   INNER JOIN lvl_web_servers ON lvl_web_servers.id = lvl_base_matches_scoretotal.server_id
											   WHERE server_id='" . $server_num . "' order by id desc LIMIT " . $page_num_min . "," . DEMOS_ON_PAGE . " ");

}

# match page

if( ! empty( $_GET['section'] ) && $_GET['section'] == 'match' ){

    $res = ( $Db->queryAll('Core', 0, 0, "SELECT * FROM lvl_base_matches_scoretotal WHERE id='" . $match_id . "'LIMIT 1") )[0];

    $match_log = json_decode(file_get_contents( MODULES . 'module_page_demos/temp/logs/' . $match_id . '.json') , true);

    $match_log_size = sizeof( $match_log );

    $players = $Db->queryAll('Core', 0, 0, "SELECT * FROM lvl_base_matches WHERE match_id='" . $match_id . "' order by kills desc");

    $c_players = sizeof($players);
    $endreasons = array (
        "SFUI_Notice_All_Hostages_Rescued" => "All Hostages Rescued",
        "SFUI_Notice_Bomb_Defused" => "Bomb defused",
        "SFUI_Notice_CTs_Win" => "Enemy eliminated",
        "SFUI_Notice_Hostages_Not_Rescued" => "Time Expired",
        "SFUI_Notice_Target_Bombed" => "Target bombed",
        "SFUI_Notice_Terrorists_Win" => "Enemy eliminated"
    );

};
