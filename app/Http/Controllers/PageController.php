<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
            // Skip very dark shades that might cause dark overlay
            if (in_array($key, [800, 900, 950])) {
                continue;
            }
            $colors = $colors."--primary-{$key}: {$color};";
        }
        return view('cms.page', ['page' => $page, 'colors' => $colors]);
    }

    /**
     * Handle contact form submission.
     */
    public function submitContactForm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'email_to' => 'required|email|max:255',
        ]);

        try {
            // Send email to the specified recipient
            Mail::raw(
                "Name: {$validated['name']}\nEmail: {$validated['email']}\nSubject: {$validated['subject']}\n\nMessage:\n{$validated['message']}",
                function ($message) use ($validated) {
                    $message->to($validated['email_to'])
                            ->subject($validated['subject'] ?: 'Kontaktformular Nachricht')
                            ->replyTo($validated['email']);
                }
            );

            return back()->with('success', 'Ihre Nachricht wurde erfolgreich versendet!');
        } catch (\Exception $e) {
            return back()->with('error', 'Es gab einen Fehler beim Versenden Ihrer Nachricht. Bitte versuchen Sie es später erneut.');
        }
    }
}
