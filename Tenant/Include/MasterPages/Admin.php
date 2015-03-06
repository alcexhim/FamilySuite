<?php
	namespace FamilySuite\Tenant\MasterPages;

	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	use Phast\System;
			
	class Admin extends PhastPage
	{
		public function OnInitializing($e)
		{
			if (!isset($_SESSION["Authentication.UserName"]))
			{
				System::RedirectToLoginPage();
				$e->Cancel = true;
				return;
			}
		}
	}
?>