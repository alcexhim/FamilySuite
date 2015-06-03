<?php
	namespace FamilySuite\Tenant\MasterPages;
	
	use Phast\Parser\PhastPage;
	
	class Blank extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$e->RenderingPage->ClassList[] = "EnableHeader";
		}
	}
?>