<?php
	namespace FamilySuite\Objects;
	
	use PDO;
	use Phast\Data\DataSystem;
	
	use Phast\System;
	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
				
	class User
	{
		/**
		 * The unique ID used to identify this user in the database.
		 * @var int
		 */
		public $ID;
		/**
		 * The name used internally to identify this user.
		 * @var string
		 */
		public $UserName;
		
		/**
		 * The name used around the site to identify this user.
		 * @var string
		 */
		public $DisplayName;
		
		/**
		 * Logs in the current user and returns the login token for this login.
		 * @return UserLogin
		 */
		public function Login()
		{
			$pdo = DataSystem::GetPDO();
			
			$token = RandomStringGenerator::Generate(RandomStringGeneratorCharacterSets::AlphaNumericMixedCase, 256);
			
			$query = "INSERT INTO fs_UserLogins (login_UserID, login_Token, login_BeginTimestamp, login_EndTimestamp, login_Comments, login_IPAddress) VALUES (:login_UserID, :login_Token, NOW(), NULL, :login_Comments, :login_IPAddress)";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":login_UserID" => $this->ID,
				":login_Token" => $token,
				":login_Comments" => $comments,
				":login_IPAddress" => $_SERVER["REMOTE_ADDR"]
			));
			
			if ($result === false) return null;
			
			return UserLogin::GetByToken($token);
		}
		public function Logout()
		{
		}
		
		public static function GetByAssoc($values)
		{
			$item = new User();
			$item->ID = $values["user_ID"];
			$item->UserName = $values["user_UserName"];
			$item->DisplayName = $values["user_DisplayName"];
			return $item;
		}
		
		public static function GetByID($id)
		{
			$pdo = DataSystem::GetPDO();
			
			$query = "SELECT * FROM fs_Users WHERE user_ID = :user_ID";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":user_ID" => $id
			));
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			$item = User::GetByAssoc($values);
			return $item;
		}
		
		public static function GetByCredentials($username, $password)
		{
			$pdo = DataSystem::GetPDO();
			
			$query = "SELECT user_PasswordSalt FROM fs_Users WHERE user_UserName = :user_UserName";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":user_UserName" => $username
			));
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			$passwordSalt = $values["user_PasswordSalt"];
			
			$passwordHash = hash("sha512", $password . $passwordSalt);
			
			$query = "SELECT * FROM fs_Users WHERE user_UserName = :user_UserName AND user_PasswordHash = :user_PasswordHash";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":user_UserName" => $username,
				":user_PasswordHash" => $passwordHash
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			
			return User::GetByAssoc($values);
		}
	}
?>