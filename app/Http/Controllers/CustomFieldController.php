<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;
use App\Http\Requests\CustomFieldRequest;

class CustomFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customFields = CustomField::orderBy('sort_order')->paginate(15);
        return view('custom-fields.index', compact('customFields'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('custom-fields.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomFieldRequest $request)
    {
        try {
            $data = $request->validated();

            if (!isset($data['sort_order'])) {
                $data['sort_order'] = CustomField::max('sort_order') + 1;
            }

            $customField = CustomField::create($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Custom field created successfully!',
                    'customField' => $customField
                ]);
            }

            return redirect()->route('custom-fields.index')->with('success', 'Custom field created successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create custom field: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->withErrors(['error' => 'Failed to create custom field.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function show(CustomField $customField)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomField $customField)
    {
        return view('custom-fields.edit', compact('customField'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function update(CustomFieldRequest $request, CustomField $customField)
    {
        try {
            $customField->update($request->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Custom field updated successfully!',
                    'customField' => $customField
                ]);
            }

            return redirect()->route('custom-fields.index')->with('success', 'Custom field updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update custom field: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->withErrors(['error' => 'Failed to update custom field.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomField  $customField
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomField $customField, Request $request)
    {
        try {
            $customField->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Custom field deleted successfully!'
                ]);
            }

            return redirect()->route('custom-fields.index')->with('success', 'Custom field deleted successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete custom field: ' . $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => 'Failed to delete custom field.']);
        }
    }
}
