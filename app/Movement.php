<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $fillable=[
        'category_id','saving_account_id','month','date','description'
    ];
    public function categories()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    public function scopeFilter($query,$options)
    {
        return $query->where('user_id',$options->user()->id)
            ->where('month',$options->month)->whereYear('date',$options->year);
    }
    public function scopeGetAll($query,$options)
    {
        return $query->with('categories')->filter($options)
            ->orderBy('date','desc')->orderBy('id','desc');
    }
    public function scopeGetTotal($query,$options)
    {
        $month=($options->year*12)+$options->month;
        $query->whereRaw('extract(year from date)*12+month<=?',[$month])
                 ->where('user_id',$options->user()->id)
                ->selectRaw("coalesce(sum(amount),0) as amount");
    }
    public function scopeGetSummary($query,$options)
    {
        return $query->filter($options)
            ->orderBy('date','asc')->groupBy('date')
            ->selectRaw('date')
            ->selectRaw('coalesce(sum( case when amount<0 then amount end),0) as expenses')
            ->selectRaw('coalesce(sum( case when amount>0 then amount end),0) as income');
    }
}
