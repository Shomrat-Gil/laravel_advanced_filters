# Advanced Filters for Laravel
## _Advanced Filters for Laravel Model using Pipeline_

 ### You can learn more about Laravel Pipeline [here](https://laravel.com/api/9.x/Illuminate/Pipeline/Pipeline.html)

  
**This Trait adds filter scope to the model**


## Create Filter with no params:
     namespace App\Filters;
     class SomeFilter
     {
           public function handle($query, $next)
           {
               $query->whereNull('column');
               return $next($query);
           }
     }
## Create Filter with single param:
     namespace App\Filters;
     class SomeFilter
     {
           public function handle($query, $next, string $value)
           {
               $query->where('column', $value);
               return $next($query);
           }
     }
## Create Filter with multiple params:
    namespace App\Filters;
    class SomeFilter
    {
          public function handle($query, $next, ...$args)
          {
              $query->where('column', $args[0])->where('field', $args[1]);
              return $next($query);
          }
    }

# How To:

## The first step is to enable the filters within the model

    <?php     
    namespace App\Models;     
    use Illuminate\Database\Eloquent\Model;     
    class SomeModel extends Model
    {
        /**
         * The filters associated with the model.
         *
         * @var array
         */
        protected $whitelistFilters = [
						        'fooFilter',
						        'booFilter'
						        ];
    }

## Example usage:
**Filters with no params:**

    SomeModel::filter('SomeFilter')->get();
    SomeModel::filter(['SomeFilter'])->get();
    SomeModel::filter('SomeFooFilter', 'SomeBooFilter')->get();
    SomeModel::filter(['SomeFooFilter', 'SomeBooFilter'])->get();

**Filters with single params:**
 
    SomeModel::filter('SomeFilter:Foo')->get();
    SomeModel::filter(['SomeFilter:Foo'])->get();

**Filters with multiple params:**
 
    SomeModel::filter('SomeFilter:Foo,Boo')->get();
    SomeModel::filter(['SomeFilter:Foo,Boo'])->get(); 


