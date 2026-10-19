<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EarningsService
{
    /**
     * Calculate tutor's total earnings from all sources.
     * 
     * @param User $tutor
     * @return array
     */
    public function calculateTutorEarnings(User $tutor): array
    {
        // Regular 1-on-1 session earnings (75%)
        $bookingEarnings = Payment::successful()
            ->where('payment_type', 'booking')
            ->whereHas('booking', function ($q) use ($tutor) {
                $q->where('tutor_id', $tutor->id);
            })
            ->sum('tutor_amount');

        // Exam clinic earnings (40%)
        $clinicEarnings = Payment::successful()
            ->where('payment_type', 'clinic')
            ->whereHas('clinic', function ($q) use ($tutor) {
                $q->where('created_by', $tutor->id);
            })
            ->sum('tutor_amount');

        // Total earnings
        $totalEarnings = $bookingEarnings + $clinicEarnings;

        // Count sessions
        $bookingSessionsCount = Payment::successful()
            ->where('payment_type', 'booking')
            ->whereHas('booking', function ($q) use ($tutor) {
                $q->where('tutor_id', $tutor->id);
            })
            ->count();

        $clinicSessionsCount = Payment::successful()
            ->where('payment_type', 'clinic')
            ->whereHas('clinic', function ($q) use ($tutor) {
                $q->where('created_by', $tutor->id);
            })
            ->count();

        // Pending payouts (if implemented)
        $pendingAmount = $totalEarnings; // Could be reduced by amount already paid out

        return [
            'booking_earnings' => $bookingEarnings,
            'clinic_earnings' => $clinicEarnings,
            'total_earnings' => $totalEarnings,
            'pending_amount' => $pendingAmount,
            'booking_sessions_count' => $bookingSessionsCount,
            'clinic_sessions_count' => $clinicSessionsCount,
            'total_sessions_count' => $bookingSessionsCount + $clinicSessionsCount,
        ];
    }

    /**
     * Get recent earnings breakdown (last 30 days).
     * 
     * @param User $tutor
     * @return array
     */
    public function getRecentEarnings(User $tutor): array
    {
        $thirtyDaysAgo = now()->subDays(30);

        $bookingEarnings = Payment::successful()
            ->where('payment_type', 'booking')
            ->where('paid_at', '>=', $thirtyDaysAgo)
            ->whereHas('booking', function ($q) use ($tutor) {
                $q->where('tutor_id', $tutor->id);
            })
            ->sum('tutor_amount');

        $clinicEarnings = Payment::successful()
            ->where('payment_type', 'clinic')
            ->where('paid_at', '>=', $thirtyDaysAgo)
            ->whereHas('clinic', function ($q) use ($tutor) {
                $q->where('created_by', $tutor->id);
            })
            ->sum('tutor_amount');

        return [
            'booking_earnings_30d' => $bookingEarnings,
            'clinic_earnings_30d' => $clinicEarnings,
            'total_earnings_30d' => $bookingEarnings + $clinicEarnings,
        ];
    }

    /**
     * Get earnings by month for charts/graphs.
     * 
     * @param User $tutor
     * @param int $months
     * @return array
     */
    public function getMonthlyEarnings(User $tutor, int $months = 6): array
    {
        $monthlyData = Payment::successful()
            ->where(function ($q) use ($tutor) {
                $q->whereHas('booking', function ($subQ) use ($tutor) {
                    $subQ->where('tutor_id', $tutor->id);
                })->orWhereHas('clinic', function ($subQ) use ($tutor) {
                    $subQ->where('created_by', $tutor->id);
                });
            })
            ->where('paid_at', '>=', now()->subMonths($months))
            ->select(
                DB::raw('DATE_FORMAT(paid_at, "%Y-%m") as month'),
                DB::raw('SUM(tutor_amount) as earnings'),
                'payment_type'
            )
            ->groupBy('month', 'payment_type')
            ->orderBy('month', 'asc')
            ->get();

        return $monthlyData->toArray();
    }

    /**
     * Get platform's total commission from a tutor's earnings.
     * 
     * @param User $tutor
     * @return array
     */
    public function getPlatformCommission(User $tutor): array
    {
        $bookingCommission = Payment::successful()
            ->where('payment_type', 'booking')
            ->whereHas('booking', function ($q) use ($tutor) {
                $q->where('tutor_id', $tutor->id);
            })
            ->sum('platform_amount');

        $clinicCommission = Payment::successful()
            ->where('payment_type', 'clinic')
            ->whereHas('clinic', function ($q) use ($tutor) {
                $q->where('created_by', $tutor->id);
            })
            ->sum('platform_amount');

        return [
            'booking_commission' => $bookingCommission,
            'clinic_commission' => $clinicCommission,
            'total_commission' => $bookingCommission + $clinicCommission,
        ];
    }
}
