<?php


namespace App\Traits;


use Illuminate\Support\Str;

trait SearchBuilder
{
    protected $filters;
    private function searchArguments()
    {
        return collect(
            explode(' ', $this->request->get('search'))
        )->filter();
    }
    public function search()
    {

        if($this->request->filled('search')){
            $this->searchArguments()->each(function ($argument) {
                $this->query->where(function ($query) use ($argument) {
                    $this->match($query, $argument);
                });
            });

            $this->filters = true;

            return $this;
        }
        return $this;
    }
    private function match($query, $argument){}
    private function whereHasRelation($query, $attribute, $argument)
    {
        if (! $this->isNested($attribute)) {
            $query->where(
                $attribute,
                'ilike',
                $this->wildcards($argument)
            );

            return;
        }

        $attributes = collect(explode('.', $attribute));

        $query->orWhere(function ($query) use ($attributes, $argument) {
            $query->whereHas($attributes->shift(), function ($query) use ($attributes, $argument) {
                $this->whereHasRelation($query, $attributes->implode('.'), $argument);
            });
        });
    }
    private function wildcards($argument)
    {
        return '%'.mb_strtoupper($argument).'%';
    }
    private function isNested($attribute)
    {

        return Str::contains($attribute, '.');
    }
}
