<?php
	namespace FamilySuite\Tenant\Modules\Frontend\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\EventArgs;
	use Phast\System;
			
	class DashboardPage extends PhastPage
	{
		public function OnInitialized(EventArgs $e)
		{
			foreach ($this->Page->GetControlByID("mnuSidebar")->Items as $item)
			{
				$item->Expanded = false;
				$item->Selected = false;
			}
			
			$this->Page->GetControlByID("mnuSidebar")->GetItemByID("mnuSidebarDashboard")->Selected = true;
		}
	}
?>