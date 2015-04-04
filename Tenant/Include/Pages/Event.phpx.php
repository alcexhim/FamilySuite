<?php
	namespace FamilySuite\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Phast\Data\DataSystem;
	use Phast\System;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColum;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use PDO;
			
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
				
				if (isset($_POST["resp_Status"]))
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
	class EventDetailPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$page = $e->RenderingPage;
			$tabPage = $page->GetControlByID("tbsTabs")->GetTabByID("pageResponses");
			$lvInvitees = $tabPage->GetControlByID("lvInvitees");

			// we're going to actually submit data now
			$pdo = DataSystem::GetPDO();
			$query = "SELECT *, fs_EventGuestTypes.guesttype_Title, fs_EventInviteSources.invitesource_Title, MealPlans.Title AS mealplan_Title FROM Responses, fs_EventGuestTypes, fs_EventInviteSources, MealPlans WHERE Responses.resp_GuestTypeID = fs_EventGuestTypes.guesttype_ID AND Responses.resp_InviteSourceID = fs_EventInviteSources.invitesource_ID AND Responses.resp_MealOptionID = MealPlans.ID";
			$statement = $pdo->prepare($query);
			$statement->execute();
			$count = $statement->rowCount();
			
			$items = array();
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcGuest", $values["resp_EnteredName"]),
					new ListViewItemColumn("lvcAttending", $values["resp_Status"] == 1 ? "Yes" : "No"),
					new ListViewItemColumn("lvcGuestCount", $values["resp_GuestCount"]),
					new ListViewItemColumn("lvcMealPlan", $values["mealplan_Title"] . ($values["resp_MealPlanComments"] != "" ? ("<br /><em>" . $values["resp_MealPlanComments"] . "</em>") : "")),
					new ListViewItemColumn("lvcGuestType", $values["guesttype_Title"]),
					new ListViewItemColumn("lvcInviteSource", $values["invitesource_Title"])
				));
			}
			
			$lvInvitees->Items = $items;
		}
	}
?>