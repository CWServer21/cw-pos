@extends('backend.layout.main')

@section('content')
<div class="container-fluid">

    <x-success-message key="message" />
    <x-error-message key="not_permitted" />

    <div class="card mt-5">
        <div class="card-header d-flex justify-content-between align-items-start">
            <!-- Left side: Name, Company, Phone -->
            <div>
                <h4 class="mb-1">{{ $lims_supplier_data->name ?? '-' }}</h4>
                <p class="mb-0"><strong>Company:</strong> {{ $lims_supplier_data->company_name ?? '-' }}</p>
                <p class="mb-0"><strong>Phone:</strong> {{ $lims_supplier_data->phone_number ?? '-' }}</p>
            </div>

            <!-- Right side: Address, city & country -->
            <div class="text-end">
                <p class="mb-0">
                    <strong>Address:</strong> {{ $lims_supplier_data->address ?? '-' }}<br>
                    <strong>City:</strong> {{ $lims_supplier_data->city ?? '-' }}<br>
                    <strong>Country:</strong> {{ $lims_supplier_data->country ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#purchase-latest" role="tab" data-toggle="tab">
                {{ __('db.Purchase') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#payment-latest" role="tab" data-toggle="tab">
                {{ __('db.Payment') }}
            </a>
        </li>
    </ul>

    <div class="tab-content mb-5">
        <div role="tabpanel" class="tab-pane fade show active" id="purchase-latest">
            <div class="table-responsive">
                <table id="recent-purchase" class="table w-100">
                    <thead>
                        <tr>
                            <th>{{ __('db.date') }}</th>
                            <th>{{ __('db.reference') }}</th>
                            <th>{{ __('db.Warehouse') }}</th>
                            <th>{{ __('db.Purchase Status') }}</th>
                            <th>{{ __('db.Payment Status') }}</th>
                            <th>{{ __('db.grand total') }}</th>
                            <th>{{ __('db.Paid Amount') }}</th>
                            <th>{{ __('db.Due') }}</th>
                            <th>{{ __('db.Action') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="payment-latest">
            <div class="table-responsive">
                <table id="recent-payment" class="table w-100">
                    <thead>
                        <tr>
                            <th>{{ __('db.date') }}</th>
                            <th>{{ __('db.reference') }}</th>
                            <th>{{ __('db.Amount') }}</th>
                            <th>{{ __('db.Payment Method') }}</th>
                            <th>{{ __('db.Payment At') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="purchase-details" class="modal fade text-left" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="container mt-3 pb-2 border-bottom">
                    <div class="row">
                        <div class="col-md-6 d-print-none">
                            <button id="print-btn" type="button" class="btn btn-default btn-sm">
                                <i class="dripicons-print"></i> {{__('db.Print')}}
                            </button>
                        </div>
                        <div class="col-md-6 d-print-none">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><i class="dripicons-cross"></i></span>
                            </button>
                        </div>
                        <div class="col-md-12 text-center">
                            <h3 class="modal-title">{{$general_setting->site_title}}</h3>
                            <i style="font-size: 15px;">{{__('db.Purchase Details')}}</i>
                        </div>
                    </div>
                </div>

                <div id="purchase-content" class="modal-body"></div>

                <table class="table table-bordered product-purchase-list">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{__('db.Product')}}</th>
                            <th>{{__('db.Batch No')}}</th>
                            <th>Qty</th>
                            <th>{{__('db.Returned')}}</th>
                            <th>{{__('db.Unit Cost')}}</th>
                            <th>{{__('db.Tax')}}</th>
                            <th>{{__('db.Discount')}}</th>
                            <th>{{__('db.Subtotal')}}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div id="purchase-footer" class="modal-body"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#recent-purchase').DataTable({
            ajax: "{{ route('purchase.supplier', $lims_supplier_data->id) }}",
            columns: [
                { data: 'date' },
                { data: 'reference' },
                { data: 'warehouse' },
                { data: 'purchase_status' },
                { data: 'payment_status' },
                { data: 'grand_total' },
                { data: 'paid_amount' },
                { data: 'payment_due' },
                {
                    data: 'id',
                    render: function (data, type, row) {
                        return `
                            <a href="javascript:void(0)" class="btn btn-sm btn-info view-purchase" 
                            data-id="${data}" title="View">
                                <i class="dripicons-preview"></i>
                            </a>
                            <a href="/purchases/${data}/edit" class="btn btn-sm btn-warning" title="Edit">
                                <i class="dripicons-document-edit"></i>
                            </a>
                        `;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[0, 'desc']],
            autoWidth: false,
            responsive: true,
        });

        $('#recent-payment').DataTable({
            ajax: "{{ route('suppliers.payments', $lims_supplier_data->id) }}",
            columns: [
                { data: 'created_at' },
                { data: 'payment_reference' },
                { data: 'amount' },
                { data: 'paying_method' },
                { data: 'payment_at' }
            ],
            order: [[0, 'desc']],
            autoWidth: false,
            responsive: true,
        });

        $("#print-btn").on("click", function(){
            var divContents = document.getElementById("purchase-details").innerHTML;
            var a = window.open('');
            a.document.write('<html>');
            a.document.write('<body><style>body{font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;}.d-print-none{display:none}.text-center{text-align:center}.row{width:100%;margin-right: -15px;margin-left: -15px;}.col-md-12{width:100%;display:block;padding: 5px 15px;}.col-md-6{width: 50%;float:left;padding: 5px 15px;}table{width:100%;margin-top:30px;}th{text-aligh:left;}td{padding:10px}table, th, td{border: 1px solid black; border-collapse: collapse;}</style><style>@media print {.modal-dialog { max-width: 1000px;} }</style>');
            a.document.write(divContents);
            a.document.write('</body></html>');
            a.document.close();
            setTimeout(function(){a.close();},10);
            a.print();
        });

        $(document).on("click", ".view-purchase", function(){
            var table = $('#recent-purchase').DataTable();
            var rowData = table.row($(this).parents('tr')).data();
            purchaseDetails(rowData);
        });
    });

    function purchaseDetails(purchase) {
        var currencyText = purchase.currency ? (purchase.currency.code || purchase.currency.name) : 'N/A';
        var noteText = purchase.note ?? '';

        var htmltext = `
            {{__("db.date")}}: ${purchase.date}<br>
            {{__("db.reference")}}: ${purchase.reference}<br>
            {{__("db.Purchase Status")}}: ${purchase.purchase_status}<br>
            {{__("db.Currency")}}: ${currencyText}<br>
        `;

        if (purchase.document) {
            htmltext += '{{__("db.Attach Document")}}: <a href="documents/purchase/' + purchase.document + '" target="_blank">Download</a><br>';
        }

        htmltext += `
            <br>
            <div class="row">
                <div class="col-md-6">
                    {{__("db.From")}}:<br>
                    ${purchase.supplier_name}<br>
                    ${purchase.supplier_company}<br>
                    ${purchase.supplier_address}
                </div>
                <div class="col-md-6">
                    <div class="float-right">
                        {{__("db.To")}}:<br>
                        ${purchase.warehouse}
                    </div>
                </div>
            </div>
        `;

        // Clear previous table content
        var $table = $("table.product-purchase-list");
        $table.find("tbody").remove();

        // Fetch product purchase items
        $.get('/purchases/product_purchase/' + purchase.id, function(data) {
            var $newBody = $("<tbody>");

            if (data && data[0] && data[0].length > 0) {
                for (var i = 0; i < data[0].length; i++) {
                    var $newRow = $(`
                        <tr>
                            <td>${i + 1}</td>
                            <td>${data[0][i]}</td> <!-- name + code -->
                            <td>${data[7][i]}</td> <!-- batch_no -->
                            <td>${data[1][i]} ${data[2][i]}</td> <!-- qty + unit -->
                            <td>${data[8][i]}</td> <!-- returned qty -->
                            <td>${data[6][i]}</td> <!-- unit cost / total -->
                            <td>${data[3][i]} (${data[4][i]}%)</td> <!-- tax -->
                            <td>${data[5][i]}</td> <!-- discount -->
                            <td>${data[6][i]}</td> <!-- subtotal -->
                        </tr>
                    `);
                    $newBody.append($newRow);
                }
            } else {
                $newBody.append('<tr><td colspan="9" class="text-center">No products found</td></tr>');
            }

            $table.append($newBody);
        }).fail(function() {
            $table.append('<tbody><tr><td colspan="9" class="text-center text-danger">Failed to load products</td></tr></tbody>');
        });

        $('#purchase-content').html(htmltext);
        $('#purchase-footer').html(`<p>{{__("db.Note")}}: ${noteText}</p>`);
        $('#purchase-details').modal('show');
    }
</script>
@endpush
