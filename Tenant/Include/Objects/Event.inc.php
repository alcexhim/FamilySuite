<?php
	namespace FamilySuite\Objects;

	use DateTime;
	use PDO;
	
	use Phast\Data\DataSystem;
	
	class Event
	{
		/**
		 * The unique identifier of this Event in the database.
		 * @var int
		 */
		public $ID;
		/**
		 * The name used in URLs to identify this Event.
		 * @var string
		 */
		public $Name;
		/**
		 * The title of this Event displayed in pages around the site.
		 * @var string
		 */
		public $Title;
		/**
		 * A short description of this Event.
		 * @var string
		 */
		public $Description;
		/**
		 * The date and time at which this Event begins.
		 * @var DateTime
		 */
		public $BeginTimestamp;
		/**
		 * The date and time at which this Event ends.
		 * @var DateTime
		 */
		public $EndTimestamp;
		/**
		 * The place at which this Event is located.
		 * @var Location
		 */
		public $Location;
		
		public static function GetByAssoc($values)
		{
			$item = new Event();
			$item->ID = $values["event_ID"];
			$item->Name = $values["event_Name"];
			$item->Title = $values["event_Title"];
			$item->Description = $values["event_Description"];
			$item->BeginTimestamp = new DateTime($values["event_BeginTimestamp"]);
			$item->EndTimestamp = new DateTime($values["event_EndTimestamp"]);
			$item->Location = Location::GetByID($values["event_LocationID"]);
			return $item;
		}
		public static function GetByIDOrName($idOrName)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM Events WHERE event_Name = :event_IDOrName OR event_ID = :event_IDOrName";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":event_IDOrName" => $idOrName
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Event::GetByAssoc($values);
		}
	}
	
?>