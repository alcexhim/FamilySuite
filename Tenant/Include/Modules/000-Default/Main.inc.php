<?php
	use Phast\System;
	use Phast\ModulePage;
	use Phast\Parser\PhastParser;
	use Phast\Module;
	
	use FamilySuite\Tenant\Pages\ErrorPage;
	
	System::$ErrorEventHandler = function($e)
	{
		$page = new ErrorPage();
		$page->MessageContent = $e->Message;
		$page->Render();
		return true;
	};
?>