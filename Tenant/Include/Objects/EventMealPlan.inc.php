<?php
	namespace FamilySuite\Objects;
	
	use Phast\Data\DataSystem;
	use PDO;
	
	class EventMealPlan
	{
		public $ID;
		public $Title;
		public $Description;
		
		public static function GetByID($id)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM fs_EventMealPlans WHERE mealplan_ID = :mealplan_ID";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":mealplan_ID" => $id
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
				
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return EventMealPlan::GetByAssoc($values);
		}
		public static function Get()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM fs_EventMealPlans";
			$statement = $pdo->prepare($query);
			$statement->execute();
			
			$retval = array();
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = EventMealPlan::GetByAssoc($values);
				$retval[] = $item;
			}
			return $retval;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new EventMealPlan();
			$item->ID = $values["mealplan_ID"];
			$item->Title = $values["mealplan_Title"];
			$item->Description = $values["mealplan_Description"];
			return $item;
		}
	}
?>