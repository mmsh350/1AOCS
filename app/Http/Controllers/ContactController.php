<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    // Create a new contact
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'required|string|max:15',
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => '01',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $contact = Contact::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'title' => $request->title,
                'message' => $request->message,
            ]);

            return response()->json([
                'responseCode' => '00',
                'message' => 'Contact saved successfully',
                'contact' => $contact
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => '01',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Fetch all contacts
    public function index(Request $request)
    {
        // try {
        //     $contacts = Contact::all();
        //     return response()->json([
        //         'responseCode' => '00',
        //         'contacts' => $contacts
        //     ], 200);

        // } catch (\Exception $e) {
        //     return response()->json([
        //         'responseCode' => '01',
        //         'error' => $e->getMessage(),
        //     ], 500);
        // }
    try {
        // Get the number of items per page from the request or default to 10
        $perPage = $request->query('per_page', 10);

        // Paginate contacts
        $contacts = Contact::paginate($perPage);

        return response()->json([
            'responseCode' => '00',
            'message' => 'Contacts retrieved successfully',
            'contacts' => $contacts
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'responseCode' => '01',
            'message' => 'Failed to fetch contacts',
            'error' => $e->getMessage()
        ], 500);
    }


    }

    // Fetch a single contact by ID
    public function show($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            return response()->json([
                'responseCode' => '00',
                'contact' => $contact
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => '01',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    // Soft delete a contact
    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();

            return response()->json([
                'responseCode' => '00',
                'message' => 'Contact deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => '01',
                'error' => $e->getMessage(),
            ], 404);
        }
    }
}
