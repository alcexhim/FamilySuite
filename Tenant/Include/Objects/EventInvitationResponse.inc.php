<?php
	namespace FamilySuite\Objects;
	
	class EventInvitationResponse
	{
		public $EnteredName;
		public $Status;
		public $GuestCount;
		
		public $MealPlan;
		public $MealPlanComments;
		
		public $Message;
		public $GuestType;
		public $InviteSource;
		
		public static function GetByAssoc($values)
		{
			$item = new EventInvitationResponse();
			$item->EnteredName = $values["resp_EnteredName"];
			$item->Status = $values["resp_Status"] == 1 ? true : false;
			$item->GuestCount = $values["resp_GuestCount"];
			
			$item->MealPlan = EventMealPlan::GetByID($values["resp_MealPlanID"]);
			$item->MealPlanComments = $values["resp_MealPlanComments"];
			
			$item->Message = $values["resp_Message"];
			$item->GuesetType = $values["guesttype_Title"];
			$item->InviteSource = $values["invitesource_Title"];
			return $item;
		}
	}
?>