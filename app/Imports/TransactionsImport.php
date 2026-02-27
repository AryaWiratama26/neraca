<?php

namespace App\Imports;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class TransactionsImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $skipped = 0;
    public array $errors = [];

    public function collection(Collection $rows)
    {
        $userId = Auth::id();
        $accounts = Account::where('user_id', $userId)->get();
        $categories = Category::forUser($userId)->get();

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because heading row is 1

            try {
                // Find account by name
                $account = $accounts->first(fn($a) => mb_strtolower($a->name) === mb_strtolower(trim($row['akun'] ?? '')));
                if (!$account) {
                    $this->errors[] = "Baris {$rowNum}: Akun '{$row['akun']}' tidak ditemukan.";
                    $this->skipped++;
                    continue;
                }

                // Determine type
                $typeRaw = mb_strtolower(trim($row['tipe'] ?? ''));
                $type = match(true) {
                    str_contains($typeRaw, 'masuk') || str_contains($typeRaw, 'income') => 'income',
                    str_contains($typeRaw, 'keluar') || str_contains($typeRaw, 'expense') => 'expense',
                    default => null,
                };
                if (!$type) {
                    $this->errors[] = "Baris {$rowNum}: Tipe '{$row['tipe']}' tidak valid (gunakan Pemasukan/Pengeluaran).";
                    $this->skipped++;
                    continue;
                }

                // Find category
                $category = $categories->first(fn($c) => mb_strtolower($c->name) === mb_strtolower(trim($row['kategori'] ?? '')));

                // Parse amount
                $amount = (float) str_replace(['.', ','], ['', '.'], $row['jumlah'] ?? 0);
                if ($amount <= 0) {
                    $this->errors[] = "Baris {$rowNum}: Jumlah tidak valid.";
                    $this->skipped++;
                    continue;
                }

                // Parse date
                $date = null;
                try {
                    $date = Carbon::parse($row['tanggal'])->toDateString();
                } catch (\Exception $e) {
                    $this->errors[] = "Baris {$rowNum}: Format tanggal tidak valid.";
                    $this->skipped++;
                    continue;
                }

                // Parse tags
                $tags = null;
                if (!empty($row['tag'])) {
                    $tags = array_map('trim', explode(',', $row['tag']));
                }

                Transaction::create([
                    'user_id' => $userId,
                    'account_id' => $account->id,
                    'category_id' => $category?->id,
                    'type' => $type,
                    'amount' => $amount,
                    'description' => $row['catatan'] ?? null,
                    'date' => $date,
                    'tags' => $tags,
                ]);

                // Update account balance
                if ($type === 'income') {
                    $account->balance = (float)$account->balance + $amount;
                } else {
                    $account->balance = (float)$account->balance - $amount;
                }
                $account->save();

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNum}: " . $e->getMessage();
                $this->skipped++;
            }
        }
    }
}
