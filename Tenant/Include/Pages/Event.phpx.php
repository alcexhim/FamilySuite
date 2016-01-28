<?php
	namespace FamilySuite\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Phast\Data\DataSystem;
	use Phast\System;

	use Phast\HTMLControls\Anchor;
	use Phast\HTMLControl;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColum;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use PDO;
	
	use FamilySuite\Objects\Event;
	use FamilySuite\Objects\User;
	
	class EventPrintPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			header("Content-Type: text/csv; charset=utf-8");
			header("Content-Disposition: attachment; filename=addresses.csv");
			
			$pdo = DataSystem::GetPDO();
			$statement = $pdo->prepare("SELECT * FROM fs_Addresses");
			$result = $statement->execute();
			
			if ($result === false)
			{
				
			}
			
			$count = $statement->rowCount();
			$ret = "";
			
			$columnSeparator = ",";
			$lineSeparator = "\n";
			
			$ret .= "\"Name\"" . $columnSeparator .
			"\"StreetAddress\"" . $columnSeparator .
			"\"City\"" . $columnSeparator .
			"\"State\"" . $columnSeparator .
			"\"PostalCode\"" . $columnSeparator .
			"\"FormattedAddressLine2\"" . $columnSeparator .
			"\"CountryName\"" . $lineSeparator;
			
			$ignoreEmptyAddresses = true;
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				
				if ($ignoreEmptyAddresses && ($values["StreetAddress"] == "" || $values["City"] == "" || $values["State"] == "" || $values["PostalCode"] == ""))
				{
					continue;
				}
			
				$country = "";
				if ($values["CountryID"] != 1)
				{
					$query = "SELECT * FROM fs_Countries WHERE ID = " . $values["CountryID"];
					$statement1 = $pdo->prepare($query);
					$result1 = $statement1->execute();
					$values1 = $statement1->fetch(PDO::FETCH_ASSOC);
					$country = $values1["Title"];
				}
				
				$formattedAddressLine2 = $values["City"] . (($values["City"] == "" || $values["State"] == "") ? "" : ", ") . $values["State"];
				
				$ret .= "\"" . $values["Name"] . "\"" . $columnSeparator;
				$ret .= "\"" . $values["StreetAddress"] . "\"" . $columnSeparator;
				$ret .= "\"" . $values["City"] . "\"" . $columnSeparator;
				$ret .= "\"" . $values["State"] . "\"" . $columnSeparator;
				$ret .= "\"" . $values["PostalCode"] . "\"" . $columnSeparator;
				$ret .= "\"" . $formattedAddressLine2 . "\"" . $columnSeparator;
				$ret .= "\"" . $country . "\"";
				$ret .= $lineSeparator;
			}
			echo ($ret);
			exit();
		}
	}
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
					$query = "INSERT INTO fs_EventInvitationResponses (resp_EnteredName, resp_EnteredEmailAddress, resp_Message, resp_AssociatedNameID, resp_MealPlanID, resp_MealPlanComments, resp_GuestCount, resp_Status) VALUES (" .
						":resp_EnteredName, :resp_EnteredEmailAddress, :resp_Message, :resp_AssociatedNameID, :resp_MealPlanID, :resp_MealPlanComments, :resp_GuestCount, :resp_Status)";
					$statement = $pdo->prepare($query);
					$statement->execute(array
					(
						":resp_EnteredName" => $_POST["resp_EnteredName"],
						":resp_EnteredEmailAddress" => $_POST["resp_EnteredEmailAddress"],
						":resp_Message" => $_POST["resp_Message"],
						":resp_AssociatedNameID" => null,
						":resp_MealPlanID" => $_POST["guest_MealPlanID"],
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
			
			$beginDate = $event->BeginTimestamp->format("l, F j, Y");
			$beginTime = $event->BeginTimestamp->format("g:i A");
			$endTime = $event->EndTimestamp->format("g:i A");
			
			$fvDetails->GetItemByID("lblWhen")->Value = $beginDate . "<br />" . $beginTime . " - " . $endTime;
			$fvDetails->GetItemByID("lblWhere")->Value = $event->Location->ToHTML();
			
			$gMapsUrl = "https://www.google.com/maps/place/";
			// $gMapsUrl .= $event->Location->Title . ", ";
			$gMapsUrl .= $event->Location->StreetAddress . ", " . $event->Location->City . ", " . $event->Location->State . " " . $event->Location->PostalCode;
			$gMapsUrl = str_replace(" ", "+", $gMapsUrl);

			$cmdGetDirections = $tabPage->GetControlByID("cmdGetDirections");
			$cmdGetDirections->TargetURL = $gMapsUrl;
			
			$litRelatedEvents = $tabPage->GetControlByID("litRelatedEvents");
			$hdrRelatedEvents = $tabPage->GetControlByID("hdrRelatedEvents");
			
			$events = $event->GetRelatedEvents();
			
			if (count($events) > 0)
			{
				$ul = new HTMLControl("ul");
				foreach ($events as $relatedEvent)
				{
					$li = new HTMLControl("li");
					
					$ctl = new Anchor();
					$ctl->TargetURL = "~/events/" . $relatedEvent->Name;
					$ctl->InnerHTML = $relatedEvent->Title;
					$li->Controls[] = $ctl;
					
					$ul->Controls[] = $li;
				}
				$litRelatedEvents->Controls[] = $ul;
				$hdrRelatedEvents->Visible = true;
			}
			else
			{
				$hdrRelatedEvents->Visible = false;
			}
		}
		private function InitializeInvitationsMeters($page)
		{
			$tabPage = $page->GetControlByID("tbsTabs")->GetTabByID("pageInvitations");
			$mtrInvited = $tabPage->GetControlByID("mtrInvited");
			$mtrAttending = $tabPage->GetControlByID("mtrAttending");
			$mtrDeclining = $tabPage->GetControlByID("mtrDeclining");
			$mtrPending = $tabPage->GetControlByID("mtrPending");

			$event = Event::GetByIDOrName($page->GetPathVariableValue("eventID"));
			$eventResponseCount = $event->CountResponses();
			
			$mtrInvited->MinimumValue = 0;
			$mtrInvited->MaximumValue = $eventResponseCount->Invited;
			$mtrInvited->CurrentValue = $eventResponseCount->Invited;

			$mtrAttending->MinimumValue = 0;
			$mtrAttending->MaximumValue = $eventResponseCount->Invited;
			$mtrAttending->CurrentValue = $eventResponseCount->Attending;
			
			$mtrDeclining->MinimumValue = 0;
			$mtrDeclining->MaximumValue = $eventResponseCount->Invited;
			$mtrDeclining->CurrentValue = $eventResponseCount->Declining;
			
			$mtrPending->MinimumValue = 0;
			$mtrPending->MaximumValue = $eventResponseCount->Invited;
			$mtrPending->CurrentValue = $eventResponseCount->Pending;
		}
		private function InitializeInvitations($page)
		{
			$tabPage = $page->GetControlByID("tbsTabs")->GetTabByID("pageInvitations");
			$lvInvitations = $tabPage->GetControlByID("lvInvitations");
			
			$event = Event::GetByIDOrName($page->GetPathVariableValue("eventID"));
			$invitations = $event->GetInvitations();
			
			$count = count($invitations);
			for ($i = 0; $i < $count; $i++)
			{
				$invitation = $invitations[$i];
				$items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcName", $invitation->Name),
					new ListViewItemColumn("lvcStreetAddress", $invitation->StreetAddress),
					new ListViewItemColumn("lvcCity", $invitation->City),
					new ListViewItemColumn("lvcState", $invitation->State),
					new ListViewItemColumn("lvcPostalCode", $invitation->PostalCode),
					new ListViewItemColumn("lvcCountry", $invitation->Country->Title)
				));
			}
			
			$lvInvitations->Items = $items;
		}
		private function InitializeResponses($page)
		{
			$tabPage = $page->GetControlByID("tbsTabs")->GetTabByID("pageResponses");
			$lvInvitees = $tabPage->GetControlByID("lvInvitees");
			
			// we're going to actually submit data now
			$event = Event::GetByIDOrName($page->GetPathVariableValue("eventID"));
			$responses = $event->GetResponses();
			
			$countChicken = 0;
			$countBeef = 0;
			$countVegetarian = 0;
			
			$count = count($responses);
			for ($i = 0; $i < $count; $i++)
			{
				$response = $responses[$i];
				$items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcGuest", $response->EnteredName),
					new ListViewItemColumn("lvcAttending", $response->Status ? "Yes" : "No"),
					new ListViewItemColumn("lvcGuestCount", $response->GuestCount),
					new ListViewItemColumn("lvcMealPlan", $response->MealPlan->Title . ($response->MealPlanComments != "" ? ("<br /><em>" . $values["resp_MealPlanComments"] . "</em>") : "")),
					new ListViewItemColumn("lvcMessage", $response->Message),
					new ListViewItemColumn("lvcGuestType", $response->GuestType->Title),
					new ListViewItemColumn("lvcInviteSource", $response->InviteSource->Title)
				));
				
				switch ($response->MealPlan->ID)
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
			
			$user = User::GetCurrent();
			if ($user == null)
			{
				$tbsTabs->GetTabByID("pageInvitations")->Visible = false;
				$tbsTabs->GetTabByID("pageResponses")->Visible = false;
				
				if ($tabPageID != "" && $tabPageID != "details")
				{
					System::RedirectToLoginPage();
					return;
				}
			}
			
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