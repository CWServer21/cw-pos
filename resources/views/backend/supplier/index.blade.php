@extends('backend.layout.main') @section('content')

<x-success-message key="message" />
<x-error-message key="not_permitted" />

<section>
    <div class="container-fluid">
        @if(in_array("suppliers-add", $all_permission))
        <a href="{{route('supplier.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{__('db.Add Supplier')}}</a>
        <a href="#" data-toggle="modal" data-target="#importSupplier" class="btn btn-primary"><i class="dripicons-copy"></i> {{__('db.Import Supplier')}}</a>
        @endif
    </div>
    <div class="table-responsive">
        <table id="supplier-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{__('db.Image')}}</th>
                    <th>{{__('db.Supplier Details')}}</th>
                    <th>{{__('db.Total Due')}}</th>
                    <th class="not-exported">{{__('db.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_supplier_all as $key => $supplier)
                <?php
                    $returned_amount = DB::table('purchases')
                                    ->join('return_purchases', 'purchases.id', '=', 'return_purchases.purchase_id')
                                    ->where([
                                        ['purchases.supplier_id', $supplier->id],
                                        ['purchases.payment_status', 1]
                                    ])
                                    ->sum('return_purchases.grand_total');
                    $purchaseData = App\Models\Purchase::where([
                                    ['supplier_id', $supplier->id],
                                    ['payment_status', 1]
                                ])
                                ->selectRaw('SUM(grand_total) as grand_total,SUM(paid_amount) as paid_amount')
                                ->first();
                ?>
                <tr data-id="{{$supplier->id}}">
                    <td>{{$key}}</td>
                    @if($supplier->image)
                    <td> <img src="{{url('images/supplier',$supplier->image)}}" height="80" width="80">
                    </td>
                    @else
                    <td><img src="{{url('images/product/zummXD2dvAtI.png')}}" height="80" width="80"></td>
                    @endif
                    <td>
                        {{$supplier->name}}
                        <br>{{$supplier->company_name}}
                        @if($supplier->vat_number)
                        <br>{{$supplier->vat_number}}
                        @endif
                        <br>{{$supplier->email}}
                        <br>{{$supplier->phone_number}}
                        <br>{{$supplier->address}}, {{$supplier->city}}
                            @if($supplier->state){{','.$supplier->state}}@endif
                            @if($supplier->postal_code){{','.$supplier->postal_code}}@endif
                            @if($supplier->country){{','.$supplier->country}}@endif
                    </td>
                    <td>{{number_format($purchaseData->grand_total - $returned_amount - $purchaseData->paid_amount, 2)}}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{__('db.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                @if(in_array("suppliers-index", $all_permission))
                                    <li>
                                        <a href="{{ route('supplier.show', $supplier->id) }}" class="btn btn-link">
                                            <i class="dripicons-preview"></i> {{ __('db.Supplier Details') }}
                                        </a>
                                    </li>
                                @endif
                                @if(in_array("suppliers-edit", $all_permission))
                                <li>
                                	<a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-link"><i class="dripicons-document-edit"></i> {{__('db.edit')}}</a>
                                </li>
                                @endif
                                @if(in_array("supplier-due-report", $all_permission))
                                <li>
                                    {!! Form::open(['route' => 'report.supplierDueByDate', 'method' => 'post', 'id' => 'supplier-due-report-form']) !!}
                                    <input type="hidden" name="start_date" value="{{date('Y-m-d', strtotime('-30 year'))}}" />
                                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                                    <input type="hidden" name="supplier_id" value="{{$supplier->id}}" />
                                    <button type="submit" class="btn btn-link"><i class="dripicons-pulse"></i> {{__('db.Supplier Due Report')}}</button>
                                    {!! Form::close() !!}
                                </li>
                                @endif
                                <li>
                                    <button type="button" data-id="{{$supplier->id}}" class="clear-due btn btn-link" data-toggle="modal" data-target="#clearDueModal" ><i class="dripicons-brush"></i> {{__('db.Clear Due')}}</button>
                                </li>
                                <li class="divider"></li>
                                @if(in_array("suppliers-delete", $all_permission))
                                {{ Form::open(['route' => ['supplier.destroy', $supplier->id], 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{__('db.delete')}}</button>
                                </li>
                                {{ Form::close() }}
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<div id="clearDueModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'supplier.clearDue', 'method' => 'post']) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{__('db.Clear Due')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
            <div class="form-group">
                <input type="hidden" name="supplier_id">
                <label>{{__('db.Amount')}} *</label>
                <input type="number" name="amount" step="any" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{__('db.Note')}}</label>
                <textarea name="note" rows="4" class="form-control"></textarea>
            </div>
            <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary" id="submit-button">
        </div>
        {!! Form::close() !!}
      </div>
    </div>
</div>

<div id="importSupplier" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
	<div role="document" class="modal-dialog">
	  <div class="modal-content">
	  	{!! Form::open(['route' => 'supplier.import', 'method' => 'post', 'files' => true]) !!}
	    <div class="modal-header">
	      <h5 id="exampleModalLabel" class="modal-title">{{__('db.Import Supplier')}}</h5>
	      <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
	    </div>
	    <div class="modal-body">
	      <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
	       <p>{{__('db.The correct column order is')}} (name*, image, company_name*, vat_number, email*, phone_number*, address*, city*,state, postal_code, country) {{__('db.and you must follow this')}}.</p>
           <p>{{__('db.To display Image it must be stored in')}} images/supplier {{__('db.directory')}}</p>
	        <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('db.Upload CSV File')}} *</label>
                        {{Form::file('file', array('class' => 'form-control','required'))}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label> {{__('db.Sample File')}}</label>
                        <a href="sample_file/sample_supplier.csv" class="btn btn-info btn-block btn-md"><i class="dripicons-download"></i> {{__('db.Download')}}</a>
                    </div>
                </div>
            </div>
	        <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary" id="submit-button">
		</div>
		{!! Form::close() !!}
	  </div>
	</div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">

    $("ul#people").siblings('a').attr('aria-expanded','true');
    $("ul#people").addClass("show");
    $("ul#people #supplier-list-menu").addClass("active");

    var all_permission = <?php echo json_encode($all_permission) ?>;
    var supplier_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".clear-due").on("click", function() {
        var id = $(this).data('id').toString();
        $("#clearDueModal input[name='supplier_id']").val(id);
    });

	function confirmDelete() {
	    if (confirm("Are you sure want to delete?")) {
	        return true;
	    }
	    return false;
	}

    $('#supplier-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{__("db.records per page")}}',
             "info":      '<small>{{__("db.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{__("db.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 2, 3]
            },
            {
                'checkboxes': {
                   'selectRow': true
                },
                'targets': 0
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
                customize: function(doc) {
                    for (var i = 1; i < doc.content[1].table.body.length; i++) {
                        if (doc.content[1].table.body[i][0].text.indexOf('<img src=') !== -1) {
                            var imagehtml = doc.content[1].table.body[i][0].text;
                            var regex = /<img.*?src=['"](.*?)['"]/;
                            var src = regex.exec(imagehtml)[1];
                            var tempImage = new Image();
                            tempImage.src = src;
                            var canvas = document.createElement("canvas");
                            canvas.width = tempImage.width;
                            canvas.height = tempImage.height;
                            var ctx = canvas.getContext("2d");
                            ctx.drawImage(tempImage, 0, 0);
                            var imagedata = canvas.toDataURL("image/png");
                            delete doc.content[1].table.body[i][0].text;
                            doc.content[1].table.body[i][0].image = imagedata;
                            doc.content[1].table.body[i][0].fit = [30, 30];
                        }
                    }
                },
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    format: {
                        body: function ( data, row, column, node ) {
                            if (column === 0 && (data.indexOf('<img src=') !== -1)) {
                                var regex = /<img.*?src=['"](.*?)['"]/;
                                data = regex.exec(data)[1];
                            }
                            return data;
                        }
                    }
                },
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    format: {
                        body: function ( data, row, column, node ) {
                            if (column === 0 && (data.indexOf('<img src=') !== -1)) {
                                var regex = /<img.*?src=['"](.*?)['"]/;
                                data = regex.exec(data)[1];
                            }
                            return data;
                        }
                    }
                },
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
            },
            {
                text: '<i title="delete" class="dripicons-cross"></i>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        supplier_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                supplier_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(supplier_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'supplier/deletebyselection',
                                data:{
                                    supplierIdArray: supplier_id
                                },
                                success:function(data){
                                    $(':checkbox:checked').each(function(i) {
                                            if (i) {
                                                 dt.row($(this).closest('tr')).remove().draw(false);
                                            }
                                        });
                                        alert(data);
                                }
                            });
                            // dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!supplier_id.length)
                            alert('No supplier is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );

    if(all_permission.indexOf("suppliers-delete") == -1)
        $('.buttons-delete').addClass('d-none');

</script>
@endpush
