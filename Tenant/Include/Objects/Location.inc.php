<?php
	namespace FamilySuite\Objects;

	use DateTime;
	use PDO;

	use Phast\System;
	use Phast\Data\DataSystem;
	
	class Location
	{
		public $ID;
		public $Title;
		public $Description;
		public $StreetAddress;
		public $City;
		public $State;
		public $PostalCode;
		/**
		 * 
		 * @var Country
		 */
		public $Country;
		
		public static function GetByAssoc($values)
		{
			$item = new Location();
			$item->ID = $values["location_ID"];
			$item->Title = $values["location_Title"];
			$item->Description = $values["location_Description"];
			$item->StreetAddress = $values["location_StreetAddress"];
			$item->City = $values["location_City"];
			$item->State = $values["location_State"];
			$item->PostalCode = $values["location_PostalCode"];
			$item->Country = Country::GetByID($values["location_CountryID"]);
			return $item;
		}
		public static function GetByID($id)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Locations WHERE location_ID = :location_ID";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":location_ID" => $id
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
				
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Location::GetByAssoc($values);
		}
		
		public function ToHTML()
		{
			return
				$this->Title . "<br />" .
				$this->StreetAddress . "<br />" .
				$this->City . ", " . $this->State . " " . $this->PostalCode . "<br />" .
				$this->Country->Title;
		}
	}
?>