<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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
    public function index(Request $request)
    {
        // Prevent staff from accessing the user list
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get per_page value from request, default to 10, validate
        $perPageOptions = [10, 25, 50, 100];
        $perPage = $request->input('per_page', 10); // Default to 10
        if (!in_array($perPage, $perPageOptions)) {
            $perPage = 10; // Reset to default if invalid value is provided
        }

        $query = User::where('role', 'staff')->with('creator');

        // Apply filters
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('mobile', 'like', "%{$request->search}%")
                  ->orWhere('city', 'like', "%{$request->search}%")
                  ->orWhere('state', 'like', "%{$request->search}%");
            });
        }

        // Apply sorting
        $sortField = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['id', 'created_at', 'name', 'email', 'mobile', 'pincode', 'city', 'state'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $direction === 'asc' ? 'asc' : 'desc');
        }

        $users = $query->paginate($perPage)->withQueryString();
        
        // Pass $perPage to the view for the dropdown selection
        return view('admin.users.index', compact('users', 'perPage'));
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

    public function export(Request $request)
    {
        // Add admin check for export
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Start query - No role filter applied here, assuming all users are exportable based on filters
        $query = User::query(); // Use base query

        // Apply filters (matching index filtering logic)
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  // Add other fields searched in index if necessary
                  // ->orWhere('mobile', 'like', "%{$search}%") // Uncomment if needed
                  // ->orWhere('city', 'like', "%{$search}%")   // Uncomment if needed
                  // ->orWhere('state', 'like', "%{$search}%")  // Uncomment if needed
                  ;
            });
        }

        // Apply sorting (matching index sorting logic)
        $sortField = $request->input('sort_by', 'id'); // Match param name from view
        $direction = $request->input('sort_direction', 'desc'); // Match param name from view

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['id', 'created_at', 'name', 'email', 'updated_at']; // Update allowed fields based on view columns
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('id', 'desc'); // Default sort if invalid field provided
        }

        $users = $query->get(); // Fetch all matching users

        // Handle export based on type
        switch($request->input('type', 'excel')) {
            case 'excel':
                // Ensure UsersExport columns match the view
                return ExcelFacade::download(new UsersExport($users), 'users.xlsx');
            case 'csv':
                // Specify CSV format for Maatwebsite/Excel v3+
                // Ensure UsersExport columns match the view
                return ExcelFacade::download(new UsersExport($users), 'users.csv', \Maatwebsite\Excel\Excel::CSV);
            case 'pdf':
                // Ensure 'exports.users' view columns match the main view
                try {
                    return Pdf::loadView('exports.users', ['users' => $users])
                             ->download('users.pdf');
                } catch (\Exception $e) {
                    Log::error("PDF Export Error: " . $e->getMessage());
                    return back()->with('error', 'Could not generate PDF. View file might be missing or invalid.');
                }
            default:
                return back()->with('error', 'Invalid export type');
        }
    }
}
