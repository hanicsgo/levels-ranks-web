<?php
/**
 * @author SAPSAN 隼 #3604
 *
 * @link https://hlmod.ru/members/sapsan.83356/
 * @link https://github.com/sapsanDev
 *
 * @license GNU General Public License Version 3
 */

use app\modules\module_page_open_case\ext\Open_case;

$CASES = new Open_case( $Translate, $Notifications, $General, $Modules, $Db, $Auth );

if( IN_LR != true ) { header( 'Location: ' . $General->arr_general['site'] ); exit; }

if( !empty( $_POST['case_id'] ) )
{
    $CASES->loadRoulette( strip_tags($_POST['case_id']) );exit;
}
else if( !empty( $_POST['liveLoad'] ) )
{
    $CASES->liveLoad();exit;
}
if( isset($_SESSION['steamid32']))
{
    if( !empty( $_POST['case_id_open'] ) )
    {
        $CASES->openCase( strip_tags($_POST['case_id_open']) );exit;
    }
    else if( !empty( $_POST['live'] ) )
    {
        $CASES->liveUpload ( strip_tags($_POST['live']) );exit;
    }
    else if( !empty( $_POST['sale'] ) )
    {
        $CASES->saleSubject( strip_tags($_POST['sale']) );exit;
    }
    else if( isset( $_POST['up'] ) )
    {
        $CASES->upSubject( $_POST );exit;
    }
    else if( !empty( $_POST['wins'] ) )
    {
        $CASES->winSubject( strip_tags($_POST['wins']) );exit;
    }
    else if( !empty( $_POST['win_up_confirm'] ) )
    {
        $_JSON = json_decode(base64_decode(strip_tags($_POST['win_up_confirm'])), true );
        if(!empty($_JSON['w_confirm']) && !empty($_JSON['up']))
        {
            $CASES->upSubject( $_JSON ); exit;
        }
        else exit;
    }
    else if( !empty( $_POST['win_up_server'] ) )
    {
        $_JSON = json_decode(base64_decode(strip_tags($_POST['win_up_server'])), true );
        if(!empty($_JSON['sid']) && !empty($_JSON['up']))
        {
            $CASES->upSubject( $_JSON ); exit;
        }
        else exit;
    }
}
if( isset( $_SESSION['user_admin'] )  && isset( $_GET['section'] ) )
{
    switch ( strip_tags($_GET['section']) )
    {
        case 'admin':
            if( isset( $_POST['server_name'] ) )
            {
                $CASES->createServer( $_POST );exit;
            }
            else if( isset( $_POST['case_name'] ) )
            {
              $CASES->createCase( $_POST );exit;
            }
            else if( isset( $_POST['case_id_edit'] ) )
            {
              $CASES->editCase( $_POST );exit;
            }
            else if( isset( $_POST['server_edit'] ) )
            {
              $CASES->editServer( $_POST );exit;
            }
            else if( isset( $_POST['case_delete'] ) )
            {
               $CASES->deletCase( $_POST );exit;
            }
            else if(isset($_POST['webhoock_url']))
            {
                $CASES->Discord($_POST); exit;
            }
        break;
        case 'case':
            if(isset( $_POST['case_id_subject'] ))
            {
                $CASES->createSubject( $_POST );exit;
            }
            else if( isset( $_POST['subject_id_edit'] ) )
            {
                $CASES->editSubject( $_POST );exit;
            }
            else if( isset( $_POST['subject_delete'] ) )
            {
                $CASES->deletSubject( $_POST );exit;
            }
        break;
        case 'cases_list':
           ###
        break;
        
        case 'updates':
           if(isset($_POST['update_module']))
           {
                $Updator->Update( 'cases', 'module_page_open_case');
           }
        break;

        default:exit;break;
    }
}

if( empty( $Db->db_data['cases'] ) )
{
    require MODULES.'module_page_open_case/includes/install.php';
    exit;
}

//Проверка в базе данных наличие таблиц.
if( isset( $Db->db_data['cases'] ) )
{
    $checkTable = [
        'cases',
        'cases_discord',
        'cases_live',
        'cases_open',
        'cases_subjects',
        'cases_wins'
    ];
    foreach ( $checkTable as $key )
    {
       if( !$Db->mysql_table_search( 'cases', $Db->db_data['cases'][0]['USER_ID'], $Db->db_data['cases'][0]['DB_num'], $key ) )
       {
           require MODULES.'module_page_open_case/includes/install.php';
           exit;
        }
    }
}

$CASES->LkBalancePlayer();