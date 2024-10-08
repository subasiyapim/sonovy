<?php

namespace App\Imports;

use App\Models\Upc;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;

class UpcFileImport implements ToModel, WithValidation, SkipsEmptyRows
{
    /**
     * @param array $row
     */
    public function model(array $row)
    {
        return new Upc([
            'upc' => $row[0],
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'required|unique:upcs,upc',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'UPC alanı boş bırakılamaz.',
            '0.unique' => 'Bu UPC zaten mevcut.',
        ];
    }

}
