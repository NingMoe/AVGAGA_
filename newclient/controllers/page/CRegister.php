<?php
/*
 * @ Fallen
 * 註冊頁面
 */

class CRegister {

	const CallBack_Url = '?page=CRegister&toRegister=true';
	const MemberTreaty_Url = '';
	const ServiceTreaty_Url = '';
		
	public function main()	{
		
		// 判斷是否進行註冊
		if (!empty($_GET['toRegister']) && $_GET['toRegister'] == true )		$this->toRegister();
		else if (!empty($_GET['mailConfirm']) )	$this->mailConfirm();
		else if (!empty($_GET['mailSend']) ) $this->sendMessage();
		else $this->show();
	}
	
	// 顯示註冊頁面
	private function show()	{
		
		CRegisterPage::show(CRegister::CallBack_Url, CRegister::MemberTreaty_Url, CRegister::ServiceTreaty_Url);
	}
	
	// 進行註冊
	private function  toRegister()	{
		
		// 抓取資料
		$account = CSecurity::deInput($_POST['account']);			
		$nickName = CSecurity::deInput($_POST['nickName']);
		$pass = sha1($_POST['pass']);
		$time = date('Y/m/d/H/m/s').'/tw';	// 帳號建立時間/生日
		(!empty($_SESSION['platform']))?$platform=$_SESSION['platform']:$platform='';	// 串接平台接取
		
		// 建立帳號
		$dataAr = $this->setAccountData($account, $nickName, $pass, $time, $platform);
		CMain::getDB()->toInsert(CDB::AVGAGA_Member, $dataAr);
				
		// 發送認證信
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('account' => $account));
		CSendMail::registerSend($_POST['account'], $_POST['nickName'], $user['_id']);
	}
	
	// 註冊信件已寄出訊息頁面
	private function sendMessage()	{
		
		if ($_GET['mailSend'] == true)	CRegisterPage::sendSuccess(BaseUrl);
		else CRegisterPage::sendLoss(BaseUrl, $_GET['mailSend']);
	}
	
	// 收到註冊確認信
	private function mailConfirm()	{
			
		// 獲取認證id
		$id = $_GET['mailConfirm'];
		
		// 認證
		$_id = new MongoId($id);
		CMain::getDB()->toFix(CDB::AVGAGA_Member, array('_id' =>$_id), array('mailConfirm' => "1"));
		
		// 確認是否成功
		$user = CMain::getDB()->getCol(CDB::AVGAGA_Member)->findOne(array('_id' =>$_id));
		
		if ($user['mailConfirm'] == 1)	{
			
			header("Refresh:3; url=".BaseUrl);
			CRegisterPage::confirmSuccess();
		}else {
			header("Refresh:3; url=".BaseUrl);
			CRegisterPage::confirmLoss();
		}
	}
	
	// 建立帳號資料
	private function setAccountData($account, $nickName, $pass, $time, $platform)	{
			
		$dataAr = array(
			
				"account"=>$account,
				"pass"=>$pass,
				"nickName"=>$nickName,
				"establishTime"=>$time,
				"loginTime"=>array(),
				"birthDay"=>$time,
				"mailConfirm" => "0",
				"money" => 0,
				"bonus" => 0,
				"collect" => "",
				"fraction" => array(),
				"record" => array(),
				"competence" => "",
				"platform" => $platform,
				"memberPic" => "",
				"messageGood"=>"",
				"messageFraction"=>"",
				"add_monthday"=>"",
				"preViewNum"=>0,
				"isBilling"=>false
		);
		//
		return $dataAr;
	}
	
	
}
?>



