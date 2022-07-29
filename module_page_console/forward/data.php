<?php
/**
 * @author SAPSAN 隼 #3604
 *
 * @link https://hlmod.ru/members/sapsan.83356/
 * @link https://github.com/sapsanDev
 *
 * @license GNU General Public License Version 3
 */

use app\modules\module_page_console\ext\Console;

$CE = new Console( $Db, $Translate );

if( IN_LR != true ) { header('Location: ' . $General->arr_general['site']); exit; }

if( isset( $_SESSION['user_admin'] )  && isset($_GET['page']))
{
    switch ($_GET['page']) {

        case 'console':
            if( isset( $_POST['sp_rcon'] ) )
            {
                $CE->CE_Rcon_Console( $_POST );exit();
            }
            else if( isset( $_POST['sp_m'] ) )
            {
                $CE->CE_Get_Server_Maps( $_POST );exit();
            }
            else if( isset($_POST['sp_sm']) )
            {
                $CE->CE_Set_Server_Maps( $_POST );exit();
            }
        break;

        default:exit();break;
    }
}

$Modules->set_page_title( $General->arr_general['short_name'] . ' :: Console');