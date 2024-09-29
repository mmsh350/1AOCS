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
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => '01',
                'errors' => $validator->errors(),
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
                'contact' => $contact,
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
                'contacts' => $contacts,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'responseCode' => '01',
                'message' => 'Failed to fetch contacts',
                'error' => $e->getMessage(),
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
                'contact' => $contact,
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
                'message' => 'Contact deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => '01',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function getCount(Request $request)
    {
        try {

            $total_messages = Contact::count(); // Count all messages
            $read_messages = Contact::where('status', 'read')->count(); // Count only read messages
            $unread_messages = Contact::where('status', 'unread')->count(); // Count only unread messages

            return response()->json([
                'responseCode' => '00',
                'message' => 'Message Count Received successfully',
                'message_count' => [
                    'total' => $total_messages,
                    'read' => $read_messages,
                    'unread' => $unread_messages,
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'responseCode' => '01',
                'message' => 'Failed to fetch contacts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            // Find the message by its ID
            $message = Contact::findOrFail($id);

            // Update the message status to 'read'
            $message->status = 'read';
            $message->save();

            return response()->json([
                'responseCode' => '00',
                'message' => 'Message marked as read successfully',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'responseCode' => '01',
                'message' => 'Message not found',
                'error' => $e->getMessage(),
            ], 500);
        }

    }
}
