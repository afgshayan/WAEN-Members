<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PersonController extends Controller
{
    /** Allowed sort columns — whitelist prevents SQL injection via ORDER BY */
    private const SORT_COLUMNS = ['name', 'last_name', 'province', 'country', 'email', 'phone', 'education', 'event_name'];

    /** Default items per page */
    private const PER_PAGE = 100;

    // ---------------------------------------------------------------------------
    // Index — list, search, sort, paginate
    // ---------------------------------------------------------------------------

    public function index(Request $request)
    {
        $request->validate([
            'search'     => 'nullable|string|max:200',
            'province'   => 'nullable|string|max:100',
            'city'       => 'nullable|string|max:100',
            'country'    => 'nullable|string|max:100',
            'education'  => 'nullable|string|max:100',
            'event_name' => 'nullable|string|max:150',
            'gender'     => 'nullable|string|in:Male,Female,Other',
            'sort'       => 'nullable|string|in:' . implode(',', self::SORT_COLUMNS),
            'direction'  => 'nullable|string|in:asc,desc',
            'per_page'   => 'nullable|integer|in:25,50,100,200,500,1000',
        ]);

        $search     = $request->input('search');
        $province   = $request->input('province');
        $country    = $request->input('country');
        $education  = $request->input('education');
        $eventName  = $request->input('event_name');
        $gender     = $request->input('gender');
        $sort       = in_array($request->input('sort'), self::SORT_COLUMNS)
                        ? $request->input('sort')
                        : 'name';
        $direction  = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $defaultPerPage = (int) Setting::get('per_page_default', self::PER_PAGE);
        $perPage    = (int) $request->input('per_page', $defaultPerPage);

        $persons = Person::query()
            ->search($search)
            ->byProvince($province)
            ->byCountry($country)
            ->byEducation($education)
            ->byEvent($eventName)
            ->byGender($gender)
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        // Dropdown lists for filters
        $pluck = fn($col) => Person::select($col)
            ->whereNotNull($col)->where($col, '!=', '')
            ->distinct()->orderBy($col)->pluck($col);

        $provinces  = $pluck('province');
        $countries  = $pluck('country');
        $educations = $pluck('education');
        $events     = $pluck('event_name');

        $totalCount = Person::count();

        return view('persons.index', compact(
            'persons', 'provinces', 'countries', 'educations', 'events',
            'search', 'province', 'country', 'education', 'eventName', 'gender',
            'sort', 'direction', 'perPage', 'totalCount'
        ));
    }

    // ---------------------------------------------------------------------------
    // Create form
    // ---------------------------------------------------------------------------

    public function create()
    {
        if (auth()->user()->isViewer()) abort(403, 'Viewers cannot create records.');
        return view('persons.create');
    }

    // ---------------------------------------------------------------------------
    // Store — save a new person
    // ---------------------------------------------------------------------------

    public function store(Request $request)
    {
        if (auth()->user()->isViewer()) abort(403, 'Viewers cannot create records.');

        $validated = $request->validate(
            Person::validationRules(),
            Person::validationMessages()
        );

        Person::create($validated);

        return redirect()->route('persons.index')
            ->with('success', 'Record created successfully.');
    }

    // ---------------------------------------------------------------------------
    // Show — view single record
    // ---------------------------------------------------------------------------

    public function show(Person $person)
    {
        return view('persons.show', compact('person'));
    }

    // ---------------------------------------------------------------------------
    // Edit form
    // ---------------------------------------------------------------------------

    public function edit(Person $person)
    {
        if (auth()->user()->isViewer()) abort(403, 'Viewers cannot edit records.');
        return view('persons.edit', compact('person'));
    }

    // ---------------------------------------------------------------------------
    // Update — save changes to existing person
    // ---------------------------------------------------------------------------

    public function update(Request $request, Person $person)
    {
        if (auth()->user()->isViewer()) abort(403, 'Viewers cannot edit records.');

        $validated = $request->validate(
            Person::validationRules(true, $person->id),
            Person::validationMessages()
        );

        $person->update($validated);

        return redirect()->route('persons.index')
            ->with('success', 'Record updated successfully.');
    }

    // ---------------------------------------------------------------------------
    // Destroy — soft-delete
    // ---------------------------------------------------------------------------

    public function destroy(Person $person)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can delete records.');
        }

        $person->forceDelete();

        return redirect()->route('persons.index')
            ->with('success', 'Record deleted successfully.');
    }

    // ---------------------------------------------------------------------------
    // Bulk delete
    // ---------------------------------------------------------------------------

    public function bulkDestroy(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can delete records.');
        }

        $request->validate([
            'ids'   => 'required|array|min:1|max:500',
            'ids.*' => 'required|integer|exists:persons,id',
        ]);

        Person::whereIn('id', $request->ids)->forceDelete();

        return redirect()->route('persons.index')
            ->with('success', count($request->ids) . ' records deleted successfully.');
    }

    // ---------------------------------------------------------------------------
    // Import CSV
    // ---------------------------------------------------------------------------

    public function importForm()
    {
        if (auth()->user()->isViewer()) abort(403, 'Viewers cannot import records.');
        return view('persons.import');
    }

    public function importCsv(Request $request)
    {
        if (auth()->user()->isViewer()) abort(403, 'Viewers cannot import records.');
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:51200', // max 50MB
        ]);

        $file    = $request->file('csv_file');
        $path    = $file->getRealPath();
        $handle  = fopen($path, 'r');

        if (!$handle) {
            return back()->withErrors(['csv_file' => 'The file could not be read.']);
        }

        // Detect and skip BOM
        $bom  = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'The file is empty or has an invalid format.']);
        }

        // Normalize header names
        $header = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h))), $header);

        $columnMap = [
            'name'       => ['name', 'first_name', 'نام'],
            'last_name'  => ['last_name', 'lastname', 'نام_خانوادگی', 'نام_خانوادگي'],
            'province'   => ['province', 'استان'],
            'country'    => ['country', 'کشور'],
            'email'      => ['email', 'e-_mail', 'e_mail', 'ایمیل'],
            'phone'      => ['phone', 'phone_number', 'تلفن', 'شماره_تلفن'],
            'whatsapp'   => ['whatsapp', 'whatsapp_number', 'واتساپ'],
            'education'  => ['education', 'تحصیلات', 'تحصيلات'],
            'gender'     => ['gender', 'جنسیت', 'جنسيت'],
            'event_name' => ['event_name', 'event', 'رویداد', 'ایونت'],
            'notes'      => ['notes', 'note', 'توضیحات'],
        ];

        $colIndex = [];
        foreach ($columnMap as $field => $aliases) {
            foreach ($aliases as $alias) {
                $idx = array_search($alias, $header);
                if ($idx !== false) {
                    $colIndex[$field] = $idx;
                    break;
                }
            }
        }

        // Load all existing emails once for duplicate detection
        $existingEmails = DB::table('persons')
            ->whereNotNull('email')->where('email', '!=', '')
            ->pluck('email')
            ->flip()
            ->all();
        $seenEmailsThisImport = [];

        $batch       = [];
        $batchSize   = 500;
        $imported    = 0;
        $skipped     = 0;
        $errors      = 0;
        $now         = now();

        while (($row = fgetcsv($handle)) !== false) {
            $g = fn(string $f, int $max = 100) =>
                isset($colIndex[$f]) ? substr(trim($row[$colIndex[$f]] ?? ''), 0, $max) : null;

            $genderVal = $g('gender', 20);
            if ($genderVal !== null && !in_array($genderVal, ['Male', 'Female', 'Other'], true)) {
                $genderVal = null;
            }

            $record = [
                'name'       => $g('name') ?? '',
                'last_name'  => $g('last_name') ?? '',
                'province'   => $g('province'),
                'country'    => $g('country'),
                'email'      => isset($colIndex['email'])
                    ? substr(strtolower(trim($row[$colIndex['email']] ?? '')), 0, 191)
                    : null,
                'phone'      => $g('phone', 30),
                'whatsapp'   => $g('whatsapp', 30),
                'education'  => $g('education'),
                'gender'     => $genderVal,
                'event_name' => $g('event_name', 150),
                'notes'      => isset($colIndex['notes'])
                    ? substr(trim($row[$colIndex['notes']] ?? ''), 0, 5000)
                    : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Require at least name
            if (empty($record['name'])) {
                $errors++;
                continue;
            }

            // Sanitize empty strings to null for optional fields
            foreach (['province', 'country', 'email', 'phone', 'whatsapp', 'education', 'event_name', 'notes'] as $f) {
                if ($record[$f] === '') {
                    $record[$f] = null;
                }
            }

            // Basic email format check
            if ($record['email'] && !filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
                $record['email'] = null;
            }

            // Skip if email already exists in DB or was seen earlier in this import
            if (!empty($record['email'])) {
                if (isset($existingEmails[$record['email']]) || isset($seenEmailsThisImport[$record['email']])) {
                    $skipped++;
                    continue;
                }
                $seenEmailsThisImport[$record['email']] = true;
            }

            $batch[] = $record;

            if (count($batch) >= $batchSize) {
                DB::table('persons')->insertOrIgnore($batch);
                $imported += count($batch);
                $batch = [];
            }
        }

        // Insert remaining records
        if (!empty($batch)) {
            DB::table('persons')->insertOrIgnore($batch);
            $imported += count($batch);
        }

        fclose($handle);

        $message = "Import completed. {$imported} records imported.";
        if ($skipped > 0) {
            $message .= " {$skipped} rows were skipped (duplicate email).";
        }
        if ($errors > 0) {
            $message .= " {$errors} rows were skipped (missing name).";
        }

        return redirect()->route('persons.index')->with('success', $message);
    }

    // ---------------------------------------------------------------------------
    // Export — filter form
    // ---------------------------------------------------------------------------

    public function exportForm(Request $request)
    {
        $pluck = fn($col) => Person::select($col)
            ->whereNotNull($col)->where($col, '!=', '')
            ->distinct()->orderBy($col)->pluck($col);

        $provinces  = $pluck('province');
        $countries  = $pluck('country');
        $educations = $pluck('education');
        $events     = $pluck('event_name');

        return view('persons.export', compact(
            'provinces', 'countries', 'educations', 'events'
        ));
    }

    // ---------------------------------------------------------------------------
    // Export CSV download (POST)
    // ---------------------------------------------------------------------------

    public function exportCsv(Request $request)
    {
        $request->validate([
            'search'     => 'nullable|string|max:200',
            'province'   => 'nullable|string|max:100',
            'country'    => 'nullable|string|max:100',
            'education'  => 'nullable|string|max:100',
            'event_name' => 'nullable|string|max:150',
            'gender'     => 'nullable|string|in:Male,Female,Other',
        ]);

        $search     = $request->input('search');
        $province   = $request->input('province');
        $country    = $request->input('country');
        $education  = $request->input('education');
        $eventName  = $request->input('event_name');
        $gender     = $request->input('gender');

        $filename = 'members_portal_export_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ];

        $callback = function () use ($search, $province, $country, $education, $eventName, $gender) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Name', 'Last Name', 'Province', 'Country',
                'Email', 'Phone', 'WhatsApp', 'Education', 'Gender',
                'Event Name', 'Notes',
            ]);

            Person::query()
                ->search($search)
                ->byProvince($province)
                ->byCountry($country)
                ->byEducation($education)
                ->byEvent($eventName)
                ->byGender($gender)
                ->orderBy('id')
                ->chunk(1000, function ($persons) use ($handle) {
                    foreach ($persons as $p) {
                        fputcsv($handle, [
                            $p->id,
                            $p->name,
                            $p->last_name,
                            $p->province,
                            $p->country,
                            $p->email,
                            $p->phone,
                            $p->whatsapp,
                            $p->education,
                            $p->gender,
                            $p->event_name,
                            $p->notes,
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ---------------------------------------------------------------------------
    // Sample CSV download
    // ---------------------------------------------------------------------------

    public function sampleCsv()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="members_portal_sample_import.csv"',
            'Cache-Control'       => 'no-cache',
        ];

        $rows = [
            ['name', 'last_name', 'province', 'country', 'email', 'phone', 'whatsapp', 'education', 'gender', 'event_name', 'notes'],
            ['Ahmad',  'Rahimi',  'Kabul',    'Afghanistan', 'ahmad@example.com',  '+93-700-123456', '+93-700-123456', "Bachelor's Degree", 'Male',   'Annual Conference', ''],
            ['Fatima', 'Noori',   'Herat',    'Afghanistan', 'fatima@example.com', '+93-799-654321', '',               "Master's Degree",   'Female', 'Annual Conference', ''],
            ['Ali',    'Ahmadi',  'Kandahar', 'Afghanistan', 'ali@example.com',    '0799123456',     '',               'High School Diploma','Male',   'Workshop 2025',     ''],
            ['Sara',   'Karimi',  'Balkh',    'Afghanistan', '',                   '',               '',               'Associate Degree',   'Female', '',                  ''],
        ];

        $callback = function () use ($rows) {
            $h = fopen('php://output', 'w');
            fwrite($h, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            foreach ($rows as $row) {
                fputcsv($h, $row);
            }
            fclose($h);
        };

        return response()->stream($callback, 200, $headers);
    }
}
