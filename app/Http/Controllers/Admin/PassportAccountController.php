<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PassportAccount;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PassportAccountController extends Controller
{
    protected BrevoMailService $brevoMailService;

    public function __construct(BrevoMailService $brevoMailService)
    {
        $this->brevoMailService = $brevoMailService;
    }

    public function store(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
            ],

            'password' => [
                'required',
                'string',
                'max:255',
            ],

            'action' => [
                'required',
                'in:save,send_email',
            ],
        ]);

        try {

            DB::beginTransaction();

            $passportAccount = PassportAccount::firstOrNew([
                'customer_id' => $customer->id,
            ]);

            $passportAccount->username = $validated['username'];

            $passportAccount->password = Crypt::encryptString(
                $validated['password']
            );

            $passportAccount->is_email = false;

            $passportAccount->save();

            DB::commit();

            if ($validated['action'] === 'save') {

                return redirect()
                    ->route('admin.customers.show', [
                        'customer' => $customer->id,
                    ])
                    ->withFragment('passport-credential')
                    ->with(
                        'success',
                        'Passport credentials saved successfully.'
                    );
            }

            if (empty($customer->email)) {

                return redirect()
                    ->route('admin.customers.show', [
                        'customer' => $customer->id,
                    ])
                    ->withFragment('passport-credential')
                    ->with(
                        'error',
                        'Customer email address is not available.'
                    );
            }

            $password = Crypt::decryptString(
                $passportAccount->password
            );

            $passportCredentials = view(
                'emails.passport-credentials',
                [
                    'customer' => $customer,
                    'username' => $passportAccount->username,
                    'password' => $password,
                ]
            )->render();

            $this->brevoMailService->sendBrevoHtmlMail(
                $customer->email,
                $customer->full_name,
                'Your Passport Seva Login Credentials',
                $passportCredentials
            );

            $passportAccount->is_email = true;
            $passportAccount->save();


            return redirect()
                ->route('admin.customers.show', [
                    'customer' => $customer->id,
                ])
                ->withFragment('passport-credential')
                ->with(
                    'success',
                    'Email sent successfully.'
                );
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Passport credential operation failed.',
                [
                    'customer_id' => $customer->id,
                    'action' => $request->input('action'),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            return redirect()
                ->route('admin.customers.show', [
                    'customer' => $customer->id,
                ])
                ->withFragment('passport-credential')
                ->withInput()
                ->with(
                    'error',
                    'Unable to process Passport credentials. Please try again.'
                );
        }
    }

    public function show($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        $passportAccount = PassportAccount::where(
            'customer_id',
            $customer->id
        )->first();

        if ($passportAccount && $passportAccount->password) {
            try {
                $passportAccount->password = Crypt::decryptString(
                    $passportAccount->password
                );
            } catch (\Throwable $e) {

                Log::error(
                    'Unable to decrypt Passport password.',
                    [
                        'customer_id' => $customer->id,
                        'passport_account_id' => $passportAccount->id,
                        'error' => $e->getMessage(),
                    ]
                );

                $passportAccount->password = '';
            }
        }

        return view(
            'admin.customers.passport-account',
            [
                'customer' => $customer,
                'passportAccount' => $passportAccount,
            ]
        );
    }

    public function destroy($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        $passportAccount = PassportAccount::where(
            'customer_id',
            $customer->id
        )->first();

        if (!$passportAccount) {
            return redirect()
                ->route('admin.customers.show', [
                    'customer' => $customer->id,
                    'tab' => 'passport-credential',
                ])
                ->with(
                    'error',
                    'Passport account not found.'
                );
        }

        $passportAccount->delete();

        return redirect()
            ->route('admin.customers.show', [
                'customer' => $customer->id,
                'tab' => 'passport-credential',
            ])
            ->with(
                'success',
                'Passport credentials deleted successfully.'
            );
    }
}
