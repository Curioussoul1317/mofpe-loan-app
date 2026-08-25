<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $search = request('q');

        $customers = Customer::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere(
                            'email',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'phone',
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->orderBy('name')
            ->paginate(15);

        return CustomerResource::collection(
            $customers
        );
    }

    public function store(
        StoreCustomerRequest $request
    ): CustomerResource {
        $customer = Customer::create(
            $request->validated()
        );

        return new CustomerResource($customer);
    }

    public function show(Customer $customer): CustomerResource {
        return new CustomerResource($customer);
    }
}