<?php
	namespace FamilySuite\Tenant\Modules\Admin\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\EventArgs;
	use Phast\System;
	use Phast\CancelEventArgs;
use FamilySuite\Objects\User;
use FamilySuite\Objects\UserLogin;
		
	class LogoutPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$token = UserLogin::GetByCurrentToken();
			if ($token != null) $token->User->Logout();
			
			$_SESSION["Authentication.LoginToken"] = null;
			System::Redirect("~/");
			
			$e->Cancel = true;
		}
	}
	class LoginPage extends PhastPage
	{
		public function OnClassLoaded(EventArgs $e)
		{
			$this->Page->GetControlByID("paraFooter")->Content = System::GetConfigurationValue("Pages.LoginPage.FooterContent");
		}
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$page = $e->RenderingPage;
			if ($page->IsPostback)
			{
				// do the login, login
				$username = $_POST["user_UserName"];
				$password = $_POST["user_Password"];
				
				$user = User::GetByCredentials($username, $password);
				if ($user != null)
				{
					$token = $user->Login();
					if ($token != null)
					{
						$_SESSION["Authentication.LoginToken"] = $token->Token;
						System::RedirectFromLoginPage();
					}
					else
					{
						$_SESSION["Authentication.LoginToken"] = null;
					}
				}
			}
		}
	}
?>