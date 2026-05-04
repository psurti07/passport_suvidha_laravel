<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteOption;

class SiteOptionController extends Controller
{
    public function index()
    {
        return view('admin.site-options');
    }

    public function update(Request $request)
    {
        $request->validate([
            'option_key' => 'required',
            'option_label' => 'required',
            'option_value' => 'nullable'
        ]);

        SiteOption::updateOrCreate(
            ['option_key' => $request->option_key],
            ['option_value' => $request->option_value]
        );

        return redirect()->route('admin.site-options')
            ->with('success', $request->option_label . ' updated successfully.');
    }
}
