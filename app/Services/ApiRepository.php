<?php


namespace App\Services;


use App\Traits\SearchBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiRepository
{
    use SearchBuilder;
    protected $request;
    protected $query;
    public $searchAttribute;
    public function __construct(Request $request)
    {
        $this->request=$request;
    }

    public function of(Builder $query)
    {
        $this->query=$query;
    }
    public function searchAttribute($searchAttribute)
    {
        $this->searchAttribute=$searchAttribute;
    }
    public function get(){
        return $this->withs()->search()->filter()->limit()->data();
    }

    public function find($id)
    {
        $this->withs();
        return $this->query->find($id);
    }
    public function data(){
        return $this->query->get();
    }
    public function withs()
    {
        if($this->request->has('with')){
            $with = explode(';', $this->request->input('with'));
            foreach ($with as $item) {
                $method='with'.Str::studly($item);
                $scope='scope'.Str::studly($method);
                if(method_exists($this->query->getModel(),$scope)){
                    $this->query->{$method}();
                }else{
                    $this->query->with($item);
                }
            }
        }
        return $this;
    }
    public function search()
    {
        if($this->request->filled('search')){
            $this->searchArguments()->each(function ($argument) {
                $this->query->where(function ($query) use ($argument) {
                    $this->match($query, $argument);
                });
            });
        }
        return $this;
    }
    private function match($query, $argument)
    {
        collect($this->searchAttribute)->each(function ($attribute) use ($query, $argument) {
            return $this->isNested($attribute)
                ? $this->whereHasRelation($query, $attribute, $argument)
                : $query->orWhere(
                    $attribute,
                    'ilike',
                    $this->wildcards($argument)
                );
        });
    }
    public function filter()
    {
        if($this->request->has('filters')){
            $filters=explode(';',$this->request->input('filters'));
            foreach($filters as $filter){
                $fields=explode(':',$filter);
                $this->query->where($fields[0],'=',$fields[1]);
            }
        }
        return $this;
    }
    public function limit()
    {
        if($this->request->has('limit')){
            $this->query->take($this->request->input('limit'));
        }
        if($this->request->has('offset')){
            $this->query->skip($this->request->input('offset'));
        }
        return $this;
    }
}
