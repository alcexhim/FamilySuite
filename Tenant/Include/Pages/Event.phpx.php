<?php
	namespace FamilySuite\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Phast\Data\DataSystem;
	use Phast\System;
	
	class EventPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$page = $e->RenderingPage;
			
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				$page1 = $page->GetControlByID("wizard")->GetPageByID("page1");
				$formview1 = $page1->GetControlByID("formview1");
				
				$formview1->GetItemByID("txtName")->Value = $_POST["resp_EnteredName"];
				$formview1->GetItemByID("txtEmailAddress")->Value = $_POST["resp_EnteredEmailAddress"];
				$formview1->GetItemByID("txtMessage")->Value = $_POST["resp_Message"];
				
				if ($_POST["resp_Status"] != "-1")
				{
					// we're going to actually submit data now
					$pdo = DataSystem::GetPDO();
					$query = "INSERT INTO Responses (resp_EnteredName, resp_EnteredEmailAddress, resp_Message, resp_AssociatedNameID, resp_MealOptionID, resp_MealPlanComments, resp_GuestCount, resp_Status) VALUES (" .
						":resp_EnteredName, :resp_EnteredEmailAddress, :resp_Message, :resp_AssociatedNameID, :resp_MealOptionID, :resp_MealPlanComments, :resp_GuestCount, :resp_Status)";
					$statement = $pdo->prepare($query);
					$statement->execute(array
					(
						":resp_EnteredName" => $_POST["resp_EnteredName"],
						":resp_EnteredEmailAddress" => $_POST["resp_EnteredEmailAddress"],
						":resp_Message" => $_POST["resp_Message"],
						":resp_AssociatedNameID" => null,
						":resp_MealOptionID" => $_POST["guest_MealPlanID"],
						":resp_MealPlanComments" => $_POST["guest_SpecialDietaryNeeds"],
						":resp_GuestCount" => $_POST["resp_GuestCount"],
						":resp_Status" => $_POST["resp_Status"]
					));
					
					if ($_POST["resp_Status"] == "1")
					{
						System::Redirect("~/events/" . $page->GetPathVariableValue("eventID") . "/thankyou1");
					}
					else if ($_POST["resp_Status"] == "0")
					{
						System::Redirect("~/events/" . $page->GetPathVariableValue("eventID") . "/thankyou2");
					}
					return;
				}
				else
				{
					// take us directly to page 2 since we came from the rsvp landing page
					$page->GetControlByID("wizard")->SelectedPageID = "page2";
				}
			}
		}
	}
?>