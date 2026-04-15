<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class RequiredDocumentsController extends Controller
{
    /**
     * Get list of required documents with their status for the authenticated customer
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $customer = Auth::user();
            $documentTypes = DocumentType::all();
            
            $documents = $documentTypes->map(function ($documentType) use ($customer) {
                $applicationDocument = $customer->applicationDocuments()
                    ->where('document_type_id', $documentType->id)
                    ->first();

                return [
                    'id' => $documentType->id,
                    'name' => $documentType->name,
                    'description' => $documentType->description,
                    'is_mandatory' => $documentType->is_mandatory,
                    'status' => $applicationDocument ? 'Uploaded' : ($documentType->is_mandatory ? 'Pending' : 'Optional'),
                    'file_details' => $applicationDocument ? [
                        'file_path' => $applicationDocument->file_path,
                        'upload_date' => $applicationDocument->created_at->format('Y-m-d H:i:s')
                    ] : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'documents' => $documents,
                    'important_note' => 'Please upload all required documents in PDF, JPG, or PNG format. All documents must be clear and valid.'
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch required documents.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Upload a document for the authenticated customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $document_type_id
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, $document_type_id)
    {
        try {
            $customer = Auth::user();
            
            // Validate document type exists
            $documentType = DocumentType::findOrFail($document_type_id);
            
            // Validate request
            $request->validate([
                'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120' // 5MB max
            ]);

            // Check if document already exists
            $existingDocument = $customer->applicationDocuments()
                ->where('document_type_id', $document_type_id)
                ->first();

            // If exists, delete old file
            if ($existingDocument && Storage::disk('public')->exists($existingDocument->file_path)) {
                Storage::disk('public')->delete($existingDocument->file_path);
            }

            // Upload new file
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs(
                'customer-documents/' . $customer->id,
                $fileName,
                'public'
            );

            // Create or update document record
            if ($existingDocument) {
                $existingDocument->update([
                    'file_path' => $filePath,
                    'is_submitted' => true
                ]);
                $document = $existingDocument;
            } else {
                $document = ApplicationDocument::create([
                    'customer_id' => $customer->id,
                    'document_type_id' => $document_type_id,
                    'file_path' => $filePath,
                    'is_submitted' => true
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Document uploaded successfully',
                'data' => [
                    'document' => $document,
                    'file_url' => Storage::disk('public')->url($filePath)
                ]
            ], Response::HTTP_OK);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document type not found.'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload document.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Download a specific document
     *
     * @param  int  $document_type_id
     * @return \Illuminate\Http\Response
     */
    public function download($document_type_id)
    {
        try {
            $customer = Auth::guard('customer')->user();
            
            $document = $customer->applicationDocuments()
                ->where('document_type_id', $document_type_id)
                ->firstOrFail();

            if (!Storage::disk('public')->exists($document->file_path)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $filePath = storage_path('app/public/' . $document->file_path);
            $fileName = basename($document->file_path);

            return response()->download($filePath, $fileName);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document not found.'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to download document.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a specific document
     *
     * @param  int  $document_type_id
     * @return \Illuminate\Http\Response
     */
    public function delete($document_type_id)
    {
        try {
            $customer = Auth::user();
            
            $document = $customer->applicationDocuments()
                ->where('document_type_id', $document_type_id)
                ->firstOrFail();

            // Delete file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete record
            $document->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Document deleted successfully'
            ], Response::HTTP_OK);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document not found.'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete document.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 