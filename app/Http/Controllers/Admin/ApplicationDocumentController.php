<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\Customer;
use App\Models\documentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ApplicationDocumentController extends Controller
{
    /**
     * Display a listing of appointment documents.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.application-documents.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = ApplicationDocument::with('customer', 'documentType')->select([
            'application_documents.id',
            'application_documents.customer_id',
            'application_documents.document_type_id',
            'application_documents.is_submitted',
            'application_documents.file_path',
            'application_documents.is_verified',
            'application_documents.created_at'
        ])->where('application_documents.is_verified', 0);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('application_documents.created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('customer_name', function ($row) {
                $firstName = $row->customer->first_name ?? '';
                $lastName  = $row->customer->last_name ?? '';
                $email     = $row->customer->email ?? '';

                $fullName = trim($firstName . ' ' . $lastName);

                return '
                    <div>
                        <div class="font-semibold text-gray-900">' . ($fullName ?: '-') . '</div>
                        <div class="text-xs text-gray-500">' . $email . '</div>
                    </div>
                ';
            })

            ->addColumn('customer_mobile', function ($row) {
                return $row->customer->mobile_number ?? '-';
            })

            ->addColumn('document_type_name', function ($row) {
                return $row->documentType->name;
            })

            ->editColumn('is_verified', function ($row) {
                if ($row->is_verified == '0') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-red-100 text-red-800">Unverified</span>';
                }
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('mobile_number', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('document_type_name', function ($query, $keyword) {
                $query->whereHas('documentType', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('document', function ($row) {

                $fileUrl = $row->file_path ? asset('storage/' . $row->file_path) : '#';

                return '
                    <!-- View Document -->
                    <a href="' . $fileUrl . '" target="_blank"
                        class="inline-flex items-center text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        View File
                    </a>
                ';
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                    
                        <!-- View -->
                        <a href="' . route('admin.documents.toggleVerify', ['id' => $row->id, 'redirect' => 'list']) . '" 
                            class="text-green-600 hover:text-green-900 flex" title="Verify">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 flex-shrink-0"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Click to Verify
                        </a>

                        <!-- Delete -->
                        <form action="' . route('admin.application-documents.destroy', $row->id) . '" method="POST" class="inline">
                            <input type="hidden" name="redirect" value="list">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="button" 
                                onclick="confirmDelete(\'' . $row->documentType->name . ' Document\', this.form)"
                                class="text-red-600 hover:text-red-900" 
                                title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                ';
            })

            ->rawColumns(['customer_name', 'is_verified', 'document', 'actions'])

            ->make(true);
    }

    /**
     * Store a newly created application document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'document_type_id' => 'required|exists:document_types,id',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'redirect' => 'nullable|string',
        ]);

        $redirectUrl = $request->input('redirect')
            ?? route('admin.customers.show', $request->customer_id) . '#documents';

        if ($validator->fails()) {
            return redirect($redirectUrl)
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $exists = ApplicationDocument::where('customer_id', $validated['customer_id'])
            ->where('document_type_id', $validated['document_type_id'])
            ->exists();

        if ($exists) {
            return redirect($redirectUrl)
                ->with('error', 'This document type already uploaded.')
                ->withInput();
        }

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();

        $filePath = $file->storeAs(
            'customer-documents/' . $validated['customer_id'],
            $fileName,
            'public'
        );

        ApplicationDocument::create([
            'customer_id' => $validated['customer_id'],
            'document_type_id' => $validated['document_type_id'],
            'is_submitted' => true,
            'file_path' => $filePath,
        ]);

        return redirect($redirectUrl)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Remove the specified application document from storage.
     *
     * @param  \App\Models\ApplicationDocument  $applicationDocument
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ApplicationDocument $applicationDocument)
    {
        $customerId = $applicationDocument->customer_id;

        // Delete the file from storage
        if ($applicationDocument->file_path && Storage::disk('public')->exists($applicationDocument->file_path)) {
            Storage::disk('public')->delete($applicationDocument->file_path);
        }

        // Delete the record
        $applicationDocument->delete();

        if ($request->redirect === 'list') {
            return redirect()
                ->route('admin.application-documents.index')
                ->with('success', 'Document deleted successfully.');
        }

        // Redirect back to the customer detail page with documents tab active
        return redirect()->route('admin.customers.show', $customerId)
            ->with('success', 'Document deleted successfully.')
            ->withFragment('documents');
    }

    public function updateAll(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:0,1'
        ]);

        ApplicationDocument::where('customer_id', $request->customer_id)
            ->update([
                'is_verified' => $request->status
            ]);

        $customer = Customer::find($request->customer_id);

        if ($request->status == 1 && $customer->registration_step != 5) {
            $customer->update([
                'registration_step' => 5
            ]);
        }

        $message = $request->status == 1
            ? 'All documents verified successfully'
            : 'All documents unverified successfully';

        return redirect()
            ->route('admin.customers.show', $request->customer_id)
            ->with('success', $message)
            ->withFragment('documents');
    }

    public function toggleVerify(Request $request, $id)
    {
        $doc = ApplicationDocument::findOrFail($id);

        $doc->is_verified = $doc->is_verified ? 0 : 1;
        $doc->save();

        $customer = Customer::find($doc->customer_id);

        $allVerified = ApplicationDocument::where('customer_id', $doc->customer_id)
            ->where('is_verified', 0)
            ->count() === 0;

        if ($allVerified && $customer->registration_step != 5) {
            $customer->update([
                'registration_step' => 5
            ]);
        }

        $message = $doc->is_verified
            ? $doc->documentType->name . ' verified successfully'
            : $doc->documentType->name . ' unverified successfully';

        if ($request->get('redirect') === 'list') {
            return redirect()
                ->route('admin.application-documents.index')
                ->with('success', $message);
        }

        return redirect()
            ->route('admin.customers.show', $customer->id)
            ->with('success', $message)
            ->withFragment('documents');
    }
}
