<?php

namespace App\Http\Controllers;

use App\Exceptions\ImportConflictException;
use App\Http\Requests\ImportSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierImportExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use JsonException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierImportExportController extends Controller
{
    public function importForm(Supplier $supplier): View
    {
        return view('suppliers.import', compact('supplier'));
    }

    public function export(Supplier $supplier, SupplierImportExportService $service): StreamedResponse|JsonResponse
    {
        $data = $service->exportSupplier($supplier);
        $fileName = Str::slug($supplier->name) . '-supplier-export.json';

        return response()->streamDownload(
            fn () => print(json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)),
            $fileName,
            ['Content-Type' => 'application/json']
        );
    }

    public function import(ImportSupplierRequest $request, Supplier $supplier, SupplierImportExportService $service): RedirectResponse
    {
        try {
            $payload = json_decode($request->file('file')->get(), true, 512, JSON_THROW_ON_ERROR);
            $summary = $service->importToSupplier($supplier, $payload, $request->string('strategy')->toString());
        } catch (JsonException) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file must contain valid JSON.',
            ]);
        } catch (ImportConflictException $exception) {
            return back()
                ->withInput()
                ->with('error', 'Import rejected. Please review the conflict report below or choose another strategy.')
                ->with('conflict_report', $exception->conflicts());
        }

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Import completed successfully.')
            ->with('import_summary', $summary);
    }
}
