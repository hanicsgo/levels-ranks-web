<?php
/**
 * @author SAPSAN 隼 #3604
 *
 * @link https://hlmod.ru/members/sapsan.83356/
 * @link https://github.com/sapsanDev
 *
 * @license GNU General Public License Version 3
 */

namespace app\modules\module_page_open_case\ext;

use app\modules\module_page_open_case\ext\Rcon;

class Open_case {

	const VIP_REFRESH = 'sm_refresh_vips';
	const ADM_REFRESH = 'sm_reloadadmins';
	const CREDITS_GIVE = 'sm_cases_credits';
	const WINS = 'sm_cases_wins';
	const WCS_VIP = 'wcs_givevip';
	const WCS_GOLD = 'wcs_setgold';
	const WCS_LVL = 'wcs_setlblvl';
	const WCS_RACE = 'wcs_giveprivate';

	public $Modules;
	public $Translate;
	public $General;
	public $Db;
	public $Notifications;
	public $Auth;


	public function __construct( $Translate, $Notifications, $General, $Modules, $Db, $Auth )
	{
		$this->Modules = $Modules;
		$this->Translate = $Translate;
		$this->General = $General;
		$this->db = $Db;
		$this->Notifications = $Notifications;
		$this->Auth = $Auth;
	}

	public function Discord($post)
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit; }
			if(empty($post['auth']))
				$auth = 0;
			else
			{
				if(!preg_match('/^\d+$/', $post['auth']))
					$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_Error'),'Error');
				if(empty($post['webhook']))
					$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_Webhook'),'Error');
				$auth = 1;
			}
			$param = ['url' => $post['webhook'], 'auth' => $auth];
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "UPDATE cases_discord SET url=:url, auth=:auth",$param);
			$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_Saved'),'success');
	}

	public function LkBalancePlayer()
	{
		if(isset($_SESSION['steamid32']))
		{
			$param = ['auth'=> $_SESSION['steamid32']];
			$infoUser =$this->db->queryAll('lk', $this->db->db_data['lk'][0]['USER_ID'], $this->db->db_data['lk'][0]['DB_num'], "SELECT cash FROM lk WHERE auth = '$param[auth]'");
			$this->Modules->set_user_info_text($this->Translate->get_translate_module_phrase('module_page_open_case','_Balance').': '.$this->Translate->get_translate_module_phrase('module_page_open_case','_AmountCourse').' <b class="material-balance">'.number_format($infoUser[0]['cash'],0,' ', ' ').'</b>');
		}
	}

	public function DiscordData()
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit; }
			$DiscordData = $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_discord");
			if(!empty($DiscordData))
					return $DiscordData[0];
	}

	public function createCase($post)
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit; }
			if(empty($post['case_name']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterNameCase'),'error');
			else if(empty($post['case_sort']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterSortCase'),'error');
			if(empty($post['case_price']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterAmountCase'),'error');
			else if(!preg_match('/^[0-9]{1,1000}.[0-9]{1,2}$/', $this->WM($post['case_price'])))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_FormatAmountCase'),'Error');
			else if(empty($_FILES['case_img']['tmp_name']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_ChangeCasePNG'),'error');
			$size = getimagesize($_FILES['case_img']['tmp_name']);
			if ($size[2] != IMAGETYPE_PNG)
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_OnlyPNG'),'error');
			else if($size[0]>400 || $size[1]>400)
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_SizePNG'),'error');
			if(!file_exists('storage/cache/img/cases'))mkdir("storage/cache/img/cases", 0777);
			$apend='storage/cache/img/cases/'.date('YmdHis').rand(100,1000).'.png';
			move_uploaded_file($_FILES['case_img']['tmp_name'], $apend);
			$data = ['case_name' =>$post['case_name'], 'case_type'=>$post['case_type'], 'case_sort'=>$post['case_sort'], 'case_price'=>$post['case_price'], 'case_img'=>$apend];
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "INSERT INTO cases(case_name, case_type, case_sort, case_price, case_img) VALUES (:case_name, :case_type, :case_sort, :case_price, :case_img)", $data);
			$id = $this->db->lastInsertId('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num']);
			exit(json_encode(['location'=>'?page=cases&section=case&id='.$id]));
	}

	public function editCase($post)
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit; }
			$case = $this->getPriceCase($post['case_id_edit']);
			if(empty($case))$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_CaseNotFound'),'error');
			else if(empty($post['case_name_edit']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterNameCase'),'error');
			else if(empty($post['case_sort_edit']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterSortCase'),'error');
			if(empty($post['case_price_edit']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterAmountCase'),'error');
			else if(!preg_match('/^[0-9]{1,1000}.[0-9]{1,2}$/', $this->WM($post['case_price_edit'])))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_FormatAmountCase'),'error');
			$caseImg = $case['case_img'];
			if(!empty($_FILES['case_img_edit']['tmp_name'])){
				$size = getimagesize($_FILES['case_img_edit']['tmp_name']);
				if ($size[2] != IMAGETYPE_PNG)
					$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_OnlyPNG'),'error');
				else if($size[0]>400 || $size[1]>400)
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_SizePNG'),'error');
				unlink($case['case_img']);
				$apend='storage/cache/img/cases/'.date('YmdHis').rand(100,1000).'.png';
				move_uploaded_file($_FILES['case_img_edit']['tmp_name'], $apend);
				$caseImg = $apend;
			}
			$data = ['case_name' =>$post['case_name_edit'], 'case_type'=>$post['case_type_edit'], 'case_sort'=>$post['case_sort_edit'], 'case_price'=>$post['case_price_edit'], 'case_img'=>$caseImg, 'id'=>$post['case_id_edit']];
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "UPDATE cases SET case_name =:case_name, case_type =:case_type, case_sort =:case_sort, case_price=:case_price, case_img=:case_img WHERE id = :id", $data);
			$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_Saved'),'success');
	}

	public function deletCase($post)
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit; }
			$case = $this->getPriceCase($post['case_delete']);
			if(empty($case))$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_CaseNotFound'),'error');
			if(file_exists($case['case_img']))
				unlink($case['case_img']);
			$data = ['id'=>$post['case_delete']];
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "DELETE FROM `cases` WHERE id = :id", $data);
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "DELETE FROM `cases_subjects` WHERE case_id = :id", $data);
			$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_CaseDeleted'),'success');
	}

	public function createSubject($post)
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit; }
			if(empty($post['subject_name']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterSubjectName'),'error');
			else if(empty($post['subject_desc']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterSubjectShortDesc'),'error');
			else if(empty($post['subject_sort']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterSubjectSort'),'error');
			else if(empty($post['subject_content']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterSubjectPrize'),'error');
			else if(empty($_FILES['subject_img']['tmp_name']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_ChangeSubjectPNG'),'error');
			$size = getimagesize($_FILES['subject_img']['tmp_name']);
			if ($size[2] != IMAGETYPE_PNG)
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_OnlyPNG'),'error');
			else if($size[0]>400 || $size[1]>400)
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_SizePNG'),'error');
			if(!file_exists('storage/cache/img/cases'))mkdir("storage/cache/img/cases", 0777);
			$apend='storage/cache/img/cases/'.date('YmdHis').rand(100,1000).'.png';
			move_uploaded_file($_FILES['subject_img']['tmp_name'], $apend);
			if(empty($post['subject_sale']))$sale = 0;
			else $sale = $post['subject_sale'];
			$data = [
				'server_id'=>$post['subject_server'],
				'case_id'=>$post['case_id_subject'],
				'subject_name'=>$post['subject_name'],
				'subject_desc'=>$post['subject_desc'],
				'subject_class'=>$this->bgReturn($post['subject_class']),
				'subject_img'=>$apend,
				'subject_type'=>$post['subject_type'],
				'subject_content'=>$post['subject_content'],
				'subject_chance'=>$post['subject_chance'],
				'subject_sort'=>$post['subject_sort'],
				'subject_sale'=>$sale
			];
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "INSERT INTO cases_subjects(server_id, case_id, subject_name, subject_desc, subject_class, subject_img, subject_type, subject_content, subject_chance, subject_sale, subject_sort) VALUES(:server_id, :case_id, :subject_name, :subject_desc, :subject_class, :subject_img, :subject_type, :subject_content, :subject_chance, :subject_sale, :subject_sort)", $data);
			$this->message('Предмет '.$post['subject_name'].' добавлен!','success');
	}

	public function editSubject($post)
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit; }
			$subject = $this->getSubjectData($post['subject_id_edit']);
			if(empty($subject))$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_SubjectNotFound'),'error');
			if(empty($post['subject_name_edit']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterSubjectName'),'error');
			else if(empty($post['subject_desc_edit']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterSubjectShortDesc'),'error');
			else if(empty($post['subject_sort_edit']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterSubjectSort'),'error');
			else if(empty($post['subject_content_edit']))
				$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_EnterSubjectPrize'),'error');
			$img = $subject[0]['subject_img'];
			if(!empty($_FILES['subject_img_edit']['tmp_name']))
			{
				unlink($img);
				$size = getimagesize($_FILES['subject_img_edit']['tmp_name']);
				if ($size[2] != IMAGETYPE_PNG)
					$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_OnlyPNG'),'error');
				else if($size[0]>400 || $size[1]>400)
					$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_SizePNG'),'error');
				$apend='storage/cache/img/cases/'.date('YmdHis').rand(100,1000).'.png';
				move_uploaded_file($_FILES['subject_img_edit']['tmp_name'], $apend);
				$img = $apend;
			}
			if(empty($post['subject_sale_edit']))$sale = 0;
			else $sale = $post['subject_sale_edit'];
			$data = [
				'id'=>$post['subject_id_edit'],
				'server_id' => $post['subject_server_edit'],
				'subject_name'=>$post['subject_name_edit'],
				'subject_desc'=>$post['subject_desc_edit'],
				'subject_class'=>$this->bgReturn($post['subject_class_edit']),
				'subject_img'=>$img,
				'subject_type'=>$post['subject_type_edit'],
				'subject_content'=>$post['subject_content_edit'],
				'subject_chance'=>$post['subject_chance_edit'],
				'subject_sort'=>$post['subject_sort_edit'],
				'subject_sale'=>$sale
			];
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "UPDATE cases_subjects SET server_id=:server_id, subject_name=:subject_name, subject_desc=:subject_desc, subject_class=:subject_class, subject_img=:subject_img, subject_type=:subject_type, subject_content=:subject_content, subject_chance=:subject_chance, subject_sale=:subject_sale, subject_sort=:subject_sort WHERE id=:id", $data);
			$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_Saved'),'success');
	}

	public function deletSubject($post)
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit; }
		$subject = $this->getSubjectData($post['subject_delete']);
		if(empty($subject))$this->message($this->Translate->get_translate_module_phrase('module_page_open_case','_SubjectNotFound'),'Error');
		if(file_exists($subject[0]['subject_img']))
			unlink($subject[0]['subject_img']);
		$data = ['id'=>$post['subject_delete']];
		$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "DELETE FROM `cases_subjects` WHERE id = :id", $data);
		$this->message('Предмет удален!','success');
	}
	public function getCases()
	{
		return $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases WHERE id IN (SELECT case_id FROM cases_subjects  GROUP BY case_id HAVING COUNT(case_id) >= 3) ORDER BY case_sort ASC");
	}

	public function getOpens($id){
		$data = ['case_id'=>$id];
		$opens = $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT COUNT(case_id) FROM cases_open WHERE case_id=:case_id",$data);
		return $opens[0]['COUNT(case_id)'];
	}

	public function getCasesAdmin()
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit;}
		return $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases");
	}

	public function getServersAdmin()
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit;}
		$servers =  $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_servers");
		return $servers;
	}

	public function getServers($id)
	{
		$data = ['id' => $id];
		$servers =  $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_servers WHERE id = :id", $data);
		return $servers;
	}

	public function getCaseSubjectsAdmin($id)
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit;}
		$data = ['case_id' => $id];
		$subjects =  $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_subjects WHERE case_id = :case_id",$data);
		return $subjects;
	}

	public function getWins()
	{
		if( empty($_SESSION['steamid32']) || IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit;}
		$data = ['steam_id'=>$_SESSION['steamid32'],];
		return $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_wins WHERE steam_id = :steam_id ORDER BY id DESC", $data);
	}
	public function getWinsData($id){
		$data = ['steam_id'=>$_SESSION['steamid32'], 'id'=>$id];
		return $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_wins WHERE steam_id = :steam_id AND id =:id", $data);
	}

	public function getCaseSubjects($id){
		if(!preg_match('/^[0-9]{1,3}$/', $id))return;
		$data = ['case_id' => $id];
		$subjects =  $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_subjects WHERE case_id = :case_id ORDER BY subject_sort ASC",$data);
		return $subjects;
	}

	public function getSubjectData($id)
	{
		if(!preg_match('/^[0-9]{1,3}$/', $id)) return;
		$data = ['id' => $id];
		$subject = $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_subjects WHERE id = :id",$data);
		return $subject;
	}

	public function getPriceCase($id)
	{
		if(!preg_match('/^[0-9]{1,3}$/', $id)) return;
		$data = ['id' => $id];
		$price = $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases WHERE id = :id",$data);
		return $price[0];
	}

	public function openCasesList()
	{
		if( !isset( $_SESSION['user_admin'] ) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit;}
		$cases =  $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_open ORDER BY date DESC");
		if(!empty($cases))
		{
			$array = [];
			foreach ($cases as $key) {
				$caseInfo = $this->getPriceCase($key['case_id']);
				$subJson = json_decode($key['wins'], true);
				array_push($array, [
					'steam_id'=> $key['steam_id'],
					'case_id'=> $key['case_id'],
					'case_img'=>$this->ImgLoad($caseInfo['case_img']),
					'case_name'=> $caseInfo['case_name'],
					'subject_img'=> $this->ImgLoad($subJson['subject_img']),
					'subject_name'=> $subJson['subject_name'],
					'date'=> date('m.d.Y H:i:s',$key['date']),

				]);
			}
			return $array;
		}
		return false;
	}

	public function getTimeFreeOpen($session,$id)
	{
		$data = ['steam_id'=> $session, 'case_id'=>$id];
		$free = $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT date FROM cases_open WHERE steam_id=:steam_id AND case_id=:case_id ORDER BY date DESC LIMIT 1",$data);
		if($free)
			return $free[0];
	}

	public function loadRoulette($id)
	{
		if(!preg_match('/^[0-9]{1,3}$/', $id))return;
			$subjects = $this->getCaseSubjects($id);
			shuffle($subjects);
			$return = array();
			$count = 0;
			$count2 = 0;
			unset($_SESSION['cases']);
			foreach ($subjects as $key ){
				$_SESSION['cases'][$key['id']] = $count2++;
				array_push($return, array(
			                'style' 	=> $key['subject_class'],
			                'data' 		=> $count++,
			                'img' 		=> $key['subject_img'],
			                'desc'		=> $key['subject_desc'],
			                'name' 		=> $key['subject_name']
			            ));
			}
			exit(json_encode(array_reverse($return)));
	}

	public function openCase($id)
	{
		if( empty($_SESSION['steamid32']) && IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit;}
		if(!preg_match('/^[0-9]{1,3}$/', strip_tags($id)))return;
			$subjects = $this->getCaseSubjects($id);
			$casePrice = $this->getPriceCase($id);
			if(empty($subjects) || empty($casePrice))exit(json_encode(['error'=> $this->Translate->get_translate_module_phrase('module_page_open_case','_Error')]));
			$data = ['auth' => $_SESSION['steamid32']];
			$userCash = $this->db->queryAll('lk', $this->db->db_data['lk'][0]['USER_ID'], $this->db->db_data['lk'][0]['DB_num'], "SELECT cash FROM lk WHERE auth=:auth",$data);
			$balance = $userCash[0]['cash'];
			if($casePrice['case_type'] == 1)
			{
				if($userCash[0]['cash'] < $casePrice['case_price'])
					exit(json_encode(['style' => 'transparent','message'=>'
				                	<div class="bonuses-title">GG WP</div>
									<div style="margin:40px;text-align: center;color: #ffc607;text-shadow: 0 0 8px #faff00;font-size: 18px">
									'.$this->Translate->get_translate_module_phrase('module_page_open_case','_NoMoney').'
									</div>
									<div class="bonuses-but"><a href="?page=lk">+ '.$this->Translate->get_translate_module_phrase('module_page_open_case','_ReplenishBalance').'</a></div>']));
				$balance = $userCash[0]['cash'] - $casePrice['case_price'];
				$data = ['auth' => $_SESSION['steamid32'], 'cash' =>$balance];
				$this->db->query('lk', $this->db->db_data['lk'][0]['USER_ID'], $this->db->db_data['lk'][0]['DB_num'], "UPDATE lk SET cash =:cash WHERE auth=:auth",$data);
			}
			else if($casePrice['case_type'] == 2)
			{
				$free = $this->getTimeFreeOpen($_SESSION['steamid32'], $id);
				$openDate = $casePrice['case_price']+$free['date'];
				if($openDate > time()){
					$date = $openDate-time();
					exit(json_encode(['date' =>date('d.m.Y.H.i',$openDate), 'style' => 'transparent','message'=>'
				                	<div class="bonuses-title">GG WP</div>
									<div style="margin:40px;text-align: center;color: var(--sidebar-gradient-1);text-shadow: 0 0 8px var(--sidebar-gradient-1);font-size: 18px">
									'.$this->Translate->get_translate_module_phrase('module_page_open_case','_WillAvailable').'<br>
										<div class="eTimer"></div>
										</div>']));
				}
			}
			foreach ($subjects as $key ) 
			{
				$subjectsCount[$key['id']] =$key['subject_chance'];
			}
			$subjectId = $this->roulette($subjectsCount);
			$randomWin = $_SESSION['cases'][$subjectId];
			$subjectInfo = $this->getSubjectData($subjectId);
			if(empty($subjectInfo))exit(json_encode(['error'=>'Error']));
			switch ($subjectInfo[0]['subject_class']) {
				case 'gold':		$color = '725a39';break;
				case 'red':			$color = 'ec8492';break;
				case 'pink':		$color = 'df0117';break;
				case 'purple':		$color = 'c555ff';break;
				case 'blue':		$color = '5655d3';break;
				case 'turquoise':	$color = '2afdf4';break;
				case 'grey':		$color = '3e3e3e';break;
				default:			$color = 'cacaca';break;
			}
			$dataOpen = [
				'steam_id'	=>$_SESSION['steamid32'],
				'case_id'	=>$id,
				'wins'		=>json_encode(['subject_name'=>$subjectInfo[0]['subject_name'],'subject_desc'=>$subjectInfo[0]['subject_desc'],
									 'subject_class'=>$subjectInfo[0]['subject_class'],'subject_img'=>$subjectInfo[0]['subject_img'],
									 'subject_sale'=>$subjectInfo[0]['subject_sale']]),
				'date'		=> time()
			];
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "INSERT INTO cases_open(steam_id, case_id, wins, date) VALUES (:steam_id, :case_id, :wins, :date)", $dataOpen);
			$data = [
				'subject_id'		=>$subjectId,
				'subject_name'		=>$subjectInfo[0]['subject_name'],
				'subject_desc'		=>$subjectInfo[0]['subject_desc'],
				'subject_style'		=>$subjectInfo[0]['subject_class'],
				'subject_img'		=>$subjectInfo[0]['subject_img'],
				'steam_id'			=>$_SESSION['steamid32'],
				'sale'				=>$subjectInfo[0]['subject_sale'],
				'up'=>0, 'sell'=>0
			];
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'],'INSERT INTO cases_wins(subject_id, subject_name, subject_desc, subject_style, subject_img, steam_id, sale, up, sell) VALUES(:subject_id, :subject_name, :subject_desc, :subject_style, :subject_img, :steam_id, :sale, :up, :sell)', $data);
			$sales = $this->db->lastInsertId('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num']);
			switch ($subjectInfo[0]['subject_type']){
				case 1:
					$data = ['steam_id' => $_SESSION['steamid32'], 'sell' => 1, 'up' => 1, 'id'=>$sales];
					$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'],'UPDATE cases_wins SET sell =:sell, up =:up WHERE id=:id AND steam_id=:steam_id',$data);
					$wincash = $balance+$subjectInfo[0]['subject_content'];
					$data = ['auth' => $_SESSION['steamid32'], 'cash' =>$wincash];
					$this->db->query('lk', $this->db->db_data['lk'][0]['USER_ID'], $this->db->db_data['lk'][0]['DB_num'],'UPDATE lk SET cash =:cash WHERE auth=:auth',$data);
					$html = '<div class="bonuses-title"'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
							  	<img class="subject-image" src="'.$subjectInfo[0]['subject_img'].'" alt="'.$subjectInfo[0]['subject_name'].' '.$subjectInfo[0]['subject_desc'].'">
								<div><span style="color:'.$color.';text-overflow: ellipsis;font-size: 35px;font-weight: 900;">'.$subjectInfo[0]['subject_name'].'</span><br>
									<span style="color: #fff;text-overflow: ellipsis;font-size: 25px;">'.$subjectInfo[0]['subject_desc'].'</span>
								</div>';
					$return = array(
			                'style'		=> 'transparent',
			                'ubal' 		=> number_format($balance,0,' ', ' ').' ₽',
			                'wcash' 	=> number_format($wincash,0,' ', ' ').' ₽',
			                'live' 		=> $subjectId,
			                'win' 		=> $randomWin,
			                'html' 		=> $html
			            );
					break;
				default:
					$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
							  	<img class="subject-image" src="'.$subjectInfo[0]['subject_img'].'" alt="'.$subjectInfo[0]['subject_name'].' '.$subjectInfo[0]['subject_desc'].'">
								<div><span style="color:'.$color.';text-overflow: ellipsis;font-size: 35px;font-weight: 900;">'.$subjectInfo[0]['subject_name'].'</span><br>
									<span style="color: #fff;text-overflow: ellipsis;font-size: 25px;">'.$subjectInfo[0]['subject_desc'].'</span>
								</div>
								<div class="bonuses-but"><a onclick="Swal.close()">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Take').'</a><a onclick="to_sale('.$sales.')">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Sell').' '.$subjectInfo[0]['subject_sale'].'</a></div>';
					$return = array(
			                'style' 	=> 'transparent',
			                'ubal' 		=> number_format($balance,0,' ', ' '),
			                'live' 		=> $subjectId,
			                'win' 		=> $randomWin,
			                'html' 		=> $html
			            );
					break;
			}
			$this->DiscordMsg($casePrice, $subjectInfo);
			exit(json_encode($return));
	}

	public function saleSubject($id){
		if( empty($_SESSION['steamid32']) || IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit;}
		$winsInfo = $this->getWinsData($id);
		if(empty($winsInfo))
		{
			$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Error').' #C1</div>
						<div style="margin-top:40px;">
							<span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
										  #C1: '.$this->Translate->get_translate_module_phrase('module_page_open_case','_ErrorAdminSend').'
								  	</span></div>';
			$return = array(
				                'style' 	=> 'transparent',
				                'html' 		=> $html
				            );
			exit(json_encode($return));
		}
		$subjectInfo = $this->getSubjectData($winsInfo[0]['subject_id']);
		if(empty($subjectInfo))
		{
			$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Error').' #C2</div>
						<div style="margin-top:40px;">
							<span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
									#C2: '.$this->Translate->get_translate_module_phrase('module_page_open_case','_ErrorAdminSend').'
								  	</span></div>';
			$return = array(
				                'style' 	=> 'transparent',
				                'html' 		=> $html
				            );
			exit(json_encode($return));
		}
		if($subjectInfo[0]['subject_type'] != 1 && empty($winsInfo[0]['up']) && empty($winsInfo[0]['sell'])){
			$data = ['auth' => $_SESSION['steamid32']];
			$userCash = $this->db->queryAll('lk', $this->db->db_data['lk'][0]['USER_ID'], $this->db->db_data['lk'][0]['DB_num'],'SELECT * FROM lk WHERE auth=:auth',$data);
			$balance = $userCash[0]['cash'] + $subjectInfo[0]['subject_sale'];
			$data = ['auth' => $_SESSION['steamid32'], 'cash' =>$balance];
			$this->db->query('lk', $this->db->db_data['lk'][0]['USER_ID'], $this->db->db_data['lk'][0]['DB_num'],'UPDATE lk SET cash =:cash WHERE auth=:auth',$data);
			$data = ['steam_id' => $_SESSION['steamid32'], 'sell' => 1, 'id'=>$id];
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'],'UPDATE cases_wins SET sell =:sell WHERE id=:id AND steam_id=:steam_id',$data);
			$return = array(
		                'bal' => number_format($balance,0,' ', ' '),
		            );
			exit(json_encode($return));
		}

	}

	public function upSubject($post){
		if( empty($_SESSION['steamid32'])  || IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit;}
		$winsInfo = $this->getWinsData(strip_tags($post['up']));
		if(empty($winsInfo))
		{
			$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Error').' #C1</div>
						<div style="margin-top:40px;">
							<span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
										  #C1: '.$this->Translate->get_translate_module_phrase('module_page_open_case','_ErrorAdminSend').'
								  	</span></div>';
			$return = array(
                'style' 	=> 'transparent',
                'html' 		=> $html
            );
			exit(json_encode($return));
		}
		$subjectInfo = $this->getSubjectData($winsInfo[0]['subject_id']);
		if(empty($subjectInfo))
		{
			$html = '<script>setTimeout(function(){Swal.close()},2000);</script><div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Error').' #C2</div>
						<div style="margin-top:40px;">
							<span style="color:red;text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
									#C2: '.$this->Translate->get_translate_module_phrase('module_page_open_case','_ErrorAdminSend').'
								  	</span></div>';
			$return = array(
				                'style' 	=> 'transparent',
				                'html' 		=> $html
				            );
			exit(json_encode($return));
		}
		if($subjectInfo[0]['subject_type'] != 1 && empty($winsInfo[0]['up']) && empty($winsInfo[0]['sell']))
		{		
				$casePrice = $this->getPriceCase($subjectInfo[0]['case_id']);

				if( $subjectInfo[0]['server_id']  == -1 )
				{
					if( !empty( $post['sid'] ) )
					{
						$server_id = strip_tags($post['sid']);
					}
					else 
					{
						$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_SetUserServer').'</div>';

						foreach ( $this->General->server_list as $key )
						{
							$_Options[]= '<option value="'.base64_encode(json_encode(['sid'=> $key['id'],'up'=> strip_tags($post['up']) ])).'">'.$key['name'].'</option>';
						}
						$html .= '<select name="wins_to_server" class="wins-select">'.implode("\n", $_Options).'</select>';
						$html .= '<div class="bonuses-but"><a onclick="Swal.close();window.location.reload();">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_No2').'</a>
											<a onclick="pick_up_wins_to_server()">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Yes2').'</a></div>';
						$return = array(
					                'allow'		=> 'true',
					                'style'		=> 'transparent',
					                'html' 		=> $html
				            );
						exit( json_encode( $return ) );
					}
				}
				else $server_id = $subjectInfo[0]['server_id'];

				$server = $this->Get_Server_Info( $server_id );

				switch( $subjectInfo[0]['subject_class'] )
				{
					case 'orange':	$color = '#f7e3a0';break;
					case 'purple':	$color = '#80aded';break;
					case 'pred':	$color = '#fbabb8';break;
					default:		$color = '#cacaca';break;
				}

				switch($subjectInfo[0]['subject_type'])
				{
					case 2://CASTOM
						$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
						$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
							  	<div style="margin-top:40px;">
							  		<span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">'.$subjectInfo[0]['subject_content'].'</span>
							  	</div>';
						$return = array(
				                'style' 	=> 'transparent',
				                'html' 		=> $html
				            );
					break;
					/*************************************
					 *------------ VIP R1KO	-------------*
					 *************************************/
					case 3:	
						$dataMysql = explode(';', $server['server_vip']);
						if(!empty($dataMysql))
						{	
							$vipINFO = explode(':', $subjectInfo[0]['subject_content']);
							$pos = strripos($vipINFO[1], '-');
							if($pos === false){
								$time = $vipINFO[1];
							}
							else{
								$getTimke = explode('-',  $vipINFO[1]);
								$time = rand($getTimke[0],$getTimke[1]);
							}
							
							$vipNewParam = [
								'account_id'=> $this->st32to3($_SESSION['steamid32']),
								'sid'=>$server['server_vip_id']
							];
							$vipNew = $this->db->queryAll($dataMysql[0], $dataMysql[1], $dataMysql[2], "SELECT * FROM {$dataMysql[3]}users WHERE account_id = :account_id AND sid = :sid", $vipNewParam);
							if( empty( $vipNew[0]['account_id'] ) )
							{
								$insertparams = [
									'account_id'	=>$this->st32to3($_SESSION['steamid32']),
									'name'			=>$this->Auth->user_auth[0]['name'],
									'lastvisit'		=>time(),
									'sid'			=>$server['server_vip_id'],
									'group'			=>$vipINFO[0],
									'expires'		=>$this->GetTimeVip($time)
								];
								$this->db->query($dataMysql[0], $dataMysql[1], $dataMysql[2], "INSERT INTO {$dataMysql[3]}users VALUES (:account_id, :name, :lastvisit, :sid, :group, :expires)", $insertparams);
								$this->RconComand($server['ip'], $server['rcon'], self::VIP_REFRESH);
								$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
								$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
											  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
													  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Privilege').': '.$subjectInfo[0]['subject_name'].'<br>
													  	Срок: до '.date('d-m-Y H:i:s',$this->GetTimeVip($time)).'<br>
													  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
													  	IP: '.$server['ip'].'
											  	</span></div>';
								$return = array(
							                'style' 	=> 'transparent',
							                'html' 		=> $html
							            );
							}
							else if($vipINFO[0] == $vipNew[0]['group']){
								if( empty( $vipNew[0]['expires'] ) )
								{
									$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Error').'</div>
											  	<div><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
											  		'.$this->Translate->get_translate_module_phrase('module_page_open_case','_HavePrivelege').$this->Translate->get_translate_module_phrase('module_page_open_case','_AmountCourse').$subjectInfo[0]['subject_sale'].'
											  	</span></div>
											  	<div class="bonuses-but" style="position: absolute;top: initial; bottom: 40px;right: 128px;"><a onclick="to_sale_wins('.$winsInfo[0]['id'].')">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Sell').' '.$this->Translate->get_translate_module_phrase('module_page_open_case','_AmountCourse').$subjectInfo[0]['subject_sale'].'</a></div>';
									$return = array(
							                'style' 	=> $baground,
							                'html' 		=> $html
							            );
									exit(json_encode($return));
								}
								else
								{
									$insertparams = [
											'account_id'	=>	$this->st32to3($_SESSION['steamid32']),
											'lastvisit'		=>	time(),
											'sid'			=>	$server['server_vip_id'],
											'expires'		=>	$time
										];
									$this->db->query($dataMysql[0], $dataMysql[1], $dataMysql[2], "UPDATE {$dataMysql[3]}users SET lastvisit = :lastvisit, expires = expires + :expires WHERE account_id = :account_id AND sid = :sid", $insertparams);
									$this->RconComand($server['ip'], $server['rcon'], self::VIP_REFRESH);
									$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
									$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
										  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
												  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Privilege').': '.$subjectInfo[0]['subject_name'].'<br>
												  	Срок: до '.date('d-m-Y H:i:s',$this->GetTimeVip($time)).'<br>
												  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
												  	IP: '.$server['ip'].'
										  	</span></div>';
									$return = array(
								                'style' 	=> 'transparent',
								                'html' 		=> $html
								            );
								}
							}
							else
							{
	
								if( !empty( $post['w_confirm'] ) )
								{
									$insertparams = [
										'account_id'	=>$this->st32to3($_SESSION['steamid32']),
										'lastvisit'		=>time(),
										'sid'			=>$server['server_vip_id'],
										'group'			=>$vipINFO[0],
										'expires'		=>$this->GetTimeVip($time)
									];

									$this->db->query($dataMysql[0], $dataMysql[1], $dataMysql[2], "UPDATE {$dataMysql[3]}users SET lastvisit = :lastvisit, expires = :expires, `group`=:group WHERE account_id = :account_id AND sid = :sid", $insertparams);
									$this->RconComand($server['ip'], $server['rcon'], self::VIP_REFRESH);
									$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
									$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
											  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
													  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Privilege').': '.$subjectInfo[0]['subject_name'].'<br>
													  	Срок: до '.date('d-m-Y H:i:s',$this->GetTimeVip($time)).'<br>
													  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
													  	IP: '.$server['ip'].'11
											  	</span></div>';
									$return = array(
							                'style'		=> 'transparent',
							                'html' 		=> $html
							            );
								}
								else
								{
								
									$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_VoteHavePrivelege').'</div>
										<br><br>
										<div class="bonuses-but"><a onclick="Swal.close();window.location.reload();">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_No').'</a>
										<a onclick=pick_up_wins_accept("'.base64_encode(json_encode(['w_confirm'=> 'true','up'=> strip_tags($post['up']),'sid'=> $server_id])).'")>'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Yes').'</a></div>';
									$return = array(
												'allow'		=> 'true',
								                'style'		=> 'transparent',
								                'html' 		=> $html
							            );
									exit(json_encode($return));
								}
							}
						}else {
							$return = array(
				                'style' 	=> 'transparent',
				                'html' 		=> 'Error'
				            );
						}
					break;
					/*************************************
					 *----MATERIAL ADMIN | SourceBans----*
					 *************************************/
					case 4:
						$dataMysql = explode(';', $server['server_sb']);
						if(!empty($dataMysql))
						{
							$sbINFO = explode(':', $subjectInfo[0]['subject_content']);
							$pos = strripos($sbINFO[1], '-');
							if($pos === false){
								$time = $sbINFO[1];
							}
							else{
								$getTimke = explode('-',  $sbINFO[1]);
								$time = rand($getTimke[0],$getTimke[1]);
							}
							$Param = ['authid'=> $_SESSION['steamid32']];
							$userSb = $this->db->queryAll($dataMysql[0], $dataMysql[1], $dataMysql[2], "SELECT * FROM {$dataMysql[3]}admins WHERE authid = :authid", $Param);

							$sb_group_param = [ 'name'=> $sbINFO[0] ];
							$sb_group = $this->db->queryAll($dataMysql[0], $dataMysql[1], $dataMysql[2], "SELECT * FROM {$dataMysql[3]}srvgroups WHERE name = :name", $sb_group_param);

							$_Server_IP = explode(':', $server['ip']);
							$sb_server_param = [ 'ip'=> $_Server_IP[0], 'port'=> $_Server_IP[1] ];
							$sb_server = $this->db->queryAll($dataMysql[0], $dataMysql[1], $dataMysql[2], "SELECT * FROM {$dataMysql[3]}servers WHERE ip = :ip AND port = :port", $sb_server_param);

							if(empty($userSb[0]['authid']))
							{	
								$userLoginSB = $this->Auth->user_auth[0]['name'];
								if (strlen($userLoginSB) != strlen(utf8_decode($userLoginSB)))
								{
			    					$userLoginSB = '';
			    				}
			    				if(empty($userLoginSB))
			    				{
			    					$userLoginSB = $this->random_str(8);
			    				}
								$userPassSB = $this->random_str(15);
								$userGenPass = $this->sbpasswd($userPassSB);
								$insAdminParams = [
									'user'		=>$userLoginSB,
									'authid'	=>$_SESSION['steamid32'],
									'password'	=>$userGenPass,
									'email'		=>'',
									'srv_group' =>$sb_group[0]['name'],
									'expired'	=>$this->GetTimeVip($time)
								];
								$this->db->query($dataMysql[0], $dataMysql[1], $dataMysql[2], "INSERT INTO {$dataMysql[3]}admins(aid, user, authid, password, gid, email, validate, extraflags, immunity, srv_group, srv_flags, srv_password, lastvisit, expired, skype, comment, vk, support) VALUES (NULL,:user,:authid,:password,-1,:email,NULL,0,50,:srv_group,NULL,NULL,NULL,:expired,NULL, NULL, NULL, 0)",$insAdminParams);

								$groupsParams = [
										'admin_id'		=> intval($this->db->lastInsertId($dataMysql[0], $dataMysql[1], $dataMysql[2])),
										'group_id'		=> $sb_group[0]['id'],
										'srv_group_id'	=> -1,
										'server_id'		=> $sb_server[0]['sid']
									];
								$this->db->query($dataMysql[0], $dataMysql[1], $dataMysql[2], "INSERT INTO {$dataMysql[3]}admins_servers_groups VALUES(:admin_id, :group_id, :srv_group_id, :server_id)", $groupsParams);

								$this->RconComand($server['ip'], $server['rcon'], self::ADM_REFRESH);
								$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
									$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
												  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
														  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Privilege').': '.$subjectInfo[0]['subject_name'].'<br>
														  	Срок: до '.date('d-m-Y H:i:s',$this->GetTimeVip($time)).'<br>
														  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
														  	IP: '.$server['ip'].'
												  	</span></div>';
									$return = array(
								                'style' 	=> 'transparent',
								                'html' 		=> $html
								            );
							}
							else
							{	
								$sb_group_param_admin =['admin_id'=> $userSb[0]['aid']];
								$sb_group_admin = $this->db->queryAll($dataMysql[0], $dataMysql[1], $dataMysql[2], "SELECT * FROM {$dataMysql[3]}admins_servers_groups WHERE admin_id =:admin_id",$sb_group_param_admin);
								if($userSb[0]['expired'] == 0 && $sb_group_admin[0]['server_id'] == $sb_server[0]['sid'])
								{
									$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Error').'</div>
											  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
													  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_HavePrivelege').$this->Translate->get_translate_module_phrase('module_page_open_case','_AmountCourse').$subjectInfo[0]['subject_sale'].'
											  	</span></div>
											  	<div class="bonuses-but" style="position: absolute;top: initial;bottom: 40px;right: 128px;"><a onclick="to_sale_wins('.$winsInfo[0]['id'].')">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Sell').' '.$this->Translate->get_translate_module_phrase('module_page_open_case','_AmountCourse').$subjectInfo[0]['subject_sale'].' ₽</a></div>';
									$return = array(
							                'style' 	=> 'transparent',
							                'html' 		=> $html
							            );
									exit(json_encode($return));
								}
								else if($sb_group_admin[0]['server_id'] != $sb_server[0]['sid'])
								{
									$groupsParams = [
										'admin_id'		=> $userSb[0]['aid'],
										'group_id'		=> $sb_group[0]['id'],
										'srv_group_id'	=> -1,
										'server_id'		=> $sb_server[0]['sid']
									];
									$this->db->query($dataMysql[0], $dataMysql[1], $dataMysql[2], "INSERT INTO {$dataMysql[3]}admins_servers_groups VALUES(:admin_id, :group_id, :srv_group_id, :server_id)", $groupsParams);
									$this->RconComand($server['ip'], $server['rcon'], self::ADM_REFRESH);
									$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
										$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
												  <div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
														  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Privilege').': '.$subjectInfo[0]['subject_name'].'<br>
														  	Срок: до '.date('d-m-Y H:i:s',$this->GetTimeVip($time)).'<br>
														  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
														  	IP: '.$server['ip'].'
												  	</span></div>';
									$return = array(
								                'style' 	=> 'transparent',
								                'html' 		=> $html
								            );
								}
								else
								{
									$insertparams = [
										'authid'		=>	$_SESSION['steamid32'],
										'lastvisit'		=>	time(),
										'expired'		=>	$time
									];
									$this->db->query($dataMysql[0], $dataMysql[1], $dataMysql[2], "UPDATE {$dataMysql[3]}admins SET expired = expired + :expired, lastvisit = :lastvisit WHERE authid = :authid", $insertparams);
									$this->RconComand($server['ip'], $server['rcon'], self::ADM_REFRESH);
									$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
									$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
												  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
														  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Privilege').': '.$subjectInfo[0]['subject_name'].'<br>
														  	Срок: до '.date('d-m-Y H:i:s',$this->GetTimeVip($time)).'<br>
														  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
														  	IP: '.$server['ip'].'
												  	</span></div>';
									$return = array(
								                'style' 	=> 'transparent',
								                'html' 		=> $html
								            );
								}
							}
						}
					break;
					/*************************************
					 *---------SHOP CORE FORZDARK--------*
					 *************************************/
					case 5://CREDITS
						$this->RconComand($server['ip'], $server['rcon'], self::CREDITS_GIVE.' "'.$_SESSION['steamid32'].'" "'.$subjectInfo[0]['subject_content'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
						$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
								  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
										  	Вам выдано '.$subjectInfo[0]['subject_content'].' кредитов!<br>
										  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
										  	IP - '.$server['ip'].'!
								  	</span></div>';
						$return = array(
				                'style' 	=> 'transparent',
				                'html' 		=> $html
				            );
					break;
					/*************************************
					 *----------WCS VIP STRAIKER---------*
					 *************************************/
					case 6:
						$vipINFO = explode(':', $subjectInfo[0]['subject_content']);
							$pos = strripos($vipINFO[1], '-');
							if($pos === false){
								$time = $vipINFO[1];
							}
							else{
								$getTimke = explode('-',  $vipINFO[1]);
								$time = rand($getTimke[0],$getTimke[1]);
							}
						$this->RconComand($server['ip'], $server['rcon'], self::WCS_VIP.' "'.$_SESSION['steamid32'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$vipINFO[0].'" '.round(($time/60)));
						$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
						$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
									  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
											  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Privilege').': '.$subjectInfo[0]['subject_name'].'<br>
											  	Срок: до '.date('d-m-Y H:i:s',$this->GetTimeVip($time)).'<br>
											  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
											  	IP: '.$server['ip'].'
									  	</span></div>';
						$return = array(
					                'style' 	=> 'transparent',
					                'html' 		=> $html
					            );
					break;
					/*************************************
					 *----------WCS GOLD STRAIKER--------*
					 *************************************/
					case 7:
						$this->RconComand($server['ip'], $server['rcon'], self::WCS_GOLD.' + '.$subjectInfo[0]['subject_content'].' "'.$_SESSION['steamid32'].'"');
						$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
						$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
								  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
										  	Вам выдано '.$subjectInfo[0]['subject_content'].' кредитов голд!<br>
										  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
										  	IP - '.$server['ip'].'!
								  	</span></div>';
						$return = array(
				                'style' 	=> 'transparent',
				                'html' 		=> $html
				            );
					break;
					/*************************************
					 *----------WCS LVL STRAIKER---------*
					 *************************************/
					case 8:
						$this->RconComand($server['ip'], $server['rcon'], self::WCS_LVL.' + '.$subjectInfo[0]['subject_content'].' "'.$_SESSION['steamid32'].'"');
						$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
						$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
								  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
										  	Вам выдано '.$subjectInfo[0]['subject_content'].' уровней!<br>
										  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
										  	IP - '.$server['ip'].'!
								  	</span></div>';
						$return = array(
				                'style' 	=> 'transparent',
				                'html' 		=> $html
				            );
					break;
					/*************************************
					 *----------WCS RACE STRAIKER--------*
					 *************************************/
					case 9:
						$vipINFO = explode(':', $subjectInfo[0]['subject_content']);
							$pos = strripos($vipINFO[1], '-');
							if($pos === false){
								$time = $vipINFO[1];
							}
							else{
								$getTimke = explode('-',  $vipINFO[1]);
								$time = rand($getTimke[0],$getTimke[1]);
							}
						$this->RconComand($server['ip'], $server['rcon'], self::WCS_RACE.' "'.$_SESSION['steamid32'].'" "'.$vipINFO[0].'" '.$time);
						$this->RconComand($server['ip'], $server['rcon'], self::WINS.' "'.$subjectInfo[0]['subject_name'].'" "'.$this->Auth->user_auth[0]['name'].'" "'.$casePrice['case_name'].'"');
						$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
									  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
											  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Privilege').': '.$subjectInfo[0]['subject_name'].'<br>
											  	Срок: до '.date('d-m-Y H:i:s',$this->GetTimeVip($time)).'<br>
											  	'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Server').':'.$server['name'].'<br>
											  	IP: '.$server['ip'].'
									  	</span></div>';
						$return = array(
					                'style' 	=> 'transparent',
					                'html' 		=> $html
					            );
					break;
					/*************************************
					 *----------******ERROR******--------*
					 *************************************/
					default:
						$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_Error').' #C3</div>
								  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">
										  	#C3: '.$this->Translate->get_translate_module_phrase('module_page_open_case','_ErrorAdminSend').'
								  	</span></div>';
						$return = array(
				                'style' 	=> 'transparent',
				                'html' 		=> $html
				            );
						exit(json_encode($return));
					break;
				}
			$data = ['steam_id' => $_SESSION['steamid32'], 'id'=>strip_tags($post['up'])];
			$this->db->query('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'],'UPDATE cases_wins SET up =1 WHERE id=:id AND steam_id=:steam_id',$data);
			exit(json_encode($return));
		}

	}

	public function winSubject($id){
		if( empty($_SESSION['steamid32']) || IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit;}
		$winsInfo = $this->getWinsData(strip_tags($id));
		if(empty($winsInfo))return;
		$subjectInfo = $this->getSubjectData($winsInfo[0]['subject_id']);
		if(empty($subjectInfo))return;
		if($subjectInfo[0]['subject_type'] != 1 && !empty($winsInfo[0]['up']) && empty($winsInfo[0]['sell'])){
			switch ($subjectInfo[0]['subject_class']) {
				case 'gold':		$color = '725a39';break;
				case 'red':			$color = 'ec8492';break;
				case 'pink':		$color = 'df0117';break;
				case 'purple':		$color = 'c555ff';break;
				case 'blue':		$color = '5655d3';break;
				case 'turquoise':	$color = '2afdf4';break;
				case 'grey':		$color = '3e3e3e';break;
				default:			$color = 'cacaca';break;
			}
			switch ($subjectInfo[0]['subject_type']) {
				case 2:
					$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
							<div style="margin-top:40px;">
								<span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">'.$subjectInfo[0]['subject_content'].'</span>
							</div>';
					$return = array(
			                'style' 	=> 'transparent',
			                'html' 		=> $html
			            );
					exit(json_encode($return));
					break;
				default:
					$html = '<div class="bonuses-title">'.$this->Translate->get_translate_module_phrase('module_page_open_case','_YourWin').'</div>
								  	<div style="margin-top:40px;"><span style="color:'.$color.';text-overflow: ellipsis;font-size: 20px;font-weight: 900;">Вам был выдан данный выигрыш</span></div>';
					$return = array(
			                'style' 	=> 'transparent',
			                'html' 		=> $html
			            );
					exit(json_encode($return));
					break;
			}
		}
	}	

	public function liveUpload($id){
		if( empty($_SESSION['steamid32']) || IN_LR != true ) { header('Location: ' . $this->General->arr_general['site'] ); exit;}
		$subjects = $this->getSubjectData($id);
		if(empty($subjects))return;
		$case = $this->getPriceCase($subjects[0]['case_id']);
		if(empty($case))return;
		$data = ['auth' => $_SESSION['steamid32']];
		$user =$this->db->queryAll('lk', $this->db->db_data['lk'][0]['USER_ID'], $this->db->db_data['lk'][0]['DB_num'],'SELECT name FROM lk WHERE auth=:auth',$data);
		$data = [ 'case_id'=>$case['id'], 'case_name'=>$case['case_name'], 'subject_name'=>$subjects[0]['subject_name'], 'user_name'=>$user[0]['name'], 'steam_id'=>$_SESSION['steamid32'], 'subject_img'=>$subjects[0]['subject_img'], 'case_img'=>$case['case_img'], 'live_style'=>$subjects[0]['subject_class']];
		$this->db->query('lk', $this->db->db_data['lk'][0]['USER_ID'], $this->db->db_data['lk'][0]['DB_num'],'INSERT INTO cases_live(case_id, case_name, subject_name, user_name, steam_id, subject_img, case_img, live_style) VALUES (:case_id, :case_name, :subject_name, :user_name, :steam_id, :subject_img, :case_img, :live_style)',$data);
		$this->db->query('lk', $this->db->db_data['lk'][0]['USER_ID'], $this->db->db_data['lk'][0]['DB_num'],'DELETE FROM cases_live WHERE id NOT IN (SELECT id FROM (SELECT id FROM cases_live ORDER BY id DESC LIMIT 15) x)');
	}

	public function liveLoad(){
		$result = $subjects =  $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_live ORDER BY id DESC LIMIT 15");
		$lifelines = array();
        foreach ($result as $entry) {
            array_push($lifelines, array(
            	'liveid'	=>$entry['id'],
                'id' 		=> $entry['case_id'],
                'cname' 	=> $entry['case_name'],
                'sname' 	=> $entry['subject_name'],
                'uname'		=> $entry['user_name'],
                'simg' 		=> $this->ImgLoad($entry['subject_img']),
                'cimg' 		=> $this->ImgLoad($entry['case_img']),
                'style' 	=> $entry['live_style'],
            ));
        }
        exit(json_encode(array_reverse($lifelines)));
	}

	public function Get_Server_Info( $id )
	{
		$param = ['id'=>$id ];
		$server = $this->db->queryAll( 'Core', 0, 0,'SELECT * FROM lvl_web_servers WHERE id = :id', $param );
		return $server[0];
	}

	protected function WM($summ){
		$ita = explode('.', $summ);
		if(COUNT($ita) == 1){
			$summa = $ita[0].'.00';
		}else{
			$summa = $summ;
		}
		return $summa;
	}

	protected function numberOfDecimals($value){
	    if ((int)$value == $value)return 0;
	    else if (! is_numeric($value))return false;
	    return strlen($value) - strrpos($value, '.') - 1;
	}

	protected function roulette($items){
		  $sumOfPercents = 0;
		  foreach($items as $itemsPercent){
		    $sumOfPercents += $itemsPercent;
		  }

		  $decimals = $this->numberOfDecimals($sumOfPercents);
		  $multiplier = 1;
		  for ($i=0; $i < $decimals; $i++){ 
		    $multiplier *= 10;
		  }

		  $sumOfPercents *= $multiplier;
		  $rand = rand(1, $sumOfPercents);
		  $rangeStart = 1;
		  foreach($items as $itemKey => $itemsPercent){
		    $rangeFinish = $rangeStart + ($itemsPercent * $multiplier);
		    if($rand >= $rangeStart && $rand <= $rangeFinish){
		      return $itemKey;
		    }
			$rangeStart = $rangeFinish + 1;
		}
	}

	protected function RconComand($ip, $rcons, $comands){
		$ip = explode(':', $ip);
		$rcon = new Rcon($ip[0], $ip[1]);
		if($rcon->Connect()){
		    $rcon->RconPass($rcons);
		    $rcon->Command($comands);
		    $rcon->Disconnect();
		}
	}

	public function bg($bg){
		$array = [
			'gold'		=>1,
			'red'		=>2,
			'pink'		=>3,
			'purple'	=>4,
			'blue'		=>5,
			'turquoise'	=>6,
			'grey'		=>7
		];
		return $array[$bg];
	}
	public function bgReturn($bg){
		$array = [
			1	=>'gold',
			2	=>'red',
			3	=>'pink',
			4	=>'purple',
			5	=>'blue',
			6	=>'turquoise',
			7	=>'grey'
		];
		return $array[$bg];
	}

	public function bg2($bg){
		$array = [
			'gold'		=>$this->Translate->get_translate_module_phrase('module_page_open_case','_Gold'),
			'red'		=>$this->Translate->get_translate_module_phrase('module_page_open_case','_Red'),
			'pink'		=>$this->Translate->get_translate_module_phrase('module_page_open_case','_Pink'),
			'purple'	=>$this->Translate->get_translate_module_phrase('module_page_open_case','_Purple'),
			'blue'		=>$this->Translate->get_translate_module_phrase('module_page_open_case','_Blue'),
			'turquoise'	=>$this->Translate->get_translate_module_phrase('module_page_open_case','_Turquoise'),
			'grey'		=>$this->Translate->get_translate_module_phrase('module_page_open_case','_Grey')
		];
		return $array[$bg];
	}

	protected function GetTimeVip($time){
			if(empty($time)){
				$time = '0';
				return $time;
			}
			else return time() + $time;
	}

	protected function random_str( $num = 30 ) {
		return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $num);
	}

	protected function sbpasswd($password, $salt='SourceBans'){
		return sha1(sha1($salt . $password));
	}

	protected function DiscordMsg($case, $subject){
			$ds = $this->db->queryAll('cases', $this->db->db_data['cases'][0]['USER_ID'], $this->db->db_data['cases'][0]['DB_num'], "SELECT * FROM cases_discord");
			switch ($subject[0]['subject_class']) {
			case 'gold':		$color = '725a39';break;
			case 'red':			$color = 'ec8492';break;
			case 'pink':		$color = 'df0117';break;
			case 'purple':		$color = 'c555ff';break;
			case 'blue':		$color = '5655d3';break;
			case 'turquoise':	$color = '2afdf4';break;
			case 'grey':		$color = '3e3e3e';break;
			default:			$color = 'cacaca';break;
		}
		if($case['case_type'] == 1)
			$price = " за ".$case['case_price']." руб.";
		else $price = " бесплатно";
		if(!empty($ds[0]['auth'])){
			if($this->Auth->user_auth[0]['name'])
			{
				$json = json_encode([
    				"username" 		=> $this->Auth->user_auth[0]['name'],
    				"avatar_url" 	=> "http:".get_url(2).$this->General->getAvatar( con_steam32to64( $_SESSION['steamid32'] ), 2 ),
					"file"=>"content",
					"embeds" => 
					[
				        [	
				        	"color"		=> hexdec( $color ),
				        	"title" 	=> "Открыть кейс ".$case['case_name'].$price,
				        	"description" => $subject[0]['subject_desc'],
				            "type" 		=> "content",
				            "url" 		=> "http:".get_url(2)."?page=cases&case=".$case['id'],
				            "image"		=>
				            [
				                "url" => "http:".get_url(2).$subject[0]['subject_img'],
				            ],
				            "thumbnail" =>
				            [
				                "url" => "http:".get_url(2).$case['case_img']
				            ],
				            "footer"=>
				            [
						        "text"		=>'LR OPEN CASE by SAPSAN 隼',
						        "icon_url"	=> "http:".get_url(2).$this->ImgLoad('0')
						     ],
				            "fields" =>
				            [
				               	[
				                    "name" 		=> "Выигрыш",
				                    "value" 	=> $subject[0]['subject_name'],
				                    "inline" 	=> true
				                ],
				                [
				                    "name" 		=> "Кейс",
				                    "value" 	=> $case['case_name'],
				                    "inline" 	=> true
				                ]
				            ]
				        ]
				    ]
				]);
				$cl = curl_init($ds[0]['url']);
				curl_setopt($cl, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
				curl_setopt($cl, CURLOPT_POST, 1);
				curl_setopt($cl, CURLOPT_POSTFIELDS, $json);
				curl_exec($cl);
			}
		}
	}

	public function st32to3($steamid32)
    {
        if (preg_match('/^STEAM_[0-1]\:(.*)\:(.*)$/', $steamid32, $res)) {
 			return $res[2] * 2 + $res[1];
        }
        return false;
    }

    protected function message($text,$status){
		exit (trim(json_encode(array(
				'text' => $text,
				'status' => $status,
			))));
	}

	public function ImgLoad($link){
		if(!file_exists($link))
			$link = 'app/modules/module_page_open_case/assets/img/no_case_img.png';
		return $link;
	}
}