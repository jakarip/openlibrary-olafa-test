<!-- BEGIN: Vendor JS-->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.2/b-3.2.2/b-html5-3.2.2/datatables.min.js" integrity="sha384-G21/IAOAMg4/9nYB3ZZGTQFatV1Z0pPjQMCiFDfZybF+BJ1wL/SgwqUWBWYNWfxE" crossorigin="anonymous"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/vendor/libs/validation/jquery.validate.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/vendor/libs/validation/additional-methods.min.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/toastr/toastr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>


<!-- Flat Picker -->
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>

<script language="JavaScript">
const styleSwitcherToggleRed = document.getElementById('style-switcher-toggle-red');
if (styleSwitcherToggleRed) {
    new bootstrap.Tooltip(styleSwitcherToggleRed, {
        fallbackPlacements: ["bottom"],
        popperConfig: {
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, 8], // Adjust the offset values as needed
                    },
                },
                {
                    name: 'preventOverflow',
                    options: {
                        padding: 8, // Adjust the padding value as needed
                    },
                },
                {
                    name: 'flip',
                    options: {
                        padding: 8, // Adjust the padding value as needed
                    },
                },
            ],
        },
    });
}
const urlassets = '{{ asset('') }}';
</script>

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<!-- END: Theme JS-->
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/setting.js') }}"></script>

<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->
