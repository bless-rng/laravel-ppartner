<?php


namespace App\Http\Controllers;


use App\Enums\TransactionType;
use App\Services\CoursesConverter;
use App\Transaction;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransactionController extends ApiController
{
    /** @var Transaction $model */
    protected $model = Transaction::class;
    protected $orderingFields = ['datetime'];


    protected function initRules()
    {
        $this->rules = [
            'user_id' => "required|numeric|exists:App\User,id",
            'amount'=>"required|numeric",
            'datetime'=>"required|string|date_format:Y-m-d H:i:s",
            'type'=>["required", "string",Rule::in(TransactionType::getValues())]
        ];
    }

    protected function initMessages()
    {
        $this->messages = [
            'type.in' => 'incorrect type: '.implode(', ', TransactionType::getValues()),
        ];
    }

    public function getAll(Request $request)
    {
        $user = $request->get('user');
        $builder = $this->model::query();
        if (!$user) {
            $builder
                ->addSelect(DB::raw('Date(datetime) as datetime'))
                ->addSelect(DB::raw('Date(datetime) as date'))
                ->rightJoin('users', 'transactions.user_id', '=', 'users.id')
                ->addSelect(DB::raw("SUM(CASE WHEN type = '".TransactionType::INCOME."' THEN amount ELSE -amount END) as amount"))
                ->addSelect('currency')
                ->groupBy(DB::raw('Date(datetime)'),'currency');
        } else {
            $builder
                ->where('user_id', $user)
                ->rightJoin('users', 'transactions.user_id', '=', 'users.id')
                ->addSelect('datetime')
                ->addSelect(DB::raw('Date(datetime) as date'))
                ->addSelect('amount')
                ->addSelect('users.currency')
                ->addSelect('type')
            ;
        }
        $this->applyPagination($request, $builder);
        $this->applyOrdering($request, $builder);
        $results = $builder->get();
        $courses = [];
        foreach ($results as $transaction) {
            $date = $transaction->getAttribute('date');
            $currency = $transaction->getAttribute('currency');
            if (!key_exists($currency, $courses)) {
                $courses[$currency][$date] = CoursesConverter::getCourseModifier($date, $currency);
            } else if (!key_exists($date, $courses[$currency])) {
                $courses[$currency][$date] = CoursesConverter::getCourseModifier($date, $currency);
            }
            $modifier = $courses[$currency][$date];
            $amount = $transaction->getAttribute('amount');
            $transaction->setAttribute('amount', $amount * $modifier);
        }
        if (!$user) {
            $sumByDate = [];
            foreach ($results as $transaction) {
                $date = $transaction->getAttribute('date');
                if(!key_exists($transaction->getAttribute('date'), $sumByDate)) {
                    $sumByDate[$date] = 0;
                }
                $sumByDate[$date]+=$transaction->getAttribute('amount');
            }
            $results = new Collection();
            foreach ($sumByDate as $key=>$value) {
                $transaction = new Transaction();
                $transaction->setAttribute('datetime', $key);
                $transaction->setAttribute('amount', $value);
                $results->add($transaction);
            }
        }
        return new Response($results->toArray(), 200);
    }
}
