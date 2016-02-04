<?php namespace App\Http\Controllers;

use Auth;
use App\Contact;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ActiveCampaign;

class ContactController extends Controller {

	public function __construct() {
		$this->ac =  new ActiveCampaign("https://genesisdigital.api-us1.com", "e69f7701062bc576faa3e393ce5839f4a67d48fa8ed88a14a9547abfd8666589e715efa8");
	}

	public function __destruct() {
		unset($this->ac);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$contacts = Contact::where('user_id', '=', Auth::user()->id)->get();
		return view ('contacts.index',compact('contacts'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('contacts.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$user_id = Auth::user()->id;

		$this->validate($request, [
			'email' => 'required|email|unique:contacts,email,NULL,id,user_id,' . $user_id, 
		]);

		$contact = $request->all();
		$contact['user_id'] = $user_id;
                $contact['info'] = json_encode(array_filter($contact['info']));

		if ($subscriber_id = $this->addActiveContact($contact)) {
			$contact['active_id'] = $subscriber_id;
			Contact::create($contact);
			return response()->json(['responseText' => 'Success!'], 200);
                } else {
			return response()->json(['responseText' => 'Failure.'],422);	
                } 
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$contact = Contact::where('user_id', '=', Auth::user()->id)->where('id', '=', $id)->firstOrFail();
   		return view('contacts.show',compact('contact'));
        }

        /**
         * Return all the resources as JSON.
         *
         * @return Response
         */
        public function json()
        {
                $contacts = Contact::where('user_id', '=', Auth::user()->id)->get();
                foreach ($contacts as $contact) {
                  $contact['editLink'] = link_to_route('contacts.edit', 'View / Edit', array($contact->id), array('class'=>'btn btn-warning dialog'));
                  $contact['deleteLink'] = link_to_route('contacts.destroy', 'Delete', array($contact->id), array('class'=>'btn btn-danger deleteLink', 'data-token' => csrf_token()));
                }
                return response()->json($contacts, 200);
        }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$contact = Contact::where('user_id', '=', Auth::user()->id)->where('id', '=', $id)->firstOrFail();
		$info_fields = (empty($contact->info)) ? array() : json_decode($contact->info);
		return view('contacts.edit',compact('contact'))->with('info_fields',$info_fields);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param  Request  $request
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$user_id = Auth::user()->id;

		$this->validate($request, [
                        'email' => 'required|email|unique:contacts,email,' . $id . ',id,user_id,' . $user_id,
                ]);

		$contactUpdate = $request->all();
		$contactUpdate['info'] = json_encode(array_filter($contactUpdate['info']));
		$contact = Contact::where('user_id', '=', Auth::user()->id)->where('id', '=', $id)->firstOrFail();
		$contactUpdate['active_id'] = $contact->active_id;
		$unused_tags = array_diff(json_decode($contact['info']), json_decode($contactUpdate['info']));
		if ($this->editActiveContact($contactUpdate, $unused_tags)) {
			$contact->update($contactUpdate);
                        return response()->json(['responseText' => 'Success!'], 200);
                } else {
                        return response()->json(['responseText' => 'Failure.'],422);
                }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$contact = Contact::where('user_id', '=', Auth::user()->id)->where('id', '=', $id)->firstOrFail();
		if ($this->deleteActiveContact($contact->active_id)) {
			$contact->delete();
			return response()->json(['responseText' => 'Success!'], 200);
		} else {
			return response()->json(['responseText' => 'Failure.'],422);
		} 
	}

        /**
         * Find our user's ActiveSync list or create a new one.
         *
         * @return Response
         */
	private function getActiveList() 
	{
		$email = Auth::user()->email;
		$acQuery = array(
			'ids'		=> 'all',
			'filters[name]'	=> $email,
			'full'		=> 0,
		);
		$acQueryResponse = $this->ac->api('list/list', $acQuery);
		if ($acQueryResponse->success) {
			return $acQueryResponse->{'0'}->id;
		} else {
			$acList = array(
				'name'			=> $email,
				'subscription_notify'	=> '',
				'unsubscription_notify'	=> '',
				'to_name'		=> 'Recipient',
				'carboncopy'		=> '',
				'sender_name'		=> 'Testing',
				'sender_addr1'		=> '123 testing lane',
				'sender_city'		=> 'atlanta',
				'sender_zip'		=> '30339',
				'sender_country'	=> 'USA',
				'sender_url'		=> 'http://laraveldemo-ryansulliv4n.rhcloud.com/contacts',
				'sender_reminder'	=> 'You are receiving this because your contact info was entered in the Laravel demo contact list manager.',
			);

			$acListResponse = $this->ac->api('list/add', $acList);
			if ($acListResponse->success) {
				return $acListResponse->id;
			} else {
				return false;
			}
		}
	}

        /**
         * Add the specified contact to ActiveSync.
         *
         * @param  array $contact
         * @return Response
         */
	private function addActiveContact($contact) 
	{
		$list_id = $this->getActiveList();
        	$acContact = array(
			'email'				=> $contact['email'],
			'first_name'			=> $contact['firstname'],
			'last_name'			=> $contact['lastname'],
			'phone'				=> $contact['phone'],
			'tags'				=> implode(',', json_decode($contact['info'])),
			'p[' . $list_id . ']'		=> $list_id,
			'status[' . $list_id .']'	=> 1,
		);
		$acResponse = $this->ac->api('contact/add', $acContact);
		if ($acResponse->success) {
			return $acResponse->subscriber_id;
		} else {
			return false;
		}
	}

	/**
         * Remove the specified contact from ActiveSync.
         *
         * @param  int  $contactID
         * @return Response
         */
	private function deleteActiveContact($contactID)
	{
		$acResponse = $this->ac->api('contact/delete?id=' . $contactID);
		return true;
	}

        /**
         * Remove the specified contact to ActiveSync.
         *
         * @param  array  $contact
	 * @param  array  $unused_tags
         * @return Response
         */
	private function editActiveContact($contact, $unused_tags)
	{
		
		$acContact = array(
			'overwrite'	=> 0,
			'id'		=> $contact['active_id'],
			'email'		=> $contact['email'],
			'first_name'	=> $contact['firstname'],
			'last_name'	=> $contact['lastname'],
			'phone'		=> $contact['phone'],
			'tags'		=> implode(',', json_decode($contact['info'])),
		);

		$this->removeActiveTags($contact['active_id'], $unused_tags);
		$acResponse = $this->ac->api('contact/edit', $acContact);
		if ($acResponse->success) {
			$this->removeActiveTags($contact['active_id'], $unused_tags);
			return true;
		} else {
			return false;
		}
	}

	/**
         * Remove the specified tags from ActiveSync contact.
         *
         * @param  int  $contactID
         * @param  array  $tags
         * @return Response
         */
	private function removeActiveTags($contactID, $tags) 
	{
		$acResponse = $this->ac->api('contact/tag/remove', array('id' => $contactID, 'tags' => $tags));
		if ($acResponse->success) {
			return true;
		} else {
			return false;
		}
	}


}
