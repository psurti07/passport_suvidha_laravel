<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DocumentTypeController extends Controller
{
    public function index()
    {
        return view('admin.document-types.index');
    }

    public function data(Request $request)
    {
        $query = DocumentType::select([
            'id',
            'name',
            'description',
            'is_mandatory',
            'created_at',
            'updated_at',
        ]);

        if ($request->filled('from_date') && $request->filled('to_date')) {

            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        if ($request->filled('is_mandatory')) {
            $query->where('is_mandatory', $request->is_mandatory);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('is_mandatory', function ($row) {
                if ($row->is_mandatory == '1') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Mandatory</span>';
                } else {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">Optional</span>';
                }
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d M Y, h:i A');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                    
                        <!-- View -->
                        <a href="' . route('admin.document-types.show', $row->id) . '" 
                            class="text-blue-600 hover:text-blue-900" title="View">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <!-- Edit -->
                        <a href="' . route('admin.document-types.edit', $row->id) . '" 
                            class="text-yellow-600 hover:text-yellow-900" title="Edit">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        <!-- Delete -->
                        <form action="' . route('admin.document-types.destroy', $row->id) . '" method="POST" class="inline">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="button" 
                                onclick="confirmDelete(\'' . $row->name . ' Document Type\', this.form)"
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

            ->rawColumns(['is_mandatory', 'actions'])

            ->make(true);
    }

    public function create()
    {
        return view('admin.document-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:document_types',
            'description' => 'nullable|string',
            'is_mandatory' => 'boolean',
        ]);

        DocumentType::create($validated);

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Document type created successfully.');
    }

    public function show(DocumentType $documentType)
    {
        return view('admin.document-types.show', compact('documentType'));
    }

    public function edit(DocumentType $documentType)
    {
        return view('admin.document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name,' . $documentType->id,
            'description' => 'nullable|string',
            'is_mandatory' => 'boolean',
        ]);

        $documentType->update($validated);

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Document type updated successfully.');
    }

    public function destroy(DocumentType $documentType)
    {
        $isUsed = ApplicationDocument::where(
            'document_type_id',
            $documentType->id
        )->exists();

        if ($isUsed) {
            return redirect()
                ->route('admin.document-types.index')
                ->with('error', 'This document type is already used.');
        }

        $documentType->delete();

        return redirect()
            ->route('admin.document-types.index')
            ->with('success', 'Document type deleted successfully.');
    }
}
