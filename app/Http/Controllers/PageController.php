<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Http\Request;
use function Pest\Laravel\json;

class PageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $slug)
    {
        $page = CmsPage::slug($slug)->published()->firstOrFail();

        $primary = \Filament\Support\Colors\Color::generatePalette('#3990b2');
        $colors = '';
        foreach ($primary as $key => $color) {
            $colors = $colors."--primary-{$key}: {$color};";
        }
        return view('cms.page', ['page' => $page, 'colors' => $colors]);
    }
}
