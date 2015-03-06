<?php
	namespace FamilySuite\Tenant\MasterPages;

	use WebFX\CancelEventArgs;
	use WebFX\Parser\WebFXPage;
	use WebFX\System;
			
	class Admin extends WebFXPage
	{
		public function OnInitializing($e)
		{
			if ($_SESSION["Authentication.UserName"] == null)
			{
				$e->Cancel = true;
				System::Redirect("~/account/login");
			}
		}
		
	}
?>