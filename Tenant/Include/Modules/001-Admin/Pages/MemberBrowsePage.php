<?php
	namespace FamilySuite\Tenant\Modules\Frontend\Pages;
	
	use WebFX\Parser\WebFXPage;
	use WebFX\EventArgs;
		
	class MemberBrowsePage extends WebFXPage
	{
		public function OnInitialized(EventArgs $e)
		{
			foreach ($this->Page->GetControlByID("mnuSidebar")->Items as $item)
			{
				$item->Expanded = false;
				$item->Selected = false;
			}
			
			$this->Page->GetControlByID("mnuSidebar")->GetItemByID("mnuSidebarMembers")->Expanded = true;
			$this->Page->GetControlByID("mnuSidebar")->GetItemByID("mnuSidebarMembers")->Selected = true;
		}
	}
?>