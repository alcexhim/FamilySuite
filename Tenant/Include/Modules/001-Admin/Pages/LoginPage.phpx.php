<?php
	namespace FamilySuite\Tenant\Modules\Admin\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\EventArgs;
	use Phast\System;
				
	class LoginPage extends PhastPage
	{
		public function OnClassLoaded(EventArgs $e)
		{
			$this->Page->GetControlByID("paraFooter")->Content = System::GetConfigurationValue("Pages.LoginPage.FooterContent");
		}
	}
?>