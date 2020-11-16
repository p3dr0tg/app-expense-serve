<?php


namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Movement extends Model
{

    protected $fillable=[
        'category_id','saving_account_id','month','date','description'
    ];
    protected $hidden=[
        'created_at','updated_at','user_id'
    ];
    protected static function boot()
    {
        static::addGlobalScope('by_user',function(Builder $builder){
            $builder->where('user_id',Auth::user()->id);
        });
        parent::boot();
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    public function scopeWithCategory($query)
    {
        return $query->with(['category'=>function($q){
            $q->select('id','description','type');
        }]);
    }
    public function scopeFilter($query,$options)
    {
        return $query//->where('user_id',$options->user()->id)
            ->where('month',$options->month)->whereYear('date',$options->year);
    }
    public function scopeGetAll($query,$options)
    {
        return $query->withCategory()->filter($options)
            ->orderBy('date','desc')->orderBy('id','desc');
    }
    public function scopeGetTotal($query,$options)
    {
        $month=($options->year*12)+$options->month;
        $query->whereRaw('extract(year from date)*12+month<=?',[$month])
                 ->where('user_id',$options->user()->id)
                ->selectRaw("coalesce(sum(amount),0) as amount");
    }
    public function scopeGetPreviousBalance($query,$options)
    {
        $month=($options->year*12)+$options->month;
        $query->whereRaw('extract(year from date)*12+month<?',[$month])
            ->where('user_id',$options->user()->id)
            ->selectRaw("coalesce(sum(amount),0) as amount");
        return $query;
    }
    public function scopeGetSummary($query,$options)
    {
        return $query->filter($options)
            ->orderBy('date','asc')->groupBy('date')
            ->selectRaw('date')
            ->selectRaw('coalesce(sum( case when amount<0 then amount end),0) as expenses')
            ->selectRaw('coalesce(sum( case when amount>0 then amount end),0) as income');
    }

    public function getByCategories($options)
    {
        return static::join('categories as c','c.id','=','movements.category_id')
            ->filter($options)
            ->groupBy('movements.category_id')
            ->groupBy('c.description')
            ->groupBy('c.type')
            ->selectRaw('movements.category_id,c.description as category,c.type')
            ->selectRaw('coalesce(sum(amount),0) as amount')
            ->orderBy('c.type','desc')
            ->orderBy('amount','desc')
            ->get();
    }
}
