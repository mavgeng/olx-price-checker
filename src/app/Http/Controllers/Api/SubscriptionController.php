<?php

namespace App\Http\Controllers\Api;

use App\Actions\Subscription\CreateSubscription;
use App\Http\Requests\StoreSubscriptionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly CreateSubscription $createSubscription,
    ) {}

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $result = $this->createSubscription->execute(
            $request->input('url'),
            $request->input('email'),
        );

        $message = $result->needVerification
            ? 'You need to verify your email address to finish your subscription.'
            : 'Your subscription has been completed.';

        return response()->json([
            'id' => $result->advertSubscriptionId,
            'message' => $message,
        ], ResponseCode::HTTP_CREATED);
    }
}
