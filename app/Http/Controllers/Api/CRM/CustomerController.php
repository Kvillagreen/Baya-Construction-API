<?php

namespace App\Http\Controllers\Api\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\StoreCustomerRequest;
use App\Http\Requests\CRM\UpdateCustomerRequest;
use App\Models\CRM\Customer;
use App\Models\CRM\Project;
use App\Support\FrontendApiPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::query()->with('projects.quotations', 'projects.timelines');

        $query->when($request->string('status')->isNotEmpty(), fn ($builder) => $builder->where('status', $request->string('status')));
        $query->when($request->string('region')->isNotEmpty(), fn ($builder) => $builder->where('region', $request->string('region')));
        $query->when($request->string('location')->isNotEmpty(), fn ($builder) => $builder->where('location', $request->string('location')));
        $query->when($request->string('search')->isNotEmpty(), function ($builder) use ($request): void {
            $search = '%'.$request->string('search').'%';
            $builder->where(function ($subQuery) use ($search): void {
                $subQuery
                    ->where('company_name', 'like', $search)
                    ->orWhere('contact_person', 'like', $search)
                    ->orWhere('location', 'like', $search);
            });
        });

        return response()->json([
            'data' => $query->paginate(10)->through(fn (Customer $customer) => FrontendApiPayload::customer($customer)),
        ]);
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'data' => FrontendApiPayload::customer($customer->load('projects.quotations', 'projects.timelines')),
        ]);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $customer = DB::transaction(function () use ($payload) {
            $customer = Customer::query()->create([
                'company_name' => $payload['company_name'],
                'company_address' => $payload['company_address'],
                'region' => $payload['region'],
                'location' => $payload['location'],
                'status' => $payload['status'],
                'contact_person' => $payload['contact_person'],
                'contact_number' => $payload['contact_number'],
            ]);

            Project::query()->create([
                'customer_id' => $customer->id,
                'project_name' => $payload['project_name'],
                'description' => $payload['description'] ?? null,
                'status' => $payload['status'],
                'invoice_number' => $payload['invoice_number'] ?? null,
                'purchase_order_number' => $payload['purchase_order_number'] ?? null,
                'project_costing_amount_encrypted' => (string) $payload['project_costing_amount_encrypted'],
            ]);

            return $customer;
        });

        return response()->json([
            'message' => 'Customer created successfully.',
            'data' => FrontendApiPayload::customer($customer->load('projects.quotations', 'projects.timelines')),
        ], 201);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $payload = $request->validated();

        DB::transaction(function () use ($payload, $customer): void {
            $customer->fill([
                'company_name' => $payload['company_name'] ?? $customer->company_name,
                'company_address' => $payload['company_address'] ?? $customer->company_address,
                'region' => $payload['region'] ?? $customer->region,
                'location' => $payload['location'] ?? $customer->location,
                'status' => $payload['status'] ?? $customer->status,
                'contact_person' => $payload['contact_person'] ?? $customer->contact_person,
                'contact_number' => $payload['contact_number'] ?? $customer->contact_number,
            ])->save();

            $project = $customer->projects()->first() ?? new Project(['customer_id' => $customer->id]);
            $project->fill([
                'project_name' => $payload['project_name'] ?? $project->project_name ?? 'Untitled Project',
                'description' => $payload['description'] ?? $project->description,
                'status' => $payload['status'] ?? $project->status ?? $customer->status,
                'invoice_number' => array_key_exists('invoice_number', $payload) ? $payload['invoice_number'] : $project->invoice_number,
                'purchase_order_number' => array_key_exists('purchase_order_number', $payload) ? $payload['purchase_order_number'] : $project->purchase_order_number,
                'project_costing_amount_encrypted' => array_key_exists('project_costing_amount_encrypted', $payload)
                    ? (string) $payload['project_costing_amount_encrypted']
                    : $project->project_costing_amount_encrypted,
            ]);
            $project->customer_id = $customer->id;
            $project->save();
        });

        return response()->json([
            'message' => 'Customer updated successfully.',
            'data' => FrontendApiPayload::customer($customer->fresh()->load('projects.quotations', 'projects.timelines')),
        ]);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully.',
        ]);
    }
}
