<!-- Checkout Wizard -->
<div id="wizard-checkout" class="bs-stepper wizard-icons wizard-icons-example mb-5">
  <div class="bs-stepper-header m-auto border-0 py-4">
    <div class="step active" >
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
    <div class="step">
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
    <div class="step" data-target="#checkout-confirmation">
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
      <div id="checkout-cart" class="content active ">
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
                  $total_printed += (($item->bdp_discount!=0?$item->bdp_discount:$item->bdp_price)*$item->cd_total_item);
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
                        <input type="number" name="total[{{$item->bd_id}}]" class="updatecart form-control form-control-sm w-px-100 mt-2" value="{{$item->cd_total_item}}" min="1" max="{{$item->bd_stock}}"><br>
                        {{__('common.stock')}} : {{$item->bd_stock}}<br><br>
                        <div class="card-text">{!!($item->bdp_discount!=0)?"<strong style='font-size:12px'> Rp " . number_format($item->bdp_discount, 0, ",", ".")."</strong>":"<strong style='font-size:12px'>Rp " . number_format($item->bdp_price, 0, ",", ".")."</strong>"!!}</div>
                      </div>
                      <div class="col-md-4">
                        <div class="text-md-end">
                          <button class='btn btn-sm btn-primary  mb-2' type='button' onclick='deletecart2({{$item['cd_id_bd']}})'>
                              <span class='d-flex align-items-center justify-content-center text-nowrap'><i class='ti ti-trash-x ti-xs me-2'></i></span>
                          </button>
                          <p class="card-text">{!!($item->bdp_discount!=0)?"<span class='card-text badge bg-label-success' ><strong style='font-size:16px'> Rp " . number_format(($item->cd_total_item*$item->bdp_discount), 0, ",", ".")."</strong></span>":"<span class='card-text badge bg-label-success' ><strong style='font-size:16px'>Rp " . number_format(($item->cd_total_item*$item->bdp_price), 0, ",", ".")."</strong></span>"!!}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endforeach
            </ul>

            <div class="form-group row">
              <label class="col-md-12 col-form-label">
                <button class="btn btn-primary d-grid w-100" type="button" onclick = "updatecart()">
                  <span class="d-flex align-items-center justify-content-center text-nowrap">{{__('common.edit_data')}}</span>
                </button>
              </label>
            </div>
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
                  $total_digital += (($item->bdp_discount!=0?$item->bdp_discount:$item->bdp_price)*$item->cd_total_item);
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
                          <button class='btn btn-sm btn-primary  mb-2' type='button' onclick='deletecart2({{$item['cd_id_bd']}})'>
                              <span class='d-flex align-items-center justify-content-center text-nowrap'><i class='ti ti-trash-x ti-xs me-2'></i></span>
                          </button>
                          <p class="card-text">{!!($item->bdp_discount!=0)?"<span class='card-text badge bg-label-success' ><strong style='font-size:16px'> Rp " . number_format(($item->cd_total_item*$item->bdp_discount), 0, ",", ".")."</strong></span>":"<span class='card-text badge bg-label-success' ><strong style='font-size:16px'>Rp " . number_format(($item->cd_total_item*$item->bdp_price), 0, ",", ".")."</strong></span>"!!}</p>
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

              <h6>{{__('common.price_detail')}}</h6>
              <dl class="row mb-0">
                @if(count($cart_printed)!=0)
                  <dt class="col-6 fw-normal text-heading">{{__('common.printed_book')}}</dt>
                  <dd class="col-6 text-end">{{"Rp " . number_format($total_printed, 0, ",", ".")}}</dd>
                @endif
                @if(count($cart_digital)!=0)
                  <dt class="col-6 fw-normal text-heading">{{__('common.digital_book')}}</dt>
                  <dd class="col-6 text-end">{{"Rp " . number_format($total_digital, 0, ",", ".")}}</dd>
                @endif
              </dl>

              <hr class="mx-n4">
              <dl class="row mb-0">
                <dt class="col-6 text-heading">Total</dt>
                <dd class="col-6 fw-medium text-end text-heading mb-0">{{"Rp " . number_format($total_digital+$total_printed, 0, ",", ".")}}</dd>
              </dl>
            </div>
            <div class="d-grid">
              <button class="btn btn-primary btn-next" onclick="nextstep()">{{(count($cart_printed)!=0?__('common.next'):'Checkout')}}</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<!--/ Checkout Wizard -->
