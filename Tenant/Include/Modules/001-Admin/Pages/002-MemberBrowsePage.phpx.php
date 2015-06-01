<?php
	namespace FamilySuite\Tenant\Modules\Frontend\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\EventArgs;
	
	use FamilySuite\Objects\User;
	
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
			
	class MemberBrowsePage extends PhastPage
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
			
			$lvMembers = $this->Page->GetControlByID("lvMembers");
			
			$items = User::Get();
			foreach ($items as $item)
			{
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("lvcUserName", $item->UserName),
					new ListViewItemColumn("lvcDisplayName", $item->DisplayName),
					new ListViewItemColumn("lvcAge", "Unknown")
				));
				$lvi->Value = $item->UserName;
				$lvMembers->Items[] = $lvi;
			}
		}
	}
?>