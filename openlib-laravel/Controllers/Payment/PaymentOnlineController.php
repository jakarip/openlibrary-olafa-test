<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\PaymentOnline;
use App\Models\Penalty;
use App\Models\MkpPaymentModel as MkpPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
class PaymentOnlineController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '-1');
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }
    public function mkp()
    {
        $result = $this->MKPGenerateLink(Auth::id(), '30000');
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }
        $penalty = (new Penalty())->getPenalty($userId);
        $payment = (new PaymentOnline())->getPayment($userId);

        return view('payment.online', [
            'title' => 'Online Payments',
            'icon' => 'icon-book3',
            'penalty' => $penalty,
            'payment' => $payment
        ]);
    }

    public function json(Request $request)
{
    if (!$request->ajax()) {
        return response()->json(['error' => 'Invalid request'], 400);
    }

    $now = now();

    // Update expired payments in the database
    DB::table('rent_penalty_payment_online')
        ->where('pay_status', '!=', 1) // Status bukan success
        ->where('pay_expired_date', '<', $now) // Sudah melewati batas waktu
        ->update(['pay_status' => 6]); // Ubah menjadi expired

    // Fetch payments for DataTable
    $payments = PaymentOnline::where('pay_id_member', Auth::id())
        ->get()
        ->map(function ($payment) {
            $payment->member = (new PaymentOnline())->member($payment->pay_id_member);
            return $payment;
        });

    $statusMap = [
        0 => 'Request',
        1 => 'Success',
        2 => 'Pending',
        3 => 'Failed',
        4 => 'Void',
        5 => 'Cancelled',
        6 => 'Expired'
    ];

    $data = $payments->map(function ($payment) use ($statusMap) {
        return [
            'member' => [
                'master_data_user' => $payment->member->master_data_user ?? '',
                'master_data_fullname' => $payment->member->master_data_fullname ?? ''
            ],
            'pay_no_ref' => $payment->pay_no_ref . ($payment->pay_status == 1 ? 
                '<br><a href="' . route('payment.online.invoice', $payment->pay_no_ref) . '" target="_blank" class="btn btn-xs btn-info">
                    <i class="icon-file-text"></i> Invoice
                </a>' : ''),
            'pay_payment_date' => $payment->pay_payment_date,
            'pay_request_date' => $payment->pay_request_date,
            'pay_expired_date' => $payment->pay_expired_date,
            'pay_link_status' => $payment->pay_link_status,
            'pay_link' => $payment->pay_link,
            'pay_amount' => $payment->pay_amount,
            'pay_status' => $payment->pay_status,
            'pay_id' => $payment->pay_id
        ];
    });

    return response()->json([
        'draw' => intval($request->input('draw')),
        'recordsTotal' => $payments->count(),
        'recordsFiltered' => $payments->count(),
        'data' => $data
    ]);
}

    public function invoice($noRef)
{
    $invoice = PaymentOnline::where('pay_no_ref', $noRef)
        ->where('pay_id_member', Auth::id())
        ->first();

    if (!$invoice || $invoice->pay_status != 1) {
        return redirect()->route('payment.online');
    }

    return view('payment.invoice', [
        'title' => 'Payment Invoice',
        'invoice' => $invoice
    ]);
}

    public function add()
    {
        $penalty = Penalty::getPenalty(Auth::id());
        $payment = PaymentOnline::getPayment(Auth::id());

        return response()->json([
            'denda' => $penalty - $payment
        ]);
    }

public function insert(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:1'
    ]);

    $member = Auth::user();
    $datetime = date('YmdHis');
    $payNoRef = "OPENLIB-" . $member->id . "-" . $datetime;
    $payRequestDate = now();
    $payExpiredDate = now()->addDay();

    $result = $this->MKPGenerateLink($member, $request->amount, $payNoRef, $payRequestDate, $payExpiredDate);

    if ($result['status']) {
        $statusCode = $result['statusCode'] == '101' ? 'success' : 'failed';

        $paymentOnline = MkpPayment::create([
            'pay_id_rent_penalty_payment' => 0,
            'pay_id_member' => $member->id,
            'pay_status' => 0,
            'pay_no_ref' => $payNoRef,
            'pay_payment_date' => null,
            'pay_amount' => $request->amount,
            'pay_request_date' => $payRequestDate,
            'pay_expired_date' => $payExpiredDate,
            'pay_link' => $result['result']['paymentUrl'],
            'pay_link_status' => $statusCode,
            'pay_link_status_code' => $result['statusCode']
        ]);

        return response()->json([
            'success' => true,
            'payment_url' => $result['result']['paymentUrl'],
            'message' => 'Payment link generated successfully'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => $result['errorMessage'] ?? 'Failed to generate payment link'
    ], 400);
}

    public function generatePaymentLink(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $payment = MkpPayment::findOrFail($request->id);
        $member = Auth::user();

        $payNoRef = $payment->pay_no_ref;
        $payRequestDate = now();
        $payExpiredDate = now()->addDay();

        $result = $this->MKPGenerateLink($member, $payment->pay_amount, $payNoRef, $payRequestDate, $payExpiredDate);

        if ($result['status']) {
            $statusCode = $result['statusCode'] == '101' ? 'success' : 'failed';

            $payment->update([
                'pay_request_date' => $payRequestDate,
                'pay_expired_date' => $payExpiredDate,
                'pay_link' => $result['result']['paymentUrl'],
                'pay_link_status' => $statusCode,
                'pay_link_status_code' => $result['statusCode']
            ]);

            return response()->json([
                'status' => 'ok',
                'message' => 'Success',
                'link' => $result['result']['paymentUrl'],
                'shortlink' => $result['result']['shortUrl']
            ]);
        }

        return response()->json([
            'status' => 'false',
            'message' => 'Failed',
            'text' => $result['errorMessage'] ?? 'Unknown error'
        ], 400);
    }

public function confirm(Request $request)
{
    $data = $request->json()->all();

    if (!$data || !isset($data['merchantNoRef']) || !isset($data['paymentStatus'])) {
        return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
    }

    $rentPenaltyPaymentId = null;

    // Check if payment status is successful (pay_status = 1)
    if ($data['paymentStatusCode'] == '1') {
        $userId = explode("-", $data['merchantNoRef'])[1];

        // Insert into rent_penalty_payment
        $rentPenaltyPaymentId = DB::table('rent_penalty_payment')->insertGetId([
            'member_id' => $userId,
            'amount' => $data['amount'],
            'payment_date' => isset($data['paymentTime']) ? date('Y-m-d', strtotime($data['paymentTime'])) : now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    // Update rent_penalty_payment_online with payment details
    PaymentOnline::where('pay_no_ref', $data['merchantNoRef'])
        ->update([
            'pay_id_rent_penalty_payment' => $rentPenaltyPaymentId,
            'pay_status' => $data['paymentStatusCode'],
            'pay_payment_date' => $data['paymentTime'] ?? null,
            'pay_method_name' => $data['paymentMethodName'] ?? null,
            'pay_category' => $data['paymentCategory'] ?? null,
            'pay_json' => json_encode($data),
            'pay_header' => json_encode($request->headers->all())
        ]);

    return response()->json(['status' => 'ok']);
}
    public function remaining()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $penalty = (new Penalty())->getPenalty($userId);
        $payment = (new PaymentOnline())->getPayment($userId);

        return response()->json([
            'remaining' => $penalty - $payment
        ]);
    }
    private function MKPGenerateLink($member, $amount, $payNoRef = null, $payRequestDate = null, $payExpiredDate = null)
    {
        $MKP_MERCHANT_KEY = 'tpTjoqbbRPto65aQUvevfZisFA==|hCzvjAOvxULBB-0o4Yjszx3e5savK9M-xCSFX7TH8eOmjXoYYqTeczAXlp2e78A7vZwaStdfOZi7';
        $MKP_SECRET_KEY = 'MT7XlbWiftvtTW6avkMForImAYR17V8pjKs=';
        $MKP_BASE_URL = 'https://sandbox.mkpmobile.com/api/payment-link';
        $auth = 'bWtwbW9iaWxlOm1rcG1vYmlsZTEyMw==';

        if (!$payNoRef) {
            $payNoRef = "OPENLIB-".$member->id."-".date('YmdHis');
        }
        
        if (!$payRequestDate) {
            $payRequestDate = now();
        }
        
        if (!$payExpiredDate) {
            $payExpiredDate = now()->addDay();
        }

        $body = [
            'amount' => (float)$amount,
            'remarks' => "Penalty Payment",
            'merchantNoRef' => $payNoRef,
            'maxAttempt' => null,
            'callbackUrl' => "https://openlibrary.telkomuniversity.ac.id/payment/index.php/paymentsonline/confirm",
            'expiredDatetime' => $payExpiredDate->format('Y-m-d H:i:s'),
            'expPaymentDuration' => 100,
            'customerData' => [
                'customerName' => $member->master_data_fullname,
                'customerInitialEmail' => $member->master_data_email,
                'customerPhoneNumber' => $member->master_data_mobile_phone,
                'remarks' => "Penalty Payment",
                'isCustomerPaymentDefault' => true
            ],
            'productItems' => [
                [
                    'productId' => "",
                    'productImageUrl' => "",
                    'productName' => "",
                    'productQuantity' => 0,
                    'productSinglePrice' => 0,
                    'productTotalPrice' => 0,
                    'productDescription' => null
                ]
            ]
        ];

        $minifiedBody = strtolower(preg_replace('/[\t\r\n\s]+/', '', json_encode($body)));
        $cypertextBody = hash('sha512', $minifiedBody);
        $textToSign = "POST||/api/v2/private/trx/generate-link||" . $MKP_MERCHANT_KEY . "||" . $cypertextBody . "||" . now()->toIso8601String();
        $digitalSignature = hash_hmac('sha512', $textToSign, $MKP_SECRET_KEY);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-MKP-Key' => $MKP_MERCHANT_KEY,
                'X-MKP-Signature' => $digitalSignature,
                'X-MKP-Timestamp' => now()->toIso8601String(),
                'Authorization' => 'Basic ' . $auth,
            ])->post($MKP_BASE_URL . '/api/v2/private/trx/generate-link', $body);

            if ($response->successful()) {
                $result = $response->json();
                $result['status'] = true;
                return $result;
            }

            return [
                'status' => false,
                'errorMessage' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('MKP Payment Error: ' . $e->getMessage());
            return [
                'status' => false,
                'errorMessage' => $e->getMessage()
            ];
        }
    }
}