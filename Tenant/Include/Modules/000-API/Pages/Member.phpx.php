<?php
	namespace FamilySuite\Tenant\Modules\API\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use FamilySuite\Objects\User;
	
	class MemberPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$action = $e->RenderingPage->GetPathVariableValue("action");
			
			switch (strtolower($action))
			{
				case "retrieve":
				{
					if ($_POST["user_UserName"] != null)
					{
						$items = array();
						$item = User::GetByCredentials($_POST["user_UserName"]);
						if ($item != null) $items[] = $item;
					}
					else
					{
						$items = User::Get();
					}
					$count = count($items);
					
					echo("{ \"result\": \"success\", \"items\": [ ");
					for ($i = 0; $i < $count; $i++)
					{
						$item = $items[$i];
						echo ("{ \"UserName\": \"" . $item->UserName . "\", \"DisplayName\": \"" . $item->DisplayName . "\" }");
						
						if ($i < $count - 1) echo(", ");
					}
					echo(" ] }");
					
					break;
				}
			}
			
			$e->Cancel = true;
		}
	}
?>