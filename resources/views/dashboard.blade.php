@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="display-4 text-primary fw-bold">Dashboard</h1>
            <p class="lead text-muted">Welcome to the AI-Powered Supply Chain Optimization System</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Product::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Stock Value
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(\App\Models\Product::sum(\DB::raw('current_stock * price')), 0) }} PKR</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Low Stock Items
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Product::where('current_stock', '<', 10)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-danger border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Out of Stock
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Product::where('current_stock', 0)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-light py-3">
                    <h5 class="m-0 font-weight-bold text-primary">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-box"></i> Manage Products
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-success btn-lg">
                        <i class="bi bi-plus-circle"></i> Add New Product
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
