<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\Customer;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationDocumentController extends Controller
{
    /**
     * Store a newly created application document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'document_type_id' => 'required|exists:document_types,id',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'redirect' => 'nullable|string',
        ]);

        // Check if document already exists for this customer
        $exists = ApplicationDocument::where('customer_id', $validated['customer_id'])
            ->where('document_type_id', $validated['document_type_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'This document type has already been uploaded for this customer.')
                ->withInput();
        }

        // Upload file
        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('customer-documents/' . $validated['customer_id'], $fileName, 'public');

        // Create document record
        $document = ApplicationDocument::create([
            'customer_id' => $validated['customer_id'],
            'document_type_id' => $validated['document_type_id'],
            'is_submitted' => true,
            'file_path' => $filePath,
        ]);

        // Redirect back to the customer page or specified redirect URL with hash fragment for documents tab
        $redirectUrl = $request->input('redirect') ?? route('admin.customers.show', $validated['customer_id']);
        // Append the hash fragment if it doesn't already have one
        if (!str_contains($redirectUrl, '#')) {
            $redirectUrl .= '#documents';
        }
        
        return redirect($redirectUrl)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Remove the specified application document from storage.
     *
     * @param  \App\Models\ApplicationDocument  $applicationDocument
     * @return \Illuminate\Http\Response
     */
    public function destroy(ApplicationDocument $applicationDocument)
    {
        $customerId = $applicationDocument->customer_id;

        // Delete the file from storage
        if ($applicationDocument->file_path && Storage::disk('public')->exists($applicationDocument->file_path)) {
            Storage::disk('public')->delete($applicationDocument->file_path);
        }

        // Delete the record
        $applicationDocument->delete();

        // Redirect back to the customer detail page with documents tab active
        return redirect()->route('admin.customers.show', $customerId)
            ->with('success', 'Document deleted successfully.')
            ->withFragment('documents');
    }
} 