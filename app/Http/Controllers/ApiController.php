<?php


namespace App\Http\Controllers;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


abstract class ApiController
{
    /** @var Model $model*/
    protected $model;
    protected $limit = 10;
    protected $orderingFields = [];
    protected $offset = 0;
    protected $rules = [];
    protected $messages = [];

    protected abstract function initRules();
    protected abstract function initMessages();

    public function __construct()
    {
        $this->initRules();
        $this->initMessages();
    }


    public function applyPagination(Request $request, Builder $builder) {
        $limit = $request->get('limit', $this->limit);
        $offset = $request->get('offset', $this->offset);
        $builder
            ->limit($limit)
            ->offset($offset);
    }

    public function applyOrdering(Request $request, Builder $builder) {
        $order = $request->get('order');
        if ($order) {
            $order = explode('.', $order);
            if (count($order) == 2) {
                $fieldName = $order[0];
                if (!in_array($fieldName, $this->orderingFields)) return;
                $direction = $order[1]==="desc"?"desc":"asc";
                $builder
                    ->orderBy($fieldName, $direction);
            }
        }
    }

    public function create(Request $request) {
        $data = $request->all();
        $validation = Validator::make($data, $this->rules, $this->messages);
        if($validation->fails()) {
            return new Response($validation->errors()->toArray(), 403);
        }
        /** @var Model $model */
        $model = new $this->model();
        $model->fill($request->all());
        $model->save();
        return new Response(['id'=>$model->getAttributeValue('id')], 201);
    }
}
