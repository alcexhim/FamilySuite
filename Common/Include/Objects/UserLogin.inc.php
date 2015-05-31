<?php
	namespace FamilySuite\Objects;
	
	use DateTime;

	use Phast\Conditionals\ConditionalStatement;
	use Phast\Conditionals\ConditionalComparison;
	
	use Phast\Data\DataSystem;
	use Phast\Data\Table;
	use Phast\Data\TableSelectCriteria;
	use Phast\Data\Record;
				
	class UserLogin
	{
		/**
		 * The internal identifier of this UserLogin.
		 * @var int
		 */
		public $ID;
		
		/**
		 * The User associated with this login.
		 * @var User
		 */
		public $User;
		/**
		 * The token used to identify this login.
		 * @var string
		 */
		public $Token;
		
		/**
		 * The date and time at which the user logged in.
		 * @var DateTime
		 */
		public $BeginTimestamp;
		/**
		 * The date and time at which the user logged out.
		 * @var DateTime
		 */
		public $EndTimestamp;
		
		/**
		 * Comments for this login.
		 * @var string
		 */
		public $Comments;
		
		/**
		 * IP address of the logged-in user.
		 * @var string
		 */
		public $IPAddress;
		
		/**
		 * 
		 * @param Record $record
		 */
		public static function GetByRecord($record)
		{
			$item = new UserLogin();
			$item->ID = $record->GetColumnByName("ID")->Value;
			$item->User = User::GetByID($record->GetColumnByName("UserID")->Value);
			$item->Token = $record->GetColumnByName("Token")->Value;
			$item->BeginTimestamp = new DateTime($record->GetColumnByName("BeginTimestamp")->Value);
			$item->EndTimestamp = new DateTime($record->GetColumnByName("EndTimestamp")->Value);
			$item->Comments = $record->GetColumnByName("Comments")->Value;
			$item->IPAddress = $record->GetColumnByName("IPAddress")->Value;
			
			return $item;
		}
		
		public static function GetByCurrentToken()
		{
			return UserLogin::GetByToken($_SESSION["Authentication.LoginToken"]);
		}
		public static function GetByToken($token)
		{
			$tblUserLogins = Table::Get("UserLogins", "login_");
			
			$criteria = new TableSelectCriteria();
			$criteria->Conditions[] = new ConditionalStatement("Token", ConditionalComparison::Equals, $token);
			
			$result = $tblUserLogins->Select($criteria);
			
			$count = count($result->Records);
			if ($count == 0) return null;
			
			return UserLogin::GetByRecord($result->Records[0]);
		}
	}
?>