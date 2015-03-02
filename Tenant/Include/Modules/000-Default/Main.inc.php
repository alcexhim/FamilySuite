<?php
	use WebFX\System;
	use WebFX\ModulePage;
	use WebFX\Parser\WebFXParser;
	use WebFX\Module;
	
	use FamilySuite\Tenant\Pages\ErrorPage;
	
	System::$ErrorEventHandler = function($e)
	{
		$page = new ErrorPage();
		$page->MessageContent = $e->Message;
		$page->Render();
		return true;
	};
?>