<?php
	namespace FamilySuite\Objects;
	
	class EventInvitation
	{
		public $ID;
		public $Name;
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
			$item = new EventInvitation();
			$item->ID = $values["ID"];
			$item->Name = $values["Name"];
			$item->StreetAddress = $values["StreetAddress"];
			$item->City = $values["City"];
			$item->State = $values["State"];
			$item->PostalCode = $values["PostalCode"];
			$item->Country = Country::GetByID($values["CountryID"]);
			return $item;
		}
	}
?>