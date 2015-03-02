<?php
	namespace FamilySuite\Tenant\MasterPages;
	
	class MessagePage extends BlankPage
	{
		public $MessageTitle;
		public $MessageSubtitle;
		public $MessageContent;
		
		public function __construct()
		{
			$this->MessageTitle = "An unexpected error has occurred";
			$this->MessageSubtitle = "We are working to fix this issue as soon as possible";
		}
		
		protected function RenderContent()
		{
		?>
		<h1><?php echo($this->MessageTitle); ?></h1>
		<h3><?php echo($this->MessageSubtitle); ?></h3>
		<div class="Panel">
			<div class="Content">
				<p><?php echo($this->MessageContent); ?></p>
			</div>
		</div>
		<?php
		}
	}
?>