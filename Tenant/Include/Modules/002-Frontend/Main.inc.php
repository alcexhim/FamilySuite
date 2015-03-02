<?php
	use WebFX\System;
	use WebFX\ModulePage;
use WebFX\Parser\WebFXParser;
use WebFX\Module;
	
	$parser = new WebFXParser();

	global $RootPath;
	$thisRootPath = dirname(__FILE__);

	$fileNames = glob($RootPath . "/Include/MasterPages/*.php");
	foreach ($fileNames as $fileName)
	{
		require_once($fileName);
	}
	
	$fileNames = glob($thisRootPath . "/MasterPages/*.php");
	foreach ($fileNames as $fileName)
	{
		require_once($fileName);
	}
	
	$fileNames = glob($RootPath . "/Include/MasterPages/*.phpx");
	foreach ($fileNames as $fileName)
	{
		$parser->LoadFile($fileName);
	}
	
	$fileNames = glob($thisRootPath . "/MasterPages/*.phpx");
	foreach ($fileNames as $fileName)
	{
		$parser->LoadFile($fileName);
	}

	$fileNames = glob($RootPath . "/Include/Pages/*.php");
	foreach ($fileNames as $fileName)
	{
		require_once($fileName);
	}
	
	$fileNames = glob($thisRootPath . "/Pages/*.php");
	foreach ($fileNames as $fileName)
	{
		require_once($fileName);
	}
	
	$fileNames = glob($RootPath . "/Include/Pages/*.phpx");
	foreach ($fileNames as $fileName)
	{
		$parser->LoadFile($fileName);
	}
	
	$fileNames = glob($thisRootPath . "/Pages/*.phpx");
	foreach ($fileNames as $fileName)
	{
		$parser->LoadFile($fileName);
	}
	
	$pages = array();
	foreach ($parser->Pages as $page)
	{
		$mpage1 = new ModulePage($page->FileName, function($mpage, $path)
		{
			$mpage->ExtraData->Render();
			return true;
		});
		$mpage1->ExtraData = $page;
		
		$pages[] = $mpage1;
	}
	System::$Modules[] = new Module("net.FamilySuite.Tenant.Frontend", $pages);
?>