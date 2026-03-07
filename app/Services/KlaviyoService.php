<?php

namespace App\Services;
use App\User;
use Klaviyo\Klaviyo as Klaviyo;
use Klaviyo\Model\ProfileModel as KlaviyoProfile;
use Klaviyo\Model\EventModel as KlaviyoEvent;

class KlaviyoService
{
    protected $public_key;
    protected $private_key;
    protected $registration_list_id;
    protected $client;

    public function __construct() {
        $this->public_key = config('services.klaviyo.public_key');
        $this->private_key = config('services.klaviyo.private_key');
        $this->registration_list_id = config('services.klaviyo.registration_list_id');
        $this->client = new Klaviyo($this->private_key, $this->public_key);
    }

    protected function addUserToList(User $user, $listID)
    {
        if($this->public_key && $this->private_key) {
            $profile = new KlaviyoProfile([
                    '$email' => $user->email,
                    '$phone_number' => $user->phone,
            ]);

            $rsp = $this->client->lists->subscribeMemberstoList($listID, [
                $profile
            ]);
        }
    }

    public function newUserRegisteredEvent(User $user)
    {
        if($this->public_key && $this->private_key) {
            $event = new KlaviyoEvent([
                'event' => "Created Account",
                'customer_properties' => [
                    '$email' => $user->email,
                    '$phone_number' => $user->phone,
                ],
                'properties' => [
                    '$event_id' => "userid-" . $user->id
                ],
            ]);
            $this->client->publicAPI->track( $event );
        }
    }

    public function addUserToRegistrationList(User $user) 
    {
        return $this->addUserToList($user, $this->registration_list_id);
    }
}
