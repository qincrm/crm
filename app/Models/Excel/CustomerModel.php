<?php
namespace App\Models\Excel;

use App\Models\Customer;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerModel implements ToModel
{
    public function model(array $rows)
    {
        return null;
    }

}