<?php
	namespace FamilySuite\Tenant\MasterPages;

	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	use Phast\System;
			
	class Admin extends PhastPage
	{
		public function OnInitializing($e)
		{
			/*
			if ($_SESSION["Authentication.UserName"] == null)
			{
				$e->Cancel = true;
				System::Redirect("~/account/login");
			}
			*/
		}
		
	}
?>