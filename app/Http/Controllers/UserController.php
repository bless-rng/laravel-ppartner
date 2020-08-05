<?php


namespace App\Http\Controllers;


use App\Enums\Currency;
use App\User;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{
    protected $model = User::class;

    protected function initRules()
    {
        $this->rules = [
            'name'=>"required|string",
            'currency'=>[
                "required",
                Rule::in(Currency::getValues())
            ]
        ];
    }

    protected function initMessages()
    {
        $this->messages = [
            'currency.in' => 'incorrect currency code: '.implode(', ', Currency::getValues()),
        ];
    }
}
