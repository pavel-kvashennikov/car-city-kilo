<?php

namespace Src\Parts\Infrastructure\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductImportController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Cabinet/Parts/Import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv,xls', 'max:10240'],
        ]);

        // TODO: Implement Excel/CSV import using maatwebsite/excel
        return back()->with('success', 'Файл принят в обработку. Импорт будет завершён в течение нескольких минут.');
    }
}
