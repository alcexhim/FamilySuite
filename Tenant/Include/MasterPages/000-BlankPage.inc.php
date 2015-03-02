<?php
	namespace FamilySuite\Tenant\MasterPages;
	
	use WebFX\WebStyleSheet;
	
	class BlankPage extends \WebFX\WebPage
	{
		public function __construct()
		{
			parent::__construct();
			$this->StyleSheets[] = new WebStyleSheet("$(Configuration:WebFramework.StaticPath)/StyleSheets/Avondale/Main.css");
		}
	}
?>