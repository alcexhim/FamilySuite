<?php
	namespace FamilySuite\Tenant\MasterPages;

	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	use Phast\System;
use FamilySuite\Objects\UserLogin;
				
	class Admin extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$e->RenderingPage->ClassList[] = "EnableHeader";
			$e->RenderingPage->ClassList[] = "EnableSidebar";
			
			if ($e->RenderingPage->GetServerVariableValue("RequireLogin") != "false")
			{
				$loginToken = null;
				if (isset($_SESSION["Authentication.LoginToken"])) $loginToken = UserLogin::GetByToken($_SESSION["Authentication.LoginToken"]);
				
				if ($loginToken == null)
				{
					System::RedirectToLoginPage();
					$e->Cancel = true;
					return;
				}
				else
				{
					$litUserName = $e->RenderingPage->GetControlByID("litUserName");
					$litUserName->Value = $loginToken->User->DisplayName;
					
					$litStatusText = $e->RenderingPage->GetControlByID("litStatusText");
					$litStatusText->Value = "Logged in";
				}
			}
		}
	}
?>