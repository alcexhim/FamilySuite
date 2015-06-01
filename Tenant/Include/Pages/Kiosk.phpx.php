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
	
	class KioskPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
		}
	}
?>