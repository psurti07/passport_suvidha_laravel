<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Maatwebsite\Excel\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of staff users.
     */
    // public function index(Request $request)
    // {
    //     // Prevent staff from accessing the user list
    //     if (!auth()->user()->isAdmin()) {
    //         abort(403, 'Unauthorized action.');
    //     }
        
    //     // Get per_page value from request, default to 10, validate
    //     $perPageOptions = [10, 25, 50, 100];
    //     $perPage = $request->input('per_page', 10); // Default to 10
    //     if (!in_array($perPage, $perPageOptions)) {
    //         $perPage = 10; // Reset to default if invalid value is provided
    //     }

    //     $query = User::where('role', 'staff')->with('creator');

    //     // Apply filters
    //     if ($request->filled('from_date')) {
    //         $query->whereDate('created_at', '>=', $request->from_date);
    //     }
    //     if ($request->filled('to_date')) {
    //         $query->whereDate('created_at', '<=', $request->to_date);
    //     }
    //     if ($request->filled('search')) {
    //         $query->where(function($q) use ($request) {
    //             $q->where('name', 'like', "%{$request->search}%")
    //               ->orWhere('email', 'like', "%{$request->search}%")
    //               ->orWhere('mobile', 'like', "%{$request->search}%")
    //               ->orWhere('city', 'like', "%{$request->search}%")
    //               ->orWhere('state', 'like', "%{$request->search}%");
    //         });
    //     }

    //     // Apply sorting
    //     $sortField = $request->input('sort', 'id');
    //     $direction = $request->input('direction', 'desc');
        
    //     // Validate sort field to prevent SQL injection
    //     $allowedSortFields = ['id', 'created_at', 'name', 'email', 'mobile', 'pincode', 'city', 'state'];
    //     if (in_array($sortField, $allowedSortFields)) {
    //         $query->orderBy($sortField, $direction === 'asc' ? 'asc' : 'desc');
    //     }

    //     $users = $query->paginate($perPage)->withQueryString();
        
    //     // Pass $perPage to the view for the dropdown selection
    //     return view('admin.users.index', compact('users', 'perPage'));
    // }
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.users.index');
    }

    public function data(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = User::with('creator')
            ->where('role', 'staff')
            ->select([
                'id',
                'name',
                'email',
                'is_active',
                'created_at',
                'updated_at',
            ])

            ->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59'
            ]);

        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('is_active', 1);
            }

            if ($request->status == 'inactive') {
                $query->where('is_active', 0);
            }
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->addColumn('status', function ($row) {
                return $row->is_active ? 1 : 0;
            })

            ->addColumn('created_by', function ($row) {
                return optional($row->creator)->name ?? '-';
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i:s');
            })

            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d/m/Y H:i:s');
            })

            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex items-center gap-2">
                    
                        <!-- View -->
                        <a href="'.route('admin.users.show', $row->id).'" 
                            class="text-blue-600 hover:text-blue-900" title="View User">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>

                        <!-- Edit -->
                        <a href="'.route('admin.users.edit', $row->id).'" 
                            class="text-yellow-600 hover:text-yellow-900" title="Edit User">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </a>

                        <!-- Delete -->
                        <form action="'.route('admin.users.destroy', $row->id).'" method="POST" class="inline">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="button" 
                                onclick="confirmDelete(\''.$row->name.' user\', this.form)"
                                class="text-red-600 hover:text-red-900" 
                                title="Delete User">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>

                    </div>
                ';
            })

            ->rawColumns(['actions'])

            ->make(true);
    }

    /**
     * Show the form for creating a new staff user.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        return view('admin.users.create');
    }

    /**
     * Store a newly created staff user.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Staff user created successfully.');
    }

    /**
     * Display the specified staff user.
     */
    public function show(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($user->role !== 'staff') {
            abort(404);
        }
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified staff user.
     */
    public function edit(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($user->role !== 'staff') {
            abort(404);
        }
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified staff user.
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($user->role !== 'staff') {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'is_active' => ['boolean'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active ?? $user->is_active,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Staff user updated successfully.');
    }

    /**
     * Remove the specified staff user.
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($user->role !== 'staff') {
            abort(404);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Staff user deleted successfully.');
    }

    // public function export(Request $request)
    // {
    //     // Add admin check for export
    //     if (!auth()->user()->isAdmin()) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     // Start query - No role filter applied here, assuming all users are exportable based on filters
    //     $query = User::query(); // Use base query

    //     // Apply filters (matching index filtering logic)
    //     if ($request->filled('from_date')) {
    //         $query->whereDate('created_at', '>=', $request->from_date);
    //     }
    //     if ($request->filled('to_date')) {
    //         $query->whereDate('created_at', '<=', $request->to_date);
    //     }
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function($q) use ($search) {
    //             $q->where('name', 'like', "%{$search}%")
    //               ->orWhere('email', 'like', "%{$search}%")
    //               // Add other fields searched in index if necessary
    //               // ->orWhere('mobile', 'like', "%{$search}%") // Uncomment if needed
    //               // ->orWhere('city', 'like', "%{$search}%")   // Uncomment if needed
    //               // ->orWhere('state', 'like', "%{$search}%")  // Uncomment if needed
    //               ;
    //         });
    //     }

    //     // Apply sorting (matching index sorting logic)
    //     $sortField = $request->input('sort_by', 'id'); // Match param name from view
    //     $direction = $request->input('sort_direction', 'desc'); // Match param name from view

    //     // Validate sort field to prevent SQL injection
    //     $allowedSortFields = ['id', 'created_at', 'name', 'email', 'updated_at']; // Update allowed fields based on view columns
    //     if (in_array($sortField, $allowedSortFields)) {
    //         $query->orderBy($sortField, $direction === 'asc' ? 'asc' : 'desc');
    //     } else {
    //         $query->orderBy('id', 'desc'); // Default sort if invalid field provided
    //     }

    //     $users = $query->get(); // Fetch all matching users

    //     // Handle export based on type
    //     switch($request->input('type', 'excel')) {
    //         case 'excel':
    //             // Ensure UsersExport columns match the view
    //             return ExcelFacade::download(new UsersExport($users), 'users.xlsx');
    //         case 'csv':
    //             // Specify CSV format for Maatwebsite/Excel v3+
    //             // Ensure UsersExport columns match the view
    //             return ExcelFacade::download(new UsersExport($users), 'users.csv', \Maatwebsite\Excel\Excel::CSV);
    //         case 'pdf':
    //             // Ensure 'exports.users' view columns match the main view
    //             try {
    //                 return Pdf::loadView('exports.users', ['users' => $users])
    //                          ->download('users.pdf');
    //             } catch (\Exception $e) {
    //                 Log::error("PDF Export Error: " . $e->getMessage());
    //                 return back()->with('error', 'Could not generate PDF. View file might be missing or invalid.');
    //             }
    //         default:
    //             return back()->with('error', 'Invalid export type');
    //     }
    // }
}
