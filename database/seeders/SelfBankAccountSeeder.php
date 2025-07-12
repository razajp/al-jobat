<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;
use App\Models\Setup;
use App\Models\Supplier;

class SelfBankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $allMyData = [
            [
                'category' => 'self',
                'account_no' => '0125-0101936491',
                'sub_category' => null,
                'bank_name' => 'Meezan Bank Limited',
                'bank_id' => null,
                'account_title' => 'Mohammad Zubair | MZ',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '15033826',
                    'end' => '15033925',
                ],
            ],
            [
                'category' => 'self',
                'account_no' => '0125-0101402162',
                'sub_category' => null,
                'bank_name' => 'Meezan Bank Limited',
                'bank_id' => null,
                'account_title' => 'Ever Smart Garments | MES',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '3408736',
                    'end' => '3408835',
                ],
            ],
            [
                'category' => 'self',
                'account_no' => '0125-0101727148',
                'sub_category' => null,
                'bank_name' => 'Meezan Bank Limited',
                'bank_id' => null,
                'account_title' => 'Ali | MA',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '9988836',
                    'end' => '9988935',
                ],
            ],
            [
                'category' => 'self',
                'account_no' => '0125-0103753902',
                'sub_category' => null,
                'bank_name' => 'Meezan Bank Limited',
                'bank_id' => null,
                'account_title' => 'Abdullah | MAB',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '17907941',
                    'end' => '17908040',
                ],
            ],
            [
                'category' => 'self',
                'account_no' => '0125-0105709778',
                'sub_category' => null,
                'bank_name' => 'Meezan Bank Limited',
                'bank_id' => null,
                'account_title' => 'AJ Kids | MAJ',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '88126026',
                    'end' => '88126125',
                ],
            ],
            [
                'category' => 'self',
                'account_no' => '0125-0108511937',
                'sub_category' => null,
                'bank_name' => 'Meezan Bank Limited',
                'bank_id' => null,
                'account_title' => 'Baby Dream Trading Company | MBD',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '14882276',
                    'end' => '14882375',
                ],
            ],
            [
                'category' => 'self',
                'account_no' => '1022-0081000704-016',
                'sub_category' => null,
                'bank_name' => 'Bank Al Habib Limited',
                'bank_id' => null,
                'account_title' => 'Zubair | HZ',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '11573441',
                    'end' => '11573540',
                ],
            ],
            [
                'category' => 'self',
                'account_no' => '1022-0981-005116-013',
                'sub_category' => null,
                'bank_name' => 'Bank Al Habib Limited',
                'bank_id' => null,
                'account_title' => 'Ali S/O Abdul Majeed | HA',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '11566521',
                    'end' => '11566620',
                ],
            ],
            [
                'category' => 'self',
                'account_no' => '1022-0081004013-018',
                'sub_category' => null,
                'bank_name' => 'Bank Al Habib Limited',
                'bank_id' => null,
                'account_title' => 'Baby Dream Trading Company | HBD',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '11267591',
                    'end' => '11267690',
                ],
            ],
            [
                'category' => 'self',
                'account_no' => '0009-1003343108',
                'sub_category' => null,
                'bank_name' => 'Bank Alfalah Limited',
                'bank_id' => null,
                'account_title' => 'Ali | F',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '40197976',
                    'end' => '40198075',
                ],
            ],
            [
                'category' => 'self',
                'account_no' => '699-7-29301-714-143484',
                'sub_category' => null,
                'bank_name' => 'Habib Metropolitan Bank Limited',
                'bank_id' => null,
                'account_title' => 'Abdullah | HMP',
                'date' => '2024-01-01',
                'remarks' => null,
                'cheque_book_serial' => [
                    'start' => '182323071',
                    'end' => '182323170',
                ],
            ],
        ];

        foreach ($allMyData as $data) {
            // Find bank by data->bank_name
            $bank = Setup::where('title', $data['bank_name'])->where('type', 'bank_name')->first();
            if (!$bank) {
                $this->command->warn("Bank '{$data['bank_name']}' not found. Skipping. Account Title: '{$data['account_title']}'");
                continue;
            }
            $data['bank_id'] = $bank->id; // Set bank_id to bank ID

            // Create the bank account
            BankAccount::create([
                'category' => $data['category'],
                'sub_category_type' => null,
                'sub_category_id' => null,
                'bank_id' => $data['bank_id'],
                'account_title' => $data['account_title'],
                'date' => $data['date'],
                'remarks' => $data['remarks'],
                'account_no' => $data['account_no'],
                'chqbk_serial_start' => $data['cheque_book_serial']['start'] ?? null,
                'chqbk_serial_end' => $data['cheque_book_serial']['end'] ?? null,
                'creator_id' => 1,
            ]);
        }
    }
}
