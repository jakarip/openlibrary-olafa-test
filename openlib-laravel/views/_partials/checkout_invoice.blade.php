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

    @if(count($cart_printed)!=0)
    <div class="step crossed">
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
    @endif
    <div class="step active" data-target="#checkout-invoice">
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
        <input type="hidden" name="cart_wizard" value="cart">
      <!-- Cart -->
      <div id="checkout-cart" class="content">

      </div>


      <!-- Confirmation -->
      <div id="checkout-invoice" class="content  active">
        <div class="row mb-3">
          <div class="col-12 col-lg-8 mx-auto text-center mb-3">
            <h4 class="mt-2">{{__('common.thank_you_your_order_has_been_received')}} 😇</h4>
            <p>{{__('common.your_order_number_is')}} <a href="javascript:void(0)">#{{$cart->cart_number}}</a></p>
            <p><span class="fw-medium"><i class="ti ti-clock me-1"></i> </span> {{$cart->cart_date}}</p>
          </div>
          <!-- Confirmation details -->
          <div class="col-12">
            <ul class="list-group list-group-horizontal-md">
              @if(count($cart_printed)!=0)
                <li class="list-group-item flex-fill p-12 text-heading">
                  <h6 class="d-flex align-items-center gap-1"><i class="ti ti-credit-card"></i> {{__('common.address')}}</h6>
                    <span class="custom-option-body">
                    @if($cart->cart_cargo_cost!="0")
                      <small>{{$cart->addr_address}}<br>{!!$cart->addr_subdistrict_name.', '.$cart->addr_city_type.' '.$cart->addr_city_name.', Prov. '.$cart->addr_province_name."<br>".$cart->addr_postcode!!}</<br><br>
                      {{$cart->addr_phone}}</small> <br>
                    @else
                      <small>Gedung Manterawu Lt. 5<br>Jl. Telekomunikasi No.1 , Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung, Prov. Jawa Barat<br>40257<br><br>
                      0812-80000-437 </small><br>
                    @endif
                  </span>
                </li>
                <li class="list-group-item flex-fill p-6 text-heading">
                  <h6 class="d-flex align-items-center gap-1"><i class="ti ti-truck-delivery"></i> {{__('common.shipping_method')}}</h6>
                  @if($cart->cart_cargo_cost!="0")
                    {{$cart->cart_cargo_name}}<br />
                    {{$cart->cart_cargo_service_name}}<br />
                  @else
                    Ambil di Tel-U Openlibrary<br />
                  @endif
                </li>
              @else
                <li class="list-group-item flex-fill p-12 text-heading">
                </li>
              @endif
            </ul>
          </div>
        </div>

        <div class="row">
          <!-- Cart left -->
          <div class="col-xl-8 mb-3 mb-xl-0">
            <!-- Shopping bag -->
            @php
              $total_printed = 0;
            @endphp
            @if(count($cart_printed)!=0)
            <h4><div class="card-text badge bg-label-danger" >{{__('common.printed_book')}}</div></h4>
            <ul class="list-group mb-3">
              @foreach($cart_printed as $item)
                @php
                  $dt = asset('storage/'.$item->cd_book_cover);
                  $total_printed += (($item->cd_discount!=0?$item->cd_discount:$item->cd_price)*$item->cd_total_item);
                @endphp
              <li class="list-group-item p-4">
                <div class="d-flex gap-3">
                  <div class="flex-shrink-0 d-flex align-items-center">
                   <img src="{{$dt}}" alt="Card image cap" class="w-px-100"/>
                  </div>
                  <div class="flex-grow-1">
                    <div class="row">
                      <div class="col-md-8">
                        <strong>{{$item->cd_book_title}}</strong><br>
                        {{$item->cd_bd_name}}<br><br>
                        {{$item->cd_total_item}} x
                        <span class="card-text">{!!($item->cd_discount!=0)?"<strong style='font-size:12px'> Rp " . number_format($item->cd_discount, 0, ",", ".")."</strong>":"<strong style='font-size:12px'>Rp " . number_format($item->cd_price, 0, ",", ".")."</strong>"!!}</span>
                      </div>
                      <div class="col-md-4">
                        <div class="text-md-end">
                          <p class="card-text">{!!($item->cd_discount!=0)?"<span class='card-text badge bg-label-success' ><strong style='font-size:16px'> Rp " . number_format(($item->cd_total_item*$item->cd_discount), 0, ",", ".")."</strong></span>":"<span class='card-text badge bg-label-success' ><strong style='font-size:16px'>Rp " . number_format(($item->cd_total_item*$item->cd_price), 0, ",", ".")."</strong></span>"!!}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endforeach
            </ul>
            @endif
            @php
              $total_digital = 0;
            @endphp
            @if(count($cart_digital)!=0)
            <h4><div class="card-text badge bg-label-danger" >{{__('common.digital_book')}}</div></h4>
            <div class="alert alert-success" role="alert">
              <div class="d-flex gap-3">
                <div class="flex-shrink-0">
                  <i class="ti ti-bookmarks ti-sm alert-icon alert-icon-lg"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-medium mb-2">{{__('common.description')}}</div>
                  <ul class="list-unstyled mb-0">
                    <li> <strong>- {{__('common.the_purchased_digital_book_can_only_be_accessed_on_the_telu_press_website_in_the_menu')}} <a href="{{url('digital-book-collection')}}" target="_blank">{{__('common.digital_book_collection')}}</a></strong></li>
                  </ul>
                </div>
              </div>
            </div>
            <button type="button" class="btn-close btn-pinned" data-bs-dismiss="alert" aria-label="Close"></button>
            <ul class="list-group mb-3">
              @foreach($cart_digital as $item)
                @php
                  $dt = asset('storage/'.$item->cd_book_cover);
                  $total_digital += (($item->cd_discount!=0?$item->cd_discount:$item->cd_price)*$item->cd_total_item);
                @endphp
              <li class="list-group-item p-4">
                <div class="d-flex gap-3">
                  <div class="flex-shrink-0 d-flex align-items-center">
                   <img src="{{$dt}}" alt="Card image cap" class="w-px-100"/>
                  </div>
                  <div class="flex-grow-1">
                    <div class="row">
                      <div class="col-md-8">
                        <strong>{{$item->cd_book_title}}</strong><br>
                        {{$item->cd_bd_name}}<br>
                      </div>
                      <div class="col-md-4">
                        <div class="text-md-end">

                          <p class="card-text">{!!($item->cd_discount!=0)?"<span class='card-text badge bg-label-success' ><strong style='font-size:16px'> Rp " . number_format(($item->cd_total_item*$item->cd_discount), 0, ",", ".")."</strong></span>":"<span class='card-text badge bg-label-success' ><strong style='font-size:16px'>Rp " . number_format(($item->cd_total_item*$item->cd_price), 0, ",", ".")."</strong></span>"!!}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endforeach
            </ul>
            @endif
          </div>

          <!-- Cart right -->
          <div class="col-xl-4">
            <div class="border rounded p-4 mb-3 pb-3">
              <!-- Price Details -->
              <h6>{{__('common.our_account_detail')}}</h6>
              <dl class="row mb-0">
                  <dt class="col-6 fw-normal text-heading">{{__('common.bank_name')}}</dt>
                  <dd class="col-6 ">BRI</dd>
                  <dt class="col-6 fw-normal text-heading">{{__('common.account_name')}}</dt>
                  <dd class="col-6 ">Universitas Telkom</dd>
                  <dt class="col-6 fw-normal text-heading">{{__('common.account_number')}}</dt>
                  <dd class="col-6 ">16800 100000 1302</dd>
              </dl>
              <hr class="mx-n4">
              <dl class="row mb-0">
                <dd class="col-12 fw-medium text-heading mb-0"><strong>
                  {{__('common.please_transfer_to_the_account_above_and_upload_the_proof_of_transfer_in_the_menu')}} <a href="{{url('transaction-list')}}" target="_blank">{{__('common.transaction_list')}}</a></strong></dd>
              </dl>
            </div>
            <div class="border rounded p-4 mb-3 pb-3">
              <!-- Price Details -->
              <h6>{{__('common.price_detail')}}</h6>
              <dl class="row mb-0">
                @if(count($cart_printed)!=0)
                  <dt class="col-6 fw-normal text-heading">{{__('common.printed_book')}}</dt>
                  <dd class="col-6 text-end">{{"Rp " . number_format($total_printed, 0, ",", ".")}}</dd>
                  <dt class="col-6 fw-normal text-heading">{{__('common.shipping_cost')}}</dt>
                  <dd class="col-6 text-end">{{"Rp " . number_format($cart->cart_cargo_cost, 0, ",", ".")}}</dd><br><br><br>
                @endif
                @if(count($cart_digital)!=0)
                  <dt class="col-6 fw-normal text-heading">{{__('common.digital_book')}}</dt>
                  <dd class="col-6 text-end">{{"Rp " . number_format($total_digital, 0, ",", ".")}}</dd>
                @endif
              </dl>

              <hr class="mx-n4">
              <dl class="row mb-0">
                <dt class="col-6 text-heading">Total</dt>
                <dd class="col-6 fw-medium text-end text-heading mb-0">{{"Rp " . number_format($total_digital+$total_printed+$cart->cart_cargo_cost, 0, ",", ".")}}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<!--/ Checkout Wizard -->
