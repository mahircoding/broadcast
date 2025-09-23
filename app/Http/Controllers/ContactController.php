<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display a listing of contacts
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by group
        if ($request->has('group') && $request->group) {
            $query->byGroup($request->group);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $contacts = $query->orderBy('name')->paginate(20);
        $groups = Contact::distinct()->pluck('group')->filter();

        return view('contacts.index', compact('contacts', 'groups'));
    }

    /**
     * Show the form for creating a new contact
     */
    public function create()
    {
        $groups = Contact::distinct()->pluck('group')->filter();
        return view('contacts.create', compact('groups'));
    }

    /**
     * Store a newly created contact
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'group' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Format phone number
        $phoneNumber = $this->formatPhoneNumber($request->phone_number);

        // Check if phone number already exists
        if (Contact::where('phone_number', $phoneNumber)->exists()) {
            return back()->withErrors(['phone_number' => 'Nomor telepon sudah ada dalam database.'])->withInput();
        }

        Contact::create([
            'name' => $request->name,
            'phone_number' => $phoneNumber,
            'email' => $request->email,
            'notes' => $request->notes,
            'group' => $request->group,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a contact
     */
    public function edit(Contact $contact)
    {
        $groups = Contact::distinct()->pluck('group')->filter();
        return view('contacts.edit', compact('contact', 'groups'));
    }

    /**
     * Update the specified contact
     */
    public function update(Request $request, Contact $contact)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'group' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Format phone number
        $phoneNumber = $this->formatPhoneNumber($request->phone_number);

        // Check if phone number already exists (excluding current contact)
        if (Contact::where('phone_number', $phoneNumber)->where('id', '!=', $contact->id)->exists()) {
            return back()->withErrors(['phone_number' => 'Nomor telepon sudah ada dalam database.'])->withInput();
        }

        $contact->update([
            'name' => $request->name,
            'phone_number' => $phoneNumber,
            'email' => $request->email,
            'notes' => $request->notes,
            'group' => $request->group,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil diperbarui.');
    }

    /**
     * Remove the specified contact
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil dihapus.');
    }

    /**
     * Show the CSV upload form
     */
    public function uploadForm()
    {
        return view('contacts.upload');
    }

    /**
     * Process CSV upload
     */
    public function processUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'has_header' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $file = $request->file('csv_file');
        $hasHeader = $request->boolean('has_header', true);

        try {
            $results = $this->processCsvFile($file, $hasHeader);

            return redirect()->route('contacts.index')->with([
                'success' => "CSV berhasil diproses. {$results['success']} kontak ditambahkan, {$results['skipped']} dilewati, {$results['errors']} error.",
                'csv_results' => $results
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['csv_file' => 'Error processing CSV: ' . $e->getMessage()]);
        }
    }

    /**
     * Export contacts to CSV
     */
    public function export(Request $request)
    {
        $query = Contact::query();

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('group') && $request->group) {
            $query->byGroup($request->group);
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $contacts = $query->orderBy('name')->get();

        $filename = 'contacts_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($contacts) {
            $file = fopen('php://output', 'w');

            // Add header row
            fputcsv($file, ['Name', 'Phone Number', 'Email', 'Group', 'Notes', 'Status']);

            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->name,
                    $contact->phone_number,
                    $contact->email,
                    $contact->group,
                    $contact->notes,
                    $contact->is_active ? 'Active' : 'Inactive'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process CSV file and import contacts
     */
    private function processCsvFile($file, $hasHeader = true)
    {
        $handle = fopen($file->path(), 'r');
        $results = [
            'success' => 0,
            'skipped' => 0,
            'errors' => 0,
            'error_details' => []
        ];

        $rowNumber = 0;

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNumber++;

            // Skip header row
            if ($hasHeader && $rowNumber === 1) {
                continue;
            }

            // Skip empty rows
            if (empty(array_filter($data))) {
                continue;
            }

            try {
                // Expected CSV format: Name, Phone, Email, Group, Notes
                $name = trim($data[0] ?? '');
                $phone = trim($data[1] ?? '');
                $email = trim($data[2] ?? '') ?: null;
                $group = trim($data[3] ?? '') ?: null;
                $notes = trim($data[4] ?? '') ?: null;

                // Validate required fields
                if (empty($name) || empty($phone)) {
                    $results['errors']++;
                    $results['error_details'][] = "Row {$rowNumber}: Name and phone are required";
                    continue;
                }

                // Format phone number
                $formattedPhone = $this->formatPhoneNumber($phone);

                // Check if phone already exists
                if (Contact::where('phone_number', $formattedPhone)->exists()) {
                    $results['skipped']++;
                    continue;
                }

                // Validate email if provided
                if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $results['errors']++;
                    $results['error_details'][] = "Row {$rowNumber}: Invalid email format";
                    continue;
                }

                // Create contact
                Contact::create([
                    'name' => $name,
                    'phone_number' => $formattedPhone,
                    'email' => $email,
                    'group' => $group,
                    'notes' => $notes,
                    'is_active' => true
                ]);

                $results['success']++;

            } catch (\Exception $e) {
                $results['errors']++;
                $results['error_details'][] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        fclose($handle);
        return $results;
    }

    /**
     * Format phone number consistently
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // If doesn't start with +, assume Indonesian number
        if (!str_starts_with($phone, '+')) {
            // Remove leading 0 if present and add +62
            $phone = '+62' . ltrim($phone, '0');
        }

        return $phone;
    }
}
