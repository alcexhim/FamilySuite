<?php
	namespace FamilySuite\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	class EventPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$page = $e->RenderingPage;
			
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				$page->GetControlByID("wizard")->SelectedPageID = "page2";
			}
			
			$page1 = $page->GetControlByID("wizard")->GetPageByID("page1");
			$formview1 = $page1->GetControlByID("formview1");
			$formview1->GetItemByID("txtName")->Value = $_POST["resp_EnteredName"];
			$formview1->GetItemByID("txtEmailAddress")->Value = $_POST["resp_EnteredEmailAddress"];
			$formview1->GetItemByID("txtMessage")->Value = $_POST["resp_Message"];
		}
	}
?>