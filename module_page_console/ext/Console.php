<?php
/**
 * @author SAPSAN 隼 #3604
 *
 * @link https://hlmod.ru/members/sapsan.83356/
 * @link https://github.com/sapsanDev
 *
 * @license GNU General Public License Version 3
 */

namespace app\modules\module_page_console\ext;

use app\modules\module_page_console\ext\Rcon;

class Console{

	public $Db;
	public $Translate;

	public function __construct( $Db, $Translate )
	{

		$this->db 				= $Db;
		$this->Translate 		= $Translate;
	}

	public function CE_Rcon_Console( $post )
	{
		if( empty( $_SESSION['user_admin'] ) )exit();
		if( empty($post['sp_rcon']) )
			exit( trim( json_encode( [ 'error'=> $this->Translate->get_translate_module_phrase('module_page_console','_EnterСommand') ] ) ) );
		else if( empty($post['sp_rcon_server']) )
			exit( trim( json_encode( [ 'error'=> $this->Translate->get_translate_module_phrase('module_page_console','_ChooseServers') ] ) ) );

		foreach( $post['sp_rcon_server'] as $_EachK => $_EachV)
		{
			$_Params 			= ['s_id'	=> (int) $_EachV];
			$_Server_Info 		= $this->db->queryAll( 'Core', 0, 0,'SELECT * FROM lvl_web_servers WHERE id = :s_id', $_Params);
			if( empty( $_Server_Info ) )
				$_Response[] = "<font color='red'>ERROR:</font> ".$this->Translate->get_translate_module_phrase('module_page_console','_ServerNotFound');
			else
			{
				$_IP = explode(':', $_Server_Info[0]['ip']);
				$_RCON = new Rcon($_IP[0], $_IP[1]);
				if( $_RCON->Connect() )
				{
				    $_RCON->RconPass( $_Server_Info[0]['rcon'] );
				    $_Onclick_Command = 'selectCommand("'.$post['sp_rcon'].'")';
				    $_Onclick_Copy_Request = 'Buffering("#request'.time().'")';
				    $_Request = trim($_RCON->Command( $post['sp_rcon'] )," \t\n\r\0\x0B");
				    $_Request = preg_replace(
				    	["/\[(.*?)\]/", "#\((.+?)\)#is", '#\"(.+?)\"#is', '/STEAM_(\d+):(\d+):(\d+)/','/<(\d+)>/','/(\d+)\.(\d+)\.(\d+)\.(\d+)/'], 

				    	["[<font color='green'>\\1</font>]","(<font color='darkorange'>\\1</font>)", '"<font color=deepskyblue>\\1</font>"',"<a id='Sid_\\3' onclick=Buffering('#Sid_\\3')>STEAM_\\1:\\2:\\3</a>", "<<font color='green'>\\1</font>>", "<a onclick=Buffering('#ip_\\1_\\2_\\3_\\4') ><font color='green' id='ip_\\1_\\2_\\3_\\4'>\\1.\\2.\\3.\\4</font></a>"], 

				    	$_Request);
				    $_Response[] = "<div class='input-form'><div class='text_on_line'><font color='green'>".$_Server_Info[0]['name']."</font></div></div>\n\n<a onclick='".$_Onclick_Command."'>".$post['sp_rcon']."</a>\n<div id='request".time()."'>".$_Request."</div>\n<a onclick='".$_Onclick_Copy_Request."'>".$this->Translate->get_translate_module_phrase('module_page_console','_CopyContents')."</a>";

				    $_RCON->Disconnect();
				}
				else $_Response[] = '<font color="red">ERROR:</font> '.$_Server_Info[0]['name'].' - '.$this->Translate->get_translate_module_phrase('module_page_console','_FailedConnect');
			}
		}
		exit( trim( json_encode( [ 'console'=> trim(implode("\n\n", $_Response)) ] ) ) );
	}

	public function CE_Get_Server_Maps( $post )
	{
		if( empty( $_SESSION['user_admin'] ) )exit();

		foreach( $post['sp_rcon_server'] as $_EachK => $_EachV)
		{	
			if( empty( $post['sp_rcon_server'] ) )
				exit( trim( json_encode( [ 'error'=> $this->Translate->get_translate_module_phrase('module_page_console','_ChooseServers') ] ) ) );
			$_Params 		= ['s_id'	=> (int) $_EachV];
			$_Server_Info 	= $this->db->queryAll( 'Core', 0, 0,'SELECT * FROM lvl_web_servers WHERE id = :s_id', $_Params);
			if( !empty( $_Server_Info ) )
			{
				$_IP = explode(':', $_Server_Info[0]['ip']);

				$_RCON = new Rcon($_IP[0], $_IP[1]);
				if( $_RCON->Connect() )
				{
				    $_RCON->RconPass( $_Server_Info[0]['rcon'] );

				    $_Maps = trim($_RCON->Command('maps *') , "\t\n\r\0\x0B");

				    $_Replace = ['-',' ','PENDING:(fs)'];
					$_Maps = str_replace( $_Replace, '', $_Maps);

					$_Maps = trim($_Maps , "\t\n\r\0\x0B");

				    $_Maps_Respons = '<div class="col-md-4"><div class="input-form"><div class="input_text">'.$this->Translate->get_translate_module_phrase('module_page_console','_ServerMaps').' <g style="color:var(--span-color)">'.$_Server_Info[0]['name'].'<g></div><select class="select" name="sp_map'.$_EachV.'">';

				    $_Maps = explode(".bsp", $_Maps);

			    	foreach( $_Maps as $_EachM )
			    	{	
			    		if( !empty( $_EachM ) )
				    		$_Maps2[] = '<option value="'.trim($_EachM, "\t\n\r\0\x0B").'">'.trim($_EachM, "\t\n\r\0\x0B").'</option>';
			   		}

				    $_RCON->Disconnect();

				    $_Maps_Respons .= implode("\n", $_Maps2).'</select></div><button class="btn" onclick="CE_Set_Map('.$_EachV.')">'.$this->Translate->get_translate_module_phrase('module_page_console','_Change').'</button></div>';

				    $_Response[] = $_Maps_Respons;

				}else $_Response[] = '';
			} else $_Response[] = '';
		}
		exit( trim( json_encode( [ 'maps'=> trim(implode($_Response)) ] ) ) );
	}

	public function CE_Set_Server_Maps( $post )
	{
		if( empty( $_SESSION['user_admin'] ) )exit();

		if( empty( $post['sp_rcon_server'] ) )
			exit( trim( json_encode( [ 'error'=> $this->Translate->get_translate_module_phrase('module_page_console','_ChooseServers') ] ) ) );

		$_Params 		= ['s_id'	=> (int) $post['sp_rcon_server']];
		$_Server_Info 	= $this->db->queryAll( 'Core', 0, 0,'SELECT * FROM lvl_web_servers WHERE id = :s_id', $_Params);

		if( !empty( $_Server_Info ) )
		{
			$_IP = explode(':', $_Server_Info[0]['ip']);
			$_RCON = new Rcon($_IP[0], $_IP[1]);
			if( $_RCON->Connect() )
			{
			    $_RCON->RconPass( $_Server_Info[0]['rcon'] );
			    $_RCON->Command('map '.$post['sp_map']);
			   
			    $_Response = "<div class='input-form'><div class='text_on_line'><font color='green'>".$_Server_Info[0]['name']."</font></div></div>\n\n".$this->Translate->get_translate_module_phrase('module_page_console','_SetMap').' <g style="color:var(--span-color)">'.$post['sp_map'].'<g>';

			   $_RCON->Disconnect();
			
			}
			else exit( trim( json_encode( [ 'error'=> '<font color="red">ERROR:</font> '.$_Server_Info[0]['name'].' - '.$this->Translate->get_translate_module_phrase('module_page_console','_FailedConnect') ] ) ) );

			exit( trim( json_encode( [ 'console'=> trim($_Response) ] ) ) );
		}else exit( trim( json_encode( [ 'error'=> '<font color="red">ERROR:</font> '.$this->Translate->get_translate_module_phrase('module_page_console','_ServerNotFound') ] ) ) );

	}
}