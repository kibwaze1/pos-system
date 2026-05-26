<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // List of world currencies – key = symbol, value = display name
        $currencies = [
            '$' => 'USD ($) – US Dollar',
            '€' => 'EUR (€) – Euro',
            '£' => 'GBP (£) – British Pound',
            '¥' => 'JPY (¥) – Japanese Yen',
            'C$' => 'CAD (C$) – Canadian Dollar',
            'A$' => 'AUD (A$) – Australian Dollar',
            'CHF' => 'CHF – Swiss Franc',
            'CN¥' => 'CNY (¥) – Chinese Yuan',
            '₹' => 'INR (₹) – Indian Rupee',
            'R$' => 'BRL (R$) – Brazilian Real',
            'R' => 'ZAR (R) – South African Rand',
            '₦' => 'NGN (₦) – Nigerian Naira',
            'KSh' => 'KES (KSh) – Kenyan Shilling',
            'TSh' => 'TZS (TSh) – Tanzanian Shilling',
            'USh' => 'UGX (USh) – Ugandan Shilling',
            '₵' => 'GHS (₵) – Ghanaian Cedi',
            'E£' => 'EGP (E£) – Egyptian Pound',
            '﷼' => 'SAR (﷼) – Saudi Riyal',
            'د.إ' => 'AED (د.إ) – UAE Dirham',
            '₽' => 'RUB (₽) – Russian Ruble',
            'S$' => 'SGD (S$) – Singapore Dollar',
            'HK$' => 'HKD (HK$) – Hong Kong Dollar',
            'NZ$' => 'NZD (NZ$) – New Zealand Dollar',
            '₩' => 'KRW (₩) – South Korean Won',
            '₺' => 'TRY (₺) – Turkish Lira',
            'kr' => 'SEK (kr) – Swedish Krona',
            'zł' => 'PLN (zł) – Polish Zloty',
            '฿' => 'THB (฿) – Thai Baht',
            '₫' => 'VND (₫) – Vietnamese Dong',
            'Rp' => 'IDR (Rp) – Indonesian Rupiah',
            '₱' => 'PHP (₱) – Philippine Peso',
            'RM' => 'MYR (RM) – Malaysian Ringgit',
            '₨' => 'PKR (₨) – Pakistani Rupee',
            '৳' => 'BDT (৳) – Bangladeshi Taka',
        ];

        return view('settings.index', compact('settings', 'currencies'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }
        return redirect()->route('settings.index')->with('success', 'Settings updated.');
    }

    public function backup()
    {
        $backupFile = storage_path('app/backup/pos_backup_' . date('Y-m-d_H-i-s') . '.sql');
        if (!is_dir(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0777, true);
        }
        $dbPath = database_path('database.sqlite'); // adjust if using MySQL
        if (file_exists($dbPath)) {
            copy($dbPath, $backupFile);
            return redirect()->back()->with('success', 'Backup created: ' . basename($backupFile));
        }
        return redirect()->back()->with('error', 'Backup failed – database file not found.');
    }
}
