<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteLink;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class WebsiteLinkController extends Controller
{
    public function index()
    {
        return view('admin.website-links.index');
    }

    public function data()
    {
        $query = WebsiteLink::query()
            ->select([
                'id',
                'link_type',
                'title',
                'link',
                'short_link',
                'status',
                'created_at',
            ])
            ->latest('created_at');

        return DataTables::eloquent($query)

            // Date column
            ->addColumn('date', function ($websiteLink) {
                return $websiteLink->created_at
                    ? $websiteLink->created_at->format('d-m-Y')
                    : '';
            })

            // Short Link column
            ->addColumn('short_url', function ($websiteLink) {
                return e($websiteLink->short_link ?? 'N/A');
            })

            // Original Link column: open directly in a new tab
            ->addColumn('view_button', function ($websiteLink) {
                return '<a href="' . e($websiteLink->link) . '" target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors text-sm">

        <svg xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2">

            <path stroke-linecap="round"
                stroke-linejoin="round"
                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>

        Visit Link
    </a>';
            })

            // Type badge
            ->addColumn('link_type_badge', function ($websiteLink) {
                $type = $websiteLink->link_type;

                $config = [
                    'website' => [
                        'label' => 'Website',
                        'color' => 'bg-blue-100 text-blue-700',
                    ],
                    'social_media' => [
                        'label' => 'Social Media',
                        'color' => 'bg-purple-100 text-purple-700',
                    ],
                ];

                $badge = $config[$type] ?? [
                    'label' => ucfirst(str_replace('_', ' ', $type)),
                    'color' => 'bg-gray-100 text-gray-700',
                ];

                return '<span class="inline-flex items-center px-3 py-1
        font-bold rounded text-xs ' . $badge['color'] . '">'
                    . e($badge['label']) .
                    '</span>';
            })

            // Status badge
            ->addColumn('status_badge', function ($websiteLink) {
                $config = [
                    'active' => [
                        'label' => 'Active',
                        'color' => 'bg-green-100 text-green-700',
                    ],
                    'inactive' => [
                        'label' => 'Deactive',
                        'color' => 'bg-red-100 text-red-700',
                    ],
                ];

                $badge = $config[$websiteLink->status] ?? [
                    'label' => ucfirst($websiteLink->status),
                    'color' => 'bg-gray-100 text-gray-700',
                ];

                return '<span class="inline-flex items-center px-3 py-1
        font-bold rounded text-xs ' . $badge['color'] . '">'
                    . e($badge['label']) .
                    '</span>';
            })

            // Edit and Delete actions
            ->addColumn('actions', function ($websiteLink) {
                return '
        <div class="flex items-center gap-2">

            <button type="button"
                class="edit-link group inline-flex items-center justify-center
                h-9 w-9 rounded-lg border border-yellow-200 bg-yellow-50
                text-yellow-600 hover:bg-yellow-100 hover:border-yellow-600
                transition-all duration-200 shadow-sm"
                title="Edit"
                data-id="' . $websiteLink->id . '">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>

            <button type="button"
                class="delete-link group inline-flex items-center justify-center
                h-9 w-9 rounded-lg border border-red-200 bg-red-50
                text-red-600 hover:bg-red-100 hover:border-red-600
                transition-all duration-200 shadow-sm"
                title="Delete"
                data-id="' . $websiteLink->id . '"
                data-title="' . e($websiteLink->title) . '">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>

        </div>

            ';
            })->filterColumn('status_badge', function ($query, $keyword) {
                $keyword = strtolower(trim($keyword));

                if (
                    str_contains('deactive', $keyword) ||
                    str_contains('inactive', $keyword)
                ) {
                    $query->where('website_links.status', 'inactive');
                } elseif (str_contains('active', $keyword)) {
                    $query->where('website_links.status', 'active');
                } else {
                    $query->where(
                        'website_links.status',
                        'LIKE',
                        "%{$keyword}%"
                    );
                }
            })

            // Render generated HTML columns
            ->rawColumns([
                'link_type_badge',
                'short_url',
                'view_button',
                'status_badge',
                'actions',
            ])
            ->toJson();
    }
    public function store(Request $request)
    {
        $validated = $this->validateWebsiteLink($request);

        // Save the short link entered manually in the form.
        $websiteLink = WebsiteLink::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Website link created successfully.',
            'data' => $websiteLink,
            'short_url' => $websiteLink->short_link,
        ], 201);
    }

    public function edit(WebsiteLink $websiteLink)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'id' => $websiteLink->id,
                'link_type' => $websiteLink->link_type,
                'title' => $websiteLink->title,
                'link' => $websiteLink->link,
                'short_link' => $websiteLink->short_link,
                'short_url' => $websiteLink->short_link,
                'status' => $websiteLink->status,
            ],
        ]);
    }

    public function update(Request $request, WebsiteLink $websiteLink)
    {
        $validated = $this->validateWebsiteLink(
            $request,
            $websiteLink
        );

        $websiteLink->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Website link updated successfully.',
            'data' => $websiteLink,
        ]);
    }

    public function destroy(WebsiteLink $websiteLink)
    {
        $websiteLink->delete();

        return response()->json([
            'status' => true,
            'message' => 'Website link deleted successfully.',
        ]);
    }

    private function validateWebsiteLink(
        Request $request,
        ?WebsiteLink $websiteLink = null
    ): array {
        return $request->validate([
            'link_type' => [
                'required',
                Rule::in(['website', 'social_media']),
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'link' => [
                'required',
                'url',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $scheme = strtolower(
                        (string) parse_url(
                            $value,
                            PHP_URL_SCHEME
                        )
                    );

                    if (!in_array($scheme, ['http', 'https'], true)) {
                        $fail(
                            'The link must start with HTTP or HTTPS.'
                        );
                    }
                },
            ],

            'short_link' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);
    }
}
