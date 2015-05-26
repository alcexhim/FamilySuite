<?php
	namespace FamilySuite\Objects;

	use DateTime;
	use PDO;
	
	use Phast\System;
	use Phast\Data\DataSystem;
	
	class Country
	{
		public $ID;
		public $Title;
		
		public static function GetByAssoc($values)
		{
			$item = new Country();
			$item->ID = $values["country_ID"];
			$item->Title = $values["country_Title"];
			return $item;
		}
		public static function GetByID($id)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Countries WHERE country_ID = :country_ID";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":country_ID" => $id
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Country::GetByAssoc($values);
		}
	}
?>