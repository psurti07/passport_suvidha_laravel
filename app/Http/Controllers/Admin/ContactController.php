<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function index()
    {
        return view('admin.contact.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = Contact::select([
            'id',
            'full_name',
            'email',
            'mobile',
            'subject',
            'message',
            'created_at',
        ])
            ->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('name', function ($row) {
                return Str::title(strtolower($row->full_name));
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y, h:i A');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">

                        <!-- Delete -->
                        <form action="' . route('admin.contact.enquiry.destroy', $row->id) . '" method="POST" class="inline">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="button" 
                                onclick="confirmDelete(\'' . $row->full_name . ' Contact Enquiry\', this.form)"
                                class="group inline-flex items-center justify-center h-9 w-9 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:border-red-600 transition-all duration-200 shadow-sm" 
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

            ->rawColumns(['actions'])

            ->make(true);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return back()->with('success', 'Contact enquiry deleted successfully.');
    }
}
