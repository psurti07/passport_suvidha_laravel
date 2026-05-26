<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteOption;
use Yajra\DataTables\Facades\DataTables;
use App\Services\SmsService;

class SmsController extends Controller
{
    public function index()
    {
        return view('admin.sms.index');
    }

    public function data(Request $request)
    {
        $from = $request->from_date ?? now()->subDays(1)->format('Y-m-d');
        $to   = $request->to_date ?? now()->format('Y-m-d');

        $query = SiteOption::select([
            'id',
            'option_key',
            'option_value',
            'created_at',
            'updated_at',
        ])->whereIn('option_key', [
            'otp-sms',
            'complete-process-sms',
            'application-submitted-sms',
            'login-otp-sms',
            'welcome-sms',
            'payment-failed-sms',
            'account-sms',
        ]);

        if ($request->filled('from_date') && $request->filled('to_date')) {

            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->editColumn('option_key', function ($row) {

                $types = [
                    'otp-sms' => 'OTP SMS',
                    'complete-process-sms' => 'Complete Process SMS',
                    'application-submitted-sms' => 'Application Submitted SMS',
                    'login-otp-sms' => 'Login OTP SMS',
                    'welcome-sms' => 'Welcome SMS',
                    'payment-failed-sms' => 'Payment Failed SMS',
                    'account-sms' => 'Account SMS',
                ];

                return $types[$row->option_key] ?? ucwords(str_replace('-', ' ', $row->option_key));
            })

            ->editColumn('option_value', function ($row) {

                return $row->option_value
                    ? $row->option_value
                    : '<span class="text-gray-400 italic">No SMS Added</span>';
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

                        <!-- Send Test SMS -->
                        <a href="' . route('admin.sms.show', $row->id) . '" 
                            class="text-green-600 hover:text-green-900"  title="Send Test SMS">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send h-4 w-4"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path><path d="m21.854 2.147-10.94 10.939"></path></svg>
                        </a>

                        <!-- Edit -->
                        <a href="' . route('admin.sms.edit', $row->id) . '" 
                            class="text-yellow-600 hover:text-yellow-900" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                    </div>
                ';
            })

            ->rawColumns(['option_value', 'actions'])

            ->make(true);
    }

    public function show(SiteOption $sms)
    {
        $smsTypes = [
            'otp-sms' => 'OTP SMS',
            'complete-process-sms' => 'Complete Process SMS',
            'application-submitted-sms' => 'Application Submitted SMS',
            'login-otp-sms' => 'Login OTP SMS',
            'welcome-sms' => 'Welcome SMS',
            'payment-failed-sms' => 'Payment Failed SMS',
            'account-sms' => 'Account SMS',
        ];
        $smsType = $smsTypes[$sms->option_key] ?? ucwords(str_replace('-', ' ', $sms->option_key));
        return view('admin.sms.show', compact('sms', 'smsType'));
    }

    public function edit(SiteOption $sms)
    {
        $smsTypes = [
            'otp-sms' => 'OTP SMS',
            'complete-process-sms' => 'Complete Process SMS',
            'application-submitted-sms' => 'Application Submitted SMS',
            'login-otp-sms' => 'Login OTP SMS',
            'welcome-sms' => 'Welcome SMS',
            'payment-failed-sms' => 'Payment Failed SMS',
            'account-sms' => 'Account SMS',
        ];

        $smsType = $smsTypes[$sms->option_key] ?? $sms->option_key;

        return view('admin.sms.edit', compact('sms', 'smsType'));
    }

    public function update(Request $request, SiteOption $sms)
    {
        $validated = $request->validate(
            [
                'option_value' => 'required|string',
            ],
            [
                'option_value.required' => 'The sms message field is required.',
            ]
        );

        $sms->update($validated);

        return redirect()->route('admin.sms.index')
            ->with('success', 'SMS updated successfully.');
    }

    public function sendTest(Request $request)
    {
        $validated = $request->validate(
            [
                'mobile_number' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
                'option_value' => ['required'],
            ],
            [
                'mobile_number.required' => 'Mobile number is required.',
                'mobile_number.regex' => 'Please enter valid mobile number.',
            ]
        );

        $mobile = $validated['mobile_number'];

        $message = $validated['option_value'];

        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $message = str_replace('{#var_otp#}', $otp, $message);
        $message = str_replace('{#var_method#}', 'Test', $message);

        $smsService = new SmsService();

        $response = $smsService->sendSms($mobile, $message);

        if ($response['success']) {

            return back()->with(
                'success',
                'Test SMS sent successfully to ' . $mobile
            );
        }

        return back()->with(
            'error',
            $response['message']
        );
    }
}
