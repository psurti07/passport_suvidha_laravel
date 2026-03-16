<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $query = DocumentType::query();

        // Handle search
        if (request()->has('search') && !empty(request('search'))) {
            $searchTerm = request('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Handle sorting
        $sortBy = request('sort_by', 'id');
        $sortDirection = request('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Get paginated results
        $documentTypes = $query->paginate(request('per_page', 10));

        return view('admin.document-types.index', compact('documentTypes'));
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

    /**
     * Display the specified document type.
     *
     * @param  \App\Models\DocumentType  $documentType
     * @return \Illuminate\Http\Response
     */
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
        $documentType->delete();

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Document type deleted successfully.');
    }
} 