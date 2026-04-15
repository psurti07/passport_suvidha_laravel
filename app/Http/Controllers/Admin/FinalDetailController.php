<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FinalDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class FinalDetailController extends Controller
{
    public function index()
    {
        return view('admin.final-details.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = FinalDetail::with('customer', 'uploader')->select([
            'final_details.id',
            'final_details.customer_id',
            'final_details.file_path',
            'final_details.upload_date',
            'final_details.uploaded_by',
            'final_details.is_approved',
            'final_details.approved_date',
            'final_details.approved_by_role',
            'final_details.approved_by',
        ])

        ->whereBetween('final_details.upload_date', [
            $from . ' 00:00:00',
            $to . ' 23:59:59'
        ]);

        if ($request->filled('is_approved')) {
            $query->where('final_details.is_approved', $request->is_approved);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('customer_name', function ($row) {
                $firstName = $row->customer->first_name ?? '';
                $lastName  = $row->customer->last_name ?? '';

                $fullName = trim($firstName . ' ' . $lastName);

                return '
                    <div>
                        <div class="text-gray-900">'.($fullName ?: '-').'</div>
                    </div>
                ';
            })

            ->addColumn('uploaded_by_name', function ($row) {
                return $row->uploader->name ?? 'System';
            }) 

            ->addColumn('is_approved', function ($row) {
                return $row->is_approved ? 1 : 0;
            })

            ->addColumn('approved_by_name', function ($row) {
                return $row->approver_name;
            }) 

            ->editColumn('upload_date', function ($row) {
                return $row->upload_date->format('d M Y, h:i A');
            })
            
            ->editColumn('is_approved', function ($row) {
                if ($row->is_approved == '1') {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">Approved</span>';
                }  else {
                    return '<span class="inline-flex px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">Pending</span>';
                }
            })

            ->editColumn('approved_date', function ($row) {
                return $row->approved_date 
                    ? $row->approved_date->format('d M Y, h:i A') 
                    : 'N/A';
            })

            ->filterColumn('customer_name', function($query, $keyword) {
                $query->whereHas('customer', function($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('document', function ($row) {

                $fileUrl = $row->file_path ? asset('storage/' . $row->file_path) : '#';

                return '
                    <!-- View Document -->
                    <a href="'.$fileUrl.'" target="_blank"
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
                $html = '
                    <div class="flex items-center gap-2">
                    
                        <!-- View -->
                        <a href="'.route('admin.final-details.show', $row->id).'" 
                            class="text-blue-600 hover:text-blue-900" title="View">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <!-- Edit -->
                        <a href="'.route('admin.final-details.edit', $row->id).'" 
                            class="text-yellow-600 hover:text-yellow-900" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                    ';
                if (!$row->is_approved) {
                    $html .= '
                        <form action="'.route('admin.final-details.approve', $row->id).'"
                            method="POST" class="inline">
                            '.csrf_field().'
                            '.method_field('PATCH').'
                            <button type="submit"
                                class="text-green-600 hover:text-green-900 flex"
                                title="Approve">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Approve
                            </button>
                        </form>
                    ';
                } else {
                    $html .= '
                        <form action="'.route('admin.final-details.unapprove', $row->id).'"
                            method="POST" class="inline">
                            '.csrf_field().'
                            '.method_field('PATCH').'
                            <button type="submit"
                                class="text-yellow-600 hover:text-yellow-900 flex"
                                title="Unapprove">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Unapprove
                            </button>
                        </form>
                    ';
                }

                $html .= '</div>';

                return $html;
            })

        ->rawColumns(['customer_name', 'is_approved', 'document', 'actions'])

        ->make(true);
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
