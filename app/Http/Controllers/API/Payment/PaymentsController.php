<?php

namespace App\Http\Controllers\API\Payment;

use App\Helpers\RoleHelper;
use App\Http\Controllers\ApiController;
use App\Model\Payments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;
class PaymentsController extends ApiController
{
    public function create(Request $request)
    {
        try{
            RoleHelper::need("payment_create");
            $all = DB::table('plans')
                ->where('id', $request->plan_id)
                ->select('amount','title')
                ->first();
            if ($all) {
                $createPayment = Payments::create([
                    'user_id' => Auth::id(),
                    'type_id' => $request->type_id,
                    'package_name' => $request->package_name,
                    'transaction_id' => $request->transaction_id,
                    'plan' => $request->plan_id,
                    'package_name' => $all->title,
                    'amount' => sprintf("%.2f", $all->amount),
                    'status' => $request->status,
                ]);
                if ($createPayment) {
                    $paymentRequest = [
                        "ACTION" => "SALE",
                        "MERCHANTUSER" => "info@tumisted.com",
                        "MERCHANTPASSWORD" => "T7273516is*",
                        "MERCHANT" => "10000862",
                        "MERCHANTPAYMENTID" => "Payment-".$createPayment->id,
                        "CUSTOMER" => "Customer1",
                        "AMOUNT" => $all->amount,
                        "CURRENCY" => "TRY",
                        "CUSTOMEREMAIL" => "erkintanyeli@gmail.com",
                        "CUSTOMERNAME" => "Erkin",
                        "CUSTOMERPHONE" => "Tanyeli",
                        "CUSTOMERIP" => "192.168.1.1",
                        "CARDPAN" => str_replace(" ", "", $request->card_no),
                        "CARDEXPIRY" => $request->card_expiry,
                        "CARDCVV" => $request->card_cvv,
                        "NAMEONCARD" => $request->card_name,
                    ];
                    $response = Http::asForm()->post("https://entegrasyon.paratika.com.tr/paratika/api/v2", $paymentRequest);
                    $responseMessage = json_decode($response->body());
                    if($responseMessage->responseCode == 00){
                        $payments = Payments::find($createPayment->id);
                        $payments->status = 1;
                        $payments->transaction_id = $responseMessage->pgTranRefId;
                        $payments->save();
                        return $this->success('OK', ['create' => $responseMessage->responseMsg]);
                    }
                    else{
                        $payments = Payments::find($createPayment->id);
                        $payments->status = 0;
                        $payments->save();
                        return $this->fail(trans('payment.'.$responseMessage->errorCode));
                    }
                }
                else {
                    return $this->fail(trans('payment.createFailed'));
                }
            }
            else{
                return $this->fail(trans('payment.planNotFound'));
            }
        }
        catch (Exception $e){
            return $this->fail($e->getMessage());
        }
    }

    public function index()
    {
        RoleHelper::need("payment_user_show");

        $all = DB::table('payments')
            ->where('payments.user_id', Auth::id())
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->join('payment_types', 'payments.type_id', '=', 'payment_types.id')
            ->select(DB::raw('CONCAT(users.name, " ", users.surname) AS fullname'),
                'users.email',
                'payments.package_name as packagename',
                'payments.transaction_id as transactionId',
                'payments.plan',
                'payments.status',
                'payments.amount',
                'payments.created_at as created',
                'payment_types.name as type'
            )
            ->get();

        $month = [
            '01',
            '02',
            '03',
            '04',
            '05',
            '06',
            '07',
            '08',
            '09',
            '10',
            '11',
            '12',
        ];

        $totel_istatistik = [];
        $totel_year = [];
        foreach ($all as $data) {
            $data->amount = $this->sprint($data->amount);
            $data->created = date('d-m-Y', strtotime($data->created));

            // Available Years
            $year = date('Y', strtotime($data->created));
            if (!in_array($year, $totel_year)) {
                array_push($totel_year, $year);

                //"12-2019"
                foreach ($month as $item) {
                    $key = (string)$item . "-" . $year;
                    $totel_istatistik[$year][$key] = 0;
                }
            }

            // Month Calculate
            $day_year = date('m-Y', strtotime($data->created));
            if (!in_array($day_year, $totel_istatistik)) {
                $totel_istatistik[$year][$day_year] = 0;
            }
            $totel_istatistik[$year][$day_year] += $data->amount;
        }

        $data = [
            'day' => $this->timeSource(1),
            'week' => $this->timeSource(7),
            'month' => $this->timeSource(30),
            'threemonth' => $this->timeSource(91),
            'sixmonth' => $this->timeSource(182),
            'year' => $this->timeSource(365),
            'all' => $all,
        ];
        return $this->success('OK', ['reports' => $data, 'statistics' => $totel_istatistik, 'years' => $totel_year]);
    }

    public function authTimeSource($day)
    {
        $date = Carbon::now()->subDays($day)->startOfDay();
        $getPayment = DB::table('payments')
            ->where('payments.user_id', Auth::id())
            ->where('payments.created_at', '>=', $date)
            ->join('payment_types', 'payments.type_id', '=', 'payment_types.id')
            ->select('payments.*', 'payment_types.name as type_name')
            ->get();

        $total = 0;
        foreach ($getPayment as $data) {
            $total += $data->amount;
        }
        return [
            'count' => $getPayment->count(),
            'total' => $this->sprint($total),
        ];
    }

    public function timeSource($day)
    {
        $date = Carbon::now()->subDays($day)->startOfDay();
        $getPayment = DB::table('payments')
            ->where('payments.created_at', '>=', $date)
            ->join('payment_types', 'payments.type_id', '=', 'payment_types.id')
            ->select('payments.*', 'payment_types.name as type_name')
            ->get();

        $total = 0;
        foreach ($getPayment as $data) {
            $total += $data->amount;
        }
        return [
            'count' => $getPayment->count(),
            'total' => $this->sprint($total),
        ];
    }

    public function sprint($sprint)
    {
        return sprintf("%.2f", $sprint);
    }

    public function allPayments()
    {
        RoleHelper::need("payment_all_user_show");

        $all = DB::table('payments')
            ->join('payment_types', 'payments.type_id', '=', 'payment_types.id')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select(DB::raw('CONCAT(users.name, " ", users.surname) AS fullname'),
                'users.email',
                'payments.package_name as packagename',
                'payments.transaction_id as transactionId',
                'payments.plan',
                'payments.status',
                'payments.amount',
                'payments.created_at as created',
                'payment_types.name as type'
            )
            ->get();

        $month = [
            '01',
            '02',
            '03',
            '04',
            '05',
            '06',
            '07',
            '08',
            '09',
            '10',
            '11',
            '12',
        ];

        $totel_istatistik = [];
        $totel_year = [];
        foreach ($all as $data) {
            $data->amount = $this->sprint($data->amount);
            $data->created = date('d-m-Y', strtotime($data->created));

            // Mevcut Yıllar
            $year = date('Y', strtotime($data->created));
            if (!in_array($year, $totel_year)) {
                array_push($totel_year, $year);

                //"12-2019"
                foreach ($month as $item) {
                    $key = (string)$item . "-" . $year;
                    $totel_istatistik[$year][$key] = 0;
                }
            }

            // Aylık Hesaplama
            $day_year = date('m-Y', strtotime($data->created));
            if (!in_array($day_year, $totel_istatistik)) {
                $totel_istatistik[$year][$day_year] = 0;
            }
            $totel_istatistik[$year][$day_year] += $data->amount;
        }

        $data = [
            'day' => $this->timeSource(1),
            'week' => $this->timeSource(7),
            'month' => $this->timeSource(30),
            'threemonth' => $this->timeSource(91),
            'sixmonth' => $this->timeSource(182),
            'year' => $this->timeSource(365),
            'all' => $all,
        ];
        return $this->success('OK', ['reports' => $data, 'statistics' => $totel_istatistik, 'years' => $totel_year]);
    }
}
