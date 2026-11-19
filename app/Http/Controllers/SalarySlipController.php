<?php

namespace App\Http\Controllers;

use App\Models\SalaryPayment;
use App\Support\AmountInWords;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SalarySlipController extends Controller
{
    /**
     * Download (or preview with ?preview=1) a salary slip as PDF.
     * Employees can only access their own slips; admins can access all.
     */
    public function __invoke(Request $request, SalaryPayment $payment): Response
    {
        $user = Auth::user();

        abort_unless($user->isAdmin() || $payment->user_id === $user->id, 403);

        $payment->load(['user.department']);

        $pdf = Pdf::loadView('pdf.salary-slip', $this->slipData($payment))
            ->setPaper('a4')
            ->setOption('isFontSubsettingEnabled', true); // keeps the file small

        $fileName = 'Salary-Slip-'.Str::slug($payment->user?->name ?? 'employee').'-'.$payment->salary_month->format('M-Y').'.pdf';

        return $request->boolean('preview')
            ? $pdf->stream($fileName)
            : $pdf->download($fileName);
    }

    private function slipData(SalaryPayment $payment): array
    {
        $monthly = (float) $payment->monthly_salary;
        $calculated = (float) $payment->calculated_salary;
        $bonus = (float) $payment->bonus;
        $deduction = (float) $payment->deduction;

        // Loss of pay = part of the monthly salary not earned (unpaid leave / absent days)
        $lossOfPay = round(max(0, $monthly - $calculated), 2);
        $lopDays = max(0, (float) $payment->working_days - (float) $payment->payable_days);

        $earnings = array_filter([
            ['label' => 'Monthly Salary (Gross)', 'amount' => $monthly],
            $bonus > 0 ? ['label' => 'Bonus / Incentive', 'amount' => $bonus] : null,
        ]);

        $deductions = array_filter([
            ['label' => 'Loss of Pay ('.self::days($lopDays).' '.($lopDays == 1 ? 'day' : 'days').')', 'amount' => $lossOfPay],
            $deduction > 0 ? ['label' => 'Other Deduction', 'amount' => $deduction] : null,
        ]);

        $logoPath = public_path(config('company.logo'));
        $logo = is_file($logoPath)
            ? 'data:'.mime_content_type($logoPath).';base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        return [
            'payment' => $payment,
            'employee' => $payment->user,
            'company' => config('company'),
            'logo' => $logo,
            'slipNumber' => 'SLIP-'.$payment->salary_month->format('Ym').'-'.str_pad((string) $payment->id, 4, '0', STR_PAD_LEFT),
            'employeeCode' => 'EMP-'.str_pad((string) $payment->user_id, 4, '0', STR_PAD_LEFT),
            'earnings' => array_values($earnings),
            'deductions' => array_values($deductions),
            'totalEarnings' => $monthly + $bonus,
            'totalDeductions' => $lossOfPay + $deduction,
            'lopDays' => $lopDays,
            'netInWords' => AmountInWords::rupees((float) $payment->net_salary),
        ];
    }

    public static function days(float $value): string
    {
        return rtrim(rtrim(number_format($value, 1), '0'), '.');
    }
}
