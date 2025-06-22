<!-- Add New Address Modal -->
<div class="modal fade" id="frmBoxAddress" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="address-title mb-2">{{__('common.address_form')}}</h3>
        </div>
        <form id="addressForms" class="row g-3 form-validate" onsubmit="return false">
          <input type="hidden" name="addr_id" id="addr_id">
          <input type="hidden" name="action_form" id="action_form">
          <div class="col-12 ">
            <label class="form-label" for="modalAddressAddress1">{{__('common.address_label')}}</label>
            <input type="text" id="addr_name_place" name="inp[addr_name_place]" required class="form-control required" placeholder="{{__('common.address_label')}}" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="modalAddressFirstName">{{__('common.first_name')}}</label>
            <input type="text" id="addr_firstname" name="inp[addr_firstname]" required class="form-control required" placeholder="{{__('common.first_name')}}" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="modalAddressLastName">{{__('common.last_name')}}</label>
            <input type="text" id="addr_lastname" name="inp[addr_lastname]" required class="form-control required" placeholder="{{__('common.last_name')}}" />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="modalAddressLastName">{{__('common.mobile_phone')}}</label>
            <input type="text" id="addr_phone" name="inp[addr_phone]" required class="form-control required addr_phone" placeholder="{{__('common.mobile_phone')}}" />
          </div>
          <div class="col-12">
            <label class="form-label" for="modalAddressCountry">{{__('common.province')}}</label>
            <select id="addr_province_id" name="inp[addr_province_id]" class="select2 form-select required" data-allow-clear="true">
              <option value="">Select</option>
              @foreach($province as $row)
                <option value="{{$row->province_id}}">{{$row->province}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <label class="form-label" for="modalAddressCountry">{{__('common.city')}}</label>
            <select id="addr_city_id" name="inp[addr_city_id]" class="select2 form-select required" data-allow-clear="true">
            </select>
          </div>
          <div class="col-12">
            <label class="form-label" for="modalAddressCountry">{{__('common.district')}}</label>
            <select id="addr_subdistrict_id" name="inp[addr_subdistrict_id]" class="select2 form-select required" data-allow-clear="true">
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="modalAddressZipCode">{{__('common.postal_code')}}</label>
            <input type="text" id="addr_postcode" name="inp[addr_postcode]" class="form-control required addr_postcode" placeholder="99950" />
          </div>
          <div class="col-12 ">
            <label class="form-label" for="modalAddressAddress1">{{__('common.address_detail')}}</label>
            <input type="text" id="addr_address" name="inp[addr_address]" class="form-control required" placeholder="{{__('common.address_detail')}}" />
          </div>
          <div class="col-12">
            <label class="switch">
              <input type="checkbox" class="switch-input" id="addr_status" name="inp[addr_status]" value="1">
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
              <span class="switch-label">{{__('common.use_as_default_address')}}?</span>
            </label>
          </div>
          <div class="col-12 text-center">
            <button type="button" class="btn btn-primary me-sm-3 me-1" onclick="saveaddress()">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Add New Address Modal -->
