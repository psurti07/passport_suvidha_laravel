<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FinalDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinalDetailController extends Controller
{
    public function index()
    {
        $query = FinalDetail::with(['customer', 'uploader']);

        // Handle search
        if (request()->has('search') && !empty(request('search'))) {
            $searchTerm = request('search');
            $query->whereHas('customer', function($q) use ($searchTerm) {
                $q->where('first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('mobile_number', 'like', '%' . $searchTerm . '%');
            });
        }

        // Handle approval status filter
        if (request()->has('approval_status') && !empty(request('approval_status'))) {
            $status = request('approval_status') === 'approved';
            $query->where('is_approved', $status);
        }

        // Handle sorting
        $sortBy = request('sort_by', 'id');
        $sortDirection = request('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Get paginated results
        $finalDetails = $query->paginate(request('per_page', 10));

        return view('admin.final-details.index', compact('finalDetails'));
    }

    public function create()
    {
        abort(403, 'Create functionality has been disabled');
    }

    public function store(Request $request)
    {
        abort(403, 'Create functionality has been disabled');
    }

    public function show(FinalDetail $finalDetail)
    {
        return view('admin.final-details.show', compact('finalDetail'));
    }

    public function edit(FinalDetail $finalDetail)
    {
        $customers = Customer::all();
        return view('admin.final-details.edit', compact('finalDetail', 'customers'));
    }

    public function update(Request $request, FinalDetail $finalDetail)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_approved' => 'boolean',
        ]);

        $data = [
            'customer_id' => $validated['customer_id'],
            'is_approved' => $request->has('is_approved'),
        ];

        // Handle file upload if a new file is provided
        if ($request->hasFile('file')) {
            // Delete old file if it exists
            if ($finalDetail->file_path && Storage::disk('public')->exists($finalDetail->file_path)) {
                Storage::disk('public')->delete($finalDetail->file_path);
            }
            
            $file = $request->file('file');
            $data['file_path'] = $file->store('final-details', 'public');
            $data['upload_date'] = now();
            $data['uploaded_by'] = auth()->id(); // Set current user as uploader
        }

        // Handle approval status changes
        if ($request->has('is_approved')) {
            $isApproved = (bool) $request->input('is_approved');
            
            // If approving and it wasn't approved before
            if ($isApproved && !$finalDetail->is_approved) {
                $data['approved_date'] = now();
                $data['approved_by_role'] = 'user'; // Staff/admin is approving
                $data['approved_by'] = auth()->id(); // Set current user as approver
            }
            
            // If un-approving
            if (!$isApproved && $finalDetail->is_approved) {
                $data['approved_date'] = null;
                $data['approved_by_role'] = null;
                $data['approved_by'] = null;
            }
        }

        $finalDetail->update($data);

        return redirect()->route('admin.final-details.index')
            ->with('success', 'Final detail updated successfully.');
    }

    public function destroy(FinalDetail $finalDetail)
    {
        abort(403, 'Delete functionality has been disabled');
    }

    public function approve(FinalDetail $finalDetail)
    {
        $finalDetail->update([
            'is_approved' => true,
            'approved_date' => now(),
            'approved_by_role' => 'user', // Staff/admin is approving
            'approved_by' => auth()->id(), // Set current user as approver
        ]);

        return redirect()->route('admin.final-details.index')
            ->with('success', 'Final detail approved successfully.');
    }

    public function unapprove(FinalDetail $finalDetail)
    {
        $finalDetail->update([
            'is_approved' => false,
            'approved_date' => null,
            'approved_by_role' => null,
            'approved_by' => null,
        ]);

        return redirect()->route('admin.final-details.index')
            ->with('success', 'Final detail approval removed successfully.');
    }
}
