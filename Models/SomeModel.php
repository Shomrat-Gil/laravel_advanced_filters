<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use App\Filters\Filtering;
 
class Flight extends Model
{
    use Filtering;

    protected $whitelistFilters = [
        'SomeFilter',
        ];
}
