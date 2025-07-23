<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\CustomField;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\MergeContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Contact::with('customFieldValues.customField')->active();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhereHas('customFieldValues', function ($q2) use ($search) {
                      $q2->where('value', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('gender')) {
            $query->filterByGender($request->gender);
        }

        // Custom field filtering
        if ($request->filled('custom_fields')) {
            foreach ($request->custom_fields as $fieldName => $value) {
                if (!empty($value)) {
                    $customField = CustomField::where('name', $fieldName)->first();
                    if ($customField) {
                        $query->whereHas('customFieldValues', function ($q) use ($customField, $value) {
                            $q->where('custom_field_id', $customField->id)
                                ->where('value', 'LIKE', "%{$value}%");
                        });
                    }
                }
            }
        }

        $contacts = $query->paginate(15);
        $customFields = CustomField::active()->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.contacts.partials.contact-list', [
                    'contacts' => $contacts,
                    'customFields' => $customFields
                ])->render(),
                'pagination' => $contacts->appends($request->all())->links()->render()
            ]);
        }

        return view('admin.contacts.index', compact('contacts', 'customFields'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customFields = CustomField::active()->get();
        return view('admin.contacts.create', compact('customFields'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle file uploads
            if ($request->hasFile('profile_image')) {
                $data['profile_image'] = $request->file('profile_image')->store('contacts/images', 'public');
            }

            if ($request->hasFile('additional_file')) {
                $data['additional_file'] = $request->file('additional_file')->store('contacts/files', 'public');
            }

            $contact = Contact::create($data);

            // Handle custom fields
            if ($request->filled('custom_fields')) {
                foreach ($request->custom_fields as $fieldName => $value) {
                    if (!empty($value)) {
                        $contact->setCustomFieldValue($fieldName, $value);
                    }
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact created successfully!',
                    'contact' => $contact->load('customFieldValues.customField')
                ]);
            }

            return redirect()->route('contacts.index')->with('success', 'Contact created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create contact: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->withErrors(['error' => 'Failed to create contact.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        $contact->load('customFieldValues.customField', 'mergedContacts');
        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        $contact->load('customFieldValues.customField');
        $customFields = CustomField::active()->get();
        return view('admin.contacts.edit', compact('contact', 'customFields'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContactRequest $request, Contact $contact)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle file uploads
            if ($request->hasFile('profile_image')) {
                // Delete old image
                if ($contact->profile_image) {
                    Storage::disk('public')->delete($contact->profile_image);
                }
                $data['profile_image'] = $request->file('profile_image')->store('contacts/images', 'public');
            }

            if ($request->hasFile('additional_file')) {
                // Delete old file
                if ($contact->additional_file) {
                    Storage::disk('public')->delete($contact->additional_file);
                }
                $data['additional_file'] = $request->file('additional_file')->store('contacts/files', 'public');
            }

            $contact->update($data);

            // Handle custom fields
            if ($request->filled('custom_fields')) {
                foreach ($request->custom_fields as $fieldName => $value) {
                    $contact->setCustomFieldValue($fieldName, $value);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact updated successfully!',
                    'contact' => $contact->fresh()->load('customFieldValues.customField')
                ]);
            }

            return redirect()->route('contacts.index')->with('success', 'Contact updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update contact: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->withErrors(['error' => 'Failed to update contact.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact, Request $request)
    {
        try {
            // Delete associated files
            if ($contact->profile_image) {
                Storage::disk('public')->delete($contact->profile_image);
            }
            if ($contact->additional_file) {
                Storage::disk('public')->delete($contact->additional_file);
            }

            $contact->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact deleted successfully!'
                ]);
            }

            return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete contact: ' . $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => 'Failed to delete contact.']);
        }
    }

    public function merge(MergeContactRequest $request)
    {
        try {
            DB::beginTransaction();

            $masterContact = Contact::findOrFail($request->master_contact_id);
            $secondaryContact = Contact::findOrFail($request->secondary_contact_id);

            $masterContact->mergeWith($secondaryContact, [
                'custom_field_strategy' => $request->custom_field_strategy ?? 'keep_master'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contacts merged successfully!',
                'redirect_url' => route('contacts.show', $masterContact)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to merge contacts: ' . $e->getMessage()
            ], 422);
        }
    }

    public function getMergePreview(Request $request)
    {
        $masterContact = Contact::with('customFieldValues.customField')->findOrFail($request->master_contact_id);
        $secondaryContact = Contact::with('customFieldValues.customField')->findOrFail($request->secondary_contact_id);
        $strategy = $request->custom_field_strategy ?? 'keep_master';
        $customFields = CustomField::active()->get();
        $html = view('admin.contacts.partials.merge-preview', compact('masterContact', 'secondaryContact', 'strategy', 'customFields'))->render();
        return response()->json(['html' => $html]);
    }
}
