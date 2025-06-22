<!-- Checkout Wizard -->
<div id="wizard-checkout" class="bs-stepper wizard-icons wizard-icons-example mb-5">
  <div class="bs-stepper-header m-auto border-0 py-4">
    <div class="step crossed" >
      <button type="button" class="step-trigger">
        <span class="bs-stepper-icon">
          <svg viewBox="0 0 58 54">
            <use xlink:href="{{asset('assets/svg/icons/wizard-checkout-cart.svg#wizardCart')}}"></use>
          </svg>
        </span>
        <span class="bs-stepper-label">{{__('common.cart') }}</span>
      </button>
    </div>
    <div class="line">
      <i class="ti ti-chevron-right"></i>
    </div>

    <div class="step active">
      <button type="button" class="step-trigger">
        <span class="bs-stepper-icon">
          <svg viewBox="0 0 54 54">
            <use xlink:href="{{asset('assets/svg/icons/wizard-checkout-address.svg#wizardCheckoutAddress')}}"></use>
          </svg>
        </span>
        <span class="bs-stepper-label">{{__('common.address') }}</span>
      </button>
    </div>
    <div class="line">
      <i class="ti ti-chevron-right"></i>
    </div>
    <div class="step">
      <button type="button" class="step-trigger">
        <span class="bs-stepper-icon">
          <svg viewBox="0 0 58 54">
            <use xlink:href="{{asset('assets/svg/icons/wizard-checkout-confirmation.svg#wizardConfirm')}}"></use>
          </svg>
        </span>
        <span class="bs-stepper-label">Invoice</span>
      </button>
    </div>
  </div>
  <div class="bs-stepper-content border-top">
    <form id="wizard-checkout-form" onSubmit="return false">
        <input type="hidden" name="cart_wizard" value="address">

      @php
        $total_printed = 0;
      @endphp
      @if(count($cart_printed)!=0)
        @foreach($cart_printed as $item)
          @php
            $dt = asset('storage/'.$item->cd_book_cover);
            $total_printed += (($item->bdp_discount!=0?$item->bdp_discount:$item->bdp_price)*$item->cd_total_item);
          @endphp
        @endforeach
      @endif

      @php
        $total_digital = 0;
      @endphp
      @if(count($cart_printed)!=0)
        @foreach($cart_digital as $item)
          @php
            $dt = asset('storage/'.$item->cd_book_cover);
            $total_digital += (($item->bdp_discount!=0?$item->bdp_discount:$item->bdp_price)*$item->cd_total_item);
          @endphp
        @endforeach
      @endif
      <!-- Address -->
      <div id="checkout-address" class="content active">
        <div class="row">
          <!-- Address left -->
          <div class="col-xl-8  col-xxl-9 mb-3 mb-xl-0">
            <!-- Select address -->
            @if(count($address)!=0) <p>{{__('common.select_your_preferable_address') }}</p>
            @else <p>{{__('common.please_add_the_new_address_through_the_link_below') }}</p>
            @endif
            <div class="row mb-3">
              @foreach($address as $row)
              <div class="col-md mb-md-0 mb-2">
                <div class="form-check custom-option custom-option-basic checked">
                  <label class="form-check-label custom-option-content" for="customRadioAddress1">
                    <input name="addr_id" class="form-check-input checkedAddress" type="radio" value="{{$row->addr_id}}" {{($row->addr_status=='1'?'checked':'')}}>
                    <span class="custom-option-header mb-2">
                      <span class="fw-medium text-heading mb-0">{{$row->addr_name_place}}</span>
                      <!-- <span class="badge bg-label-primary">Home</span> -->
                    </span>
                    <span class="custom-option-body">
                      <small> {{$row->addr_address}}<br>{!!$row->addr_subdistrict_name.', '.$row->addr_city_type.' '.$row->addr_city_name.', Prov. '.$row->addr_province_name."<br>".$row->addr_postcode!!}<br><br>
                      {{$row->addr_phone}}</small><br>
                      <span class="my-2 border-bottom d-block"></span>
                      <span class="d-flex">
                        <a class="me-2" onclick="editaddress('{{$row->addr_id}}')" href="javascript:void(0)">Edit</a> <a onclick="deleteaddress('{{$row->addr_id}}')" href="javascript:void(0)">Remove</a>
                      </span>
                    </span>
                  </label>
                </div>
              </div>
              @endforeach
              <div class="col-md mb-md-0 mb-2">
                <div class="form-check custom-option custom-option-basic checked">
                  <label class="form-check-label custom-option-content" for="customRadioAddress1">
                    <input name="addr_id" class="form-check-input cargoprice" type="radio" value="0">
                    <span class="custom-option-header mb-2">
                      <span class="fw-medium text-heading mb-0">Ambil di Tel-U Openlibrary </span>
                      <!-- <span class="badge bg-label-primary">Home</span> -->
                    </span>
                    <span class="custom-option-body">
                      <small>Gedung Manterawu Lt. 5<br>Jl. Telekomunikasi No.1 , Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung, Prov. Jawa Barat<br>40257<br><br>
                      0812-80000-437 </small><br>
                      <span class="my-2 border-bottom d-block"></span>
                    </span>
                  </label>
                </div>
              </div>
            </div>
            <button type="button" class="btn btn-label-primary mb-4" onclick="addaddress()">{{__('common.add_new_address') }}</button>

            <!-- Choose Delivery -->
            <p>Choose Delivery Speed</p>
            <div class="cargo">
            <div class="row mt-2 cargo">
              @foreach($cargo as $index => $row)
                @foreach($row->costs as $index2 => $row2)
                  <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option custom-option-icon position-relative">
                      <label class="form-check-label custom-option-content" for="customRadioDelivery1">
                        <span class="custom-option-body">
                          <i class="ti ti-truck-delivery ti-lg"></i>
                          <span class="custom-option-title mb-1">{{strtoupper($row->code)}} - {{$row2->service}}<br>{{'('.$row2->description.')'}}</span>

                          <small>{{($row2->cost[0]->etd!=""?$row2->cost[0]->etd.' hari':'-')}}</small><br>
                          <span class="badge bg-label-success">{{"Rp " . number_format($row2->cost[0]->value, 0, ",", ".")}}</span>
                        </span>
                        <input name="shipping_cost" class="form-check-input cargoprice" type="radio" value="{{$row->code.'_'.$row2->service.'_'.$row2->cost[0]->value}}">
                      </label>
                    </div>
                  </div>
                @endforeach
              @endforeach
            </div>
            </div>
          </div>

          <!-- Address right -->
          <div class="col-xl-4 col-xxl-3"  id="price_details">
            <div class="border rounded p-4 pb-3 mb-3">
              <!-- Price Details -->
              <h6>{{__('common.price_detail')}}</h6>
              <dl class="row mb-0">
                @if(count($cart_printed)!=0)
                  <dt class="col-6 fw-normal text-heading">{{__('common.printed_book')}}</dt>
                  <dd class="col-6 text-end">{{"Rp " . number_format($total_printed, 0, ",", ".")}}</dd>
                  <dt class="col-6 fw-normal text-heading">{{__('common.shipping_cost')}}</dt>
                  <dd class="col-6 text-end">{{"Rp -"}}</dd><br><br><br>
                @endif
                @if(count($cart_digital)!=0)
                  <dt class="col-6 fw-normal text-heading">{{__('common.digital_book')}}</dt>
                  <dd class="col-6 text-end">{{"Rp " . number_format($total_digital, 0, ",", ".")}}</dd>
                @endif
              </dl>

              <hr class="mx-n4">
              <dl class="row mb-0">
                <dt class="col-6 text-heading"><strong>Total</strong></dt>
                <dd class="col-6 fw-medium text-end text-heading mb-0"><strong>{{"Rp -"}}</strong></dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<!--/ Checkout Wizard -->
