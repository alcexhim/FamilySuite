<?php
	namespace FamilySuite\Tenant\Modules\Admin\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\EventArgs;
	use Phast\System;
	use Phast\CancelEventArgs;
					
	class LoginPage extends PhastPage
	{
		public function OnClassLoaded(EventArgs $e)
		{
			$this->Page->GetControlByID("paraFooter")->Content = System::GetConfigurationValue("Pages.LoginPage.FooterContent");
		}
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$page = $e->RenderingPage;
			
			$key = $page->GetPathVariableValue("key");
			if ($key != null)
			{
				$user = User::GetByLoginKey($key);
				
			}
		}
	}
?>