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
	
	use FamilySuite\Objects\Event;
	
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
		private function InitializeDetails($page)
		{
			$tabPage = $page->GetControlByID("tbsTabs")->GetTabByID("pageDetails");
			
			$event = Event::GetByIDOrName($page->GetPathVariableValue("eventID"));
			if ($event == null)
			{
				echo ("Invalid event");
				die();
			}
			
			$litEventTitle = $page->GetControlByID("litEventTitle");
			$litEventTitle->Value = $event->Title;
			
			$litEventDescription = $tabPage->GetControlByID("litEventDescription");
			$litEventDescription->Value = $event->Description;
			
			$fvDetails = $tabPage->GetControlByID("fvDetails");
			$fvDetails->GetItemByID("lblWhen")->Value = $event->BeginTimestamp->format("l, F j, Y");
		}
		private function InitializeInvitationsMeters($page)
		{
			$tabPage = $page->GetControlByID("tbsTabs")->GetTabByID("pageInvitations");
			$mtrInvited = $tabPage->GetControlByID("mtrInvited");
			$mtrAttending = $tabPage->GetControlByID("mtrAttending");
			$mtrDeclining = $tabPage->GetControlByID("mtrDeclining");
			$mtrPending = $tabPage->GetControlByID("mtrPending");
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT (SELECT COUNT(*) FROM Addresses) AS count_Invited, (SELECT COUNT(*) FROM Responses WHERE resp_Status = 1) AS count_Attending, (SELECT COUNT(*) FROM Responses WHERE resp_Status = 0) AS count_Declining";
			$statement = $pdo->prepare($query);
			$statement->execute();
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			$mtrInvited->MinimumValue = 0;
			$mtrInvited->MaximumValue = $values["count_Invited"];
			$mtrInvited->CurrentValue = $values["count_Invited"];

			$mtrAttending->MinimumValue = 0;
			$mtrAttending->MaximumValue = $values["count_Invited"];
			$mtrAttending->CurrentValue = $values["count_Attending"];
			
			$mtrDeclining->MinimumValue = 0;
			$mtrDeclining->MaximumValue = $values["count_Invited"];
			$mtrDeclining->CurrentValue = $values["count_Declining"];
			
			$mtrPending->MinimumValue = 0;
			$mtrPending->MaximumValue = $values["count_Invited"];
			$mtrPending->CurrentValue = ($values["count_Invited"] - $values["count_Attending"] - $values["count_Declining"]);
		}
		private function InitializeInvitations($page)
		{
			$tabPage = $page->GetControlByID("tbsTabs")->GetTabByID("pageInvitations");
			$lvInvitations = $tabPage->GetControlByID("lvInvitations");
			
			// we're going to actually submit data now
			$pdo = DataSystem::GetPDO();
			$query = "SELECT *, Countries.Title FROM Addresses, Countries WHERE Countries.ID = Addresses.CountryID";
			$statement = $pdo->prepare($query);
			$statement->execute();
			$count = $statement->rowCount();
			
			$items = array();
			
			$countChicken = 0;
			$countBeef = 0;
			$countVegetarian = 0;
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcName", $values["Name"]),
					new ListViewItemColumn("lvcStreetAddress", $values["StreetAddress"]),
					new ListViewItemColumn("lvcCity", $values["City"]),
					new ListViewItemColumn("lvcState", $values["State"]),
					new ListViewItemColumn("lvcPostalCode", $values["PostalCode"]),
					new ListViewItemColumn("lvcCountry", $values["Title"])
				));
			}
			
			$lvInvitations->Items = $items;
		}
		private function InitializeResponses($page)
		{
			$tabPage = $page->GetControlByID("tbsTabs")->GetTabByID("pageResponses");
			$lvInvitees = $tabPage->GetControlByID("lvInvitees");
			
			// we're going to actually submit data now
			$pdo = DataSystem::GetPDO();
			$query = "SELECT *, fs_EventGuestTypes.guesttype_Title, fs_EventInviteSources.invitesource_Title, MealPlans.Title AS mealplan_Title FROM Responses, fs_EventGuestTypes, fs_EventInviteSources, MealPlans WHERE Responses.resp_GuestTypeID = fs_EventGuestTypes.guesttype_ID AND Responses.resp_InviteSourceID = fs_EventInviteSources.invitesource_ID AND Responses.resp_MealOptionID = MealPlans.ID";
			$statement = $pdo->prepare($query);
			$statement->execute();
			$count = $statement->rowCount();
				
			$items = array();
				
			$countChicken = 0;
			$countBeef = 0;
			$countVegetarian = 0;
				
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcGuest", $values["resp_EnteredName"]),
					new ListViewItemColumn("lvcAttending", $values["resp_Status"] == 1 ? "Yes" : "No"),
					new ListViewItemColumn("lvcGuestCount", $values["resp_GuestCount"]),
					new ListViewItemColumn("lvcMealPlan", $values["mealplan_Title"] . ($values["resp_MealPlanComments"] != "" ? ("<br /><em>" . $values["resp_MealPlanComments"] . "</em>") : "")),
					new ListViewItemColumn("lvcMessage", $values["resp_Message"]),
					new ListViewItemColumn("lvcGuestType", $values["guesttype_Title"]),
					new ListViewItemColumn("lvcInviteSource", $values["invitesource_Title"])
				));
			
				switch ($values["resp_MealOptionID"])
				{
					case 1:
					{
						$countChicken++;
						break;
					}
					case 2:
					{
						$countBeef++;
						break;
					}
					case 3:
					{
						$countVegetarian++;
						break;
					}
				}
			}
			
			$lvInvitees->Items = $items;
				
			$countTotal = ($countChicken + $countBeef + $countVegetarian);
				
			$mtrChicken = $tabPage->GetControlByID("mtrChicken");
			$mtrBeef = $tabPage->GetControlByID("mtrBeef");
			$mtrVegetarian = $tabPage->GetControlByID("mtrVegetarian");
				
			$mtrChicken->MaximumValue = $countTotal;
			$mtrChicken->CurrentValue = $countChicken;
				
			$mtrBeef->MaximumValue = $countTotal;
			$mtrBeef->CurrentValue = $countBeef;
				
			$mtrVegetarian->MaximumValue = $countTotal;
			$mtrVegetarian->CurrentValue = $countVegetarian;
		}
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$this->InitializeDetails($e->RenderingPage);
			
			$this->InitializeInvitations($e->RenderingPage);
			$this->InitializeResponses($e->RenderingPage);
			$this->InitializeInvitationsMeters($e->RenderingPage);
			
			$tbsTabs = $e->RenderingPage->GetControlByID("tbsTabs");
			$tabPageID = $e->RenderingPage->GetPathVariableValue("tabPage");
			if ($tabPageID != "")
			{
				switch ($tabPageID)
				{
					case "details":
					{
						$tbsTabs->SelectedTabID = "pageDetails";
						break;
					}
					case "invitations":
					{
						$tbsTabs->SelectedTabID = "pageInvitations";
						break;
					}
					case "responses":
					{
						$tbsTabs->SelectedTabID = "pageResponses";
						break;
					}
					default:
					{
						$tbsTabs->SelectedTabID = "pageDetails";
						break;
					}
				}
			}
		}
	}
?>