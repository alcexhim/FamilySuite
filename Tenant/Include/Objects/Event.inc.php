<?php
	namespace FamilySuite\Objects;

	use DateTime;
	use PDO;
	
	use Phast\Data\DataSystem;
	
	class Event
	{
		/**
		 * The unique identifier of this Event in the database.
		 * @var int
		 */
		public $ID;
		/**
		 * The name used in URLs to identify this Event.
		 * @var string
		 */
		public $Name;
		/**
		 * The title of this Event displayed in pages around the site.
		 * @var string
		 */
		public $Title;
		/**
		 * A short description of this Event.
		 * @var string
		 */
		public $Description;
		/**
		 * The date and time at which this Event begins.
		 * @var DateTime
		 */
		public $BeginTimestamp;
		/**
		 * The date and time at which this Event ends.
		 * @var DateTime
		 */
		public $EndTimestamp;
		/**
		 * The place at which this Event is located.
		 * @var Location
		 */
		public $Location;
		
		public static function GetByAssoc($values)
		{
			$item = new Event();
			$item->ID = $values["event_ID"];
			$item->Name = $values["event_Name"];
			$item->Title = $values["event_Title"];
			$item->Description = $values["event_Description"];
			$item->BeginTimestamp = new DateTime($values["event_BeginTimestamp"]);
			$item->EndTimestamp = new DateTime($values["event_EndTimestamp"]);
			$item->Location = Location::GetByID($values["event_LocationID"]);
			return $item;
		}
		public static function GetByIDOrName($idOrName)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM fs_Events WHERE event_Name = :event_IDOrName OR event_ID = :event_IDOrName";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":event_IDOrName" => $idOrName
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Event::GetByAssoc($values);
		}
		
		/**
		 * @return Event[] The events related to this event.
		 */
		public function GetRelatedEvents()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM Events, fs_RelatedEvents WHERE Events.event_ID = fs_RelatedEvents.related_SecondaryEvent AND fs_RelatedEvents.related_PrimaryEvent = :related_PrimaryEventID";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				"related_PrimaryEventID" => $this->ID
			));
			
			$retval = array();
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = Event::GetByAssoc($values);
				if ($item != null) $retval[] = $item;
			}
			return $retval;
		}
		
		/**
		 * Counts the number of invited, attending, declining, and pending responses to
		 * invitations for this event.
		 */
		public function CountResponses()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT (SELECT COUNT(*) FROM Addresses WHERE invitation_EventID = :EventID) AS count_Invited, (SELECT COUNT(*) FROM fs_EventInvitationResponses WHERE resp_EventID = :EventID AND resp_Status = 1) AS count_Attending, (SELECT COUNT(*) FROM fs_EventInvitationResponses WHERE resp_EventID = :EventID AND resp_Status = 0) AS count_Declining";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":EventID" => $this->ID
			));
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			
			$countInvited = $values["count_Invited"];
			$countAttending = $values["count_Attending"];
			$countDeclining = $values["count_Declining"];
			
			return new EventResponseCount($countInvited, $countAttending, $countDeclining);
		}
		
		public function GetInvitations()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT *, fs_Countries.country_Title FROM Addresses, fs_Countries WHERE Addresses.invitation_EventID = " . $this->ID . " AND fs_Countries.country_ID = Addresses.CountryID";
			$statement = $pdo->prepare($query);
			$statement->execute();
			$count = $statement->rowCount();
			
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = EventInvitation::GetByAssoc($values);
				$retval[] = $item;
			}
			return $retval;
		}
		/**
		 * Gets all the EventInvitationResponses associated with this Event.
		 * @var EventInvitationResponse
		 */
		public function GetResponses()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT *, fs_EventGuestTypes.guesttype_Title, fs_EventInviteSources.invitesource_Title FROM fs_EventInvitationResponses, fs_EventGuestTypes, fs_EventInviteSources WHERE fs_EventInvitationResponses.resp_EventID = :resp_EventID AND fs_EventInvitationResponses.resp_GuestTypeID = fs_EventGuestTypes.guesttype_ID AND fs_EventInvitationResponses.resp_InviteSourceID = fs_EventInviteSources.invitesource_ID";
			$statement = $pdo->prepare($query);
			$statement->execute
			(
				array
				(
					":resp_EventID" => $this->ID
				)
			);
			$count = $statement->rowCount();
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = EventInvitationResponse::GetByAssoc($values);
				$retval[] = $item;
			}
			return $retval;
		}
	}
	class EventResponseCount
	{
		public $Invited;
		public $Attending;
		public $Declining;
		public $Pending;
		
		public function __construct($invited, $attending, $declining)
		{
			$this->Invited = $invited;
			$this->Attending = $attending;
			$this->Declining = $declining;
			$this->Pending = $this->Invited - $this->Attending - $this->Declining;
		}
	}
	
?>