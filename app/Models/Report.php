<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $date;
    public $total_sales;
    public $total_expenses;
    public $profit;
    public $identifier;
    
    // Penting: Mencegah Laravel mencoba menyimpan ke database
    public $exists = true;
}
