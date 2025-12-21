<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionIntent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $plans = [
            'free' => [
                'name' => 'Free',
                'price' => 0,
                'description' => 'Plan dasar untuk mulai membangun ritme kerja.',
                'features' => ['Tanpa voice recorder', 'Kolaborasi terbatas'],
            ],
            'pro' => [
                'name' => 'Pro',
                'price' => 45000,
                'description' => 'Semua fitur produktivitas utama untuk fokus maksimal.',
                'features' => ['Voice recorder aktif', 'Hingga 10 kolaborator'],
            ],
            'plus' => [
                'name' => 'Plus',
                'price' => 65000,
                'description' => 'Fitur premium untuk tim yang berkembang lebih cepat.',
                'features' => ['Semua fitur Pro', 'Hingga 30 kolaborator'],
            ],
        ];

        return view('subscription.plans', compact('user', 'plans'));
    }

    public function updatePlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan' => ['required', 'in:free,pro,plus'],
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Invalid plan selection.');
        }

        $user = $request->user();
        $selected = $request->plan;

        if ($user->subscription_plan === $selected) {
            return back()->with('success', 'You are already on this plan.');
        }

        if (in_array($selected, ['pro', 'plus'], true)) {
            return back()->with('error', 'Upgrade Pro/Plus harus melalui pembayaran.');
        }

        $user->subscription_plan = $selected;
        $user->plan_started_at = now();
        $user->save();

        return back()->with('success', 'Plan updated successfully!');
    }

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan' => ['required', 'in:pro,plus'],
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Invalid plan selection.');
        }

        $user = $request->user();
        $selected = $request->plan;
        $price = $selected === 'plus' ? 65000 : 45000;

        if ($user->subscription_plan === $selected) {
            return back()->with('success', 'You are already on this plan.');
        }

        $intent = SubscriptionIntent::create([
            'user_id' => $user->id,
            'plan' => $selected,
            'price' => $price,
            'status' => 'pending',
            'order_id' => 'ZT-' . $user->id . '-' . Str::uuid(),
            'payment_token' => null,
            'redirect_url' => null,
        ]);

        return redirect()->route('subscription.payment', ['order' => $intent->order_id]);
    }

    public function payment(Request $request, string $order)
    {
        $intent = SubscriptionIntent::where('order_id', $order)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return view('subscription.payment', [
            'intent' => $intent,
        ]);
    }

    public function midtransCallback(Request $request)
    {
        $orderId = $request->input('order_id');
        $status = $request->input('transaction_status');

        $intent = SubscriptionIntent::where('order_id', $orderId)->first();
        if (!$intent) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $intent->payload = $request->all();
        $intent->midtrans_status = $status;

        if (in_array($status, ['capture', 'settlement'], true)) {
            $intent->status = 'paid';
            $intent->paid_at = now();

            $user = $intent->user;
            if ($user) {
                $user->subscription_plan = $intent->plan;
                $user->plan_started_at = now();
                $user->save();
            }
        } elseif ($status === 'pending') {
            $intent->status = 'pending';
        } else {
            $intent->status = 'failed';
        }

        $intent->save();

        return response()->json(['message' => 'OK']);
    }

    public function checkFeature(Request $request)
    {
        return response()->json([
            'plan' => $request->user()->subscription_plan,
        ]);
    }

    public function billing()
    {
        abort(404);
    }

    public function cancel()
    {
        abort(404);
    }
}
