@extends('backend.layout.main') @section('content')

<x-error-message key="not_permitted" />

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.Update Customer')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => ['customer.update',$lims_customer_data->id], 'method' => 'put', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="hidden" name="customer_group" value="{{$lims_customer_data->customer_group_id}}">
                                    <label>{{__('db.Customer Group')}} *</strong> </label>
                                    <select required class="form-control selectpicker" name="customer_group_id">
                                        @foreach($lims_customer_group_all as $customer_group)
                                            <option value="{{$customer_group->id}}">{{$customer_group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.name')}} *</strong> </label>
                                    <input type="text" name="customer_name" value="{{$lims_customer_data->name}}" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.Company Name')}} </label>
                                    <input type="text" name="company_name" value="{{$lims_customer_data->company_name}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.Email')}}</label>
                                    <input type="email" name="email" value="{{$lims_customer_data->email}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('db.Type') }}</label>
                                    <select name="type" class="form-control">
                                        @foreach(\App\Enums\CustomerTypeEnum::cases() as $case)
                                            <option value="{{ $case->value }}" {{ $lims_customer_data->type === $case->value ? 'selected' : '' }}>
                                                {{ $case->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.Phone Number')}} *</label>
                                    <input type="text" name="phone_number" required value="{{$lims_customer_data->phone_number}}" class="form-control">
                                    @if($errors->has('phone_number'))
                                   <span>
                                       <strong>{{ $errors->first('phone_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.WhatsApp Number')}}</label>
                                    <input type="text" name="wa_number" class="form-control" value="{{$lims_customer_data->phone_number}}">
                                    @if($errors->has('wa_number'))
                                   <span>
                                       <strong>{{ $errors->first('wa_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.Tax Number')}}</label>
                                    <input type="text" name="tax_no" class="form-control" value="{{$lims_customer_data->tax_no}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.Address')}}</label>
                                    <input type="text" name="address" value="{{$lims_customer_data->address}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.City')}}</label>
                                    <input type="text" name="city" value="{{$lims_customer_data->city}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.State')}}</label>
                                    <input type="text" name="state" value="{{$lims_customer_data->state}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.Postal Code')}}</label>
                                    <input type="text" name="postal_code" value="{{$lims_customer_data->postal_code}}" class="form-control">
                                </div>
                            </div>
                            @if(!$lims_customer_data->user_id)
                            <div class="col-md-6 mt-3">
                                <div class="form-group">
                                    <label>{{__('db.Add User')}}</label>&nbsp;
                                    <input type="checkbox" name="user" value="1" />
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.Country')}}</label>
                                    <input type="text" name="country" value="{{$lims_customer_data->country}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{__('db.Credit Limit')}} <x-info title="Leave it blank for unlimited credit" type="info" /></label>
                                    <input type="number" name="credit_limit" class="form-control" value="{{$lims_customer_data->credit_limit}}" step="any" min="0">
                                </div>
                            </div>
                            @foreach($custom_fields as $field)
                                <?php $field_name = str_replace(' ', '_', strtolower($field->name)); ?>
                                @if(!$field->is_admin || \Auth::user()->role_id == 1)
                                    <div class="{{'col-md-'.$field->grid_value}}">
                                        <div class="form-group">
                                            <label>{{$field->name}}</label>
                                            @if($field->type == 'text')
                                                <input type="text" name="{{$field_name}}" value="{{$lims_customer_data->$field_name}}" class="form-control" @if($field->is_required){{'required'}}@endif>
                                            @elseif($field->type == 'number')
                                                <input type="number" name="{{$field_name}}" value="{{$lims_customer_data->$field_name}}" class="form-control" @if($field->is_required){{'required'}}@endif>
                                            @elseif($field->type == 'textarea')
                                                <textarea rows="5" name="{{$field_name}}" value="{{$lims_customer_data->$field_name}}" class="form-control" @if($field->is_required){{'required'}}@endif></textarea>
                                            @elseif($field->type == 'checkbox')
                                                <br>
                                                <?php
                                                $option_values = explode(",", $field->option_value);
                                                $field_values =  explode(",", $lims_customer_data->$field_name);
                                                ?>
                                                @foreach($option_values as $value)
                                                    <label>
                                                        <input type="checkbox" name="{{$field_name}}[]" value="{{$value}}" @if(in_array($value, $field_values)) checked @endif @if($field->is_required){{'required'}}@endif> {{$value}}
                                                    </label>
                                                    &nbsp;
                                                @endforeach
                                            @elseif($field->type == 'radio_button')
                                                <br>
                                                <?php 
                                                $option_values = explode(",", $field->option_value);
                                                ?>
                                                @foreach($option_values as $value)
                                                    <label class="radio-inline">
                                                        <input type="radio" name="{{$field_name}}" value="{{$value}}" @if($value == $lims_customer_data->$field_name){{'checked'}}@endif @if($field->is_required){{'required'}}@endif> {{$value}}
                                                    </label>
                                                    &nbsp;
                                                @endforeach
                                            @elseif($field->type == 'select')
                                                <?php $option_values = explode(",", $field->option_value); ?>
                                                <select class="form-control" name="{{$field_name}}" @if($field->is_required){{'required'}}@endif>
                                                    @foreach($option_values as $value)
                                                        <option value="{{$value}}" @if($value == $lims_customer_data->$field_name){{'selected'}}@endif>{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($field->type == 'multi_select')
                                                <?php
                                                $option_values = explode(",", $field->option_value);
                                                $field_values =  explode(",", $lims_customer_data->$field_name);
                                                ?>
                                                <select class="form-control" name="{{$field_name}}[]" @if($field->is_required){{'required'}}@endif multiple>
                                                    @foreach($option_values as $value)
                                                        <option value="{{$value}}" @if(in_array($value, $field_values)) selected @endif>{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($field->type == 'date_picker')
                                                <input type="text" name="{{$field_name}}" value="{{$lims_customer_data->$field_name}}" class="form-control date" @if($field->is_required){{'required'}}@endif>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <div class="col-md-6 user-input">
                                <div class="form-group">
                                    <label>{{__('db.UserName')}} *</label>
                                    <input type="text" name="name" class="form-control">
                                    @if($errors->has('name'))
                                   <span>
                                       <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 user-input">
                                <div class="form-group">
                                    <label>{{__('db.Password')}} *</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-3">
                                    <input type="submit" value="{{__('db.submit')}}" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@push('scripts')
<script type="text/javascript">

    $("ul#people").siblings('a').attr('aria-expanded','true');
    $("ul#people").addClass("show");

    $(".user-input").hide();

    $('input[name="user"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('.user-input').show(300);
            $('input[name="name"]').prop('required',true);
            $('input[name="password"]').prop('required',true);
        }
        else{
            $('.user-input').hide(300);
            $('input[name="name"]').prop('required',false);
            $('input[name="password"]').prop('required',false);
        }
    });

    var customer_group = $("input[name='customer_group']").val();
    $('select[name=customer_group_id]').val(customer_group);
</script>
@endpush
