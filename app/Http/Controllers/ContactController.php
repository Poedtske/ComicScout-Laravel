<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create(Request $request)
    {
        return view('contact.create');

    }

    public function store(ContactFormRequest $request)
    {
        Contact::create([
            'email'=>$request->input('email'),
            'title'=>$request->input('title'),
            'content'=>$request->input('content'),
        ]);


        return redirect()
                ->route('home2',[$request])
                ->with('success', 'Message has been submitted!');
    }



    public function destroy(Contact $contact)
    {
        $this->authorize('delete',$contact);
        $contact->delete();

        return redirect()
        ->route('dashboard')
        ->with('success','Message has been deleted!');
    }
}
