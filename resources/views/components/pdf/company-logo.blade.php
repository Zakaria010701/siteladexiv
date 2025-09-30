{{-- Header --}}
@if(is_null(company()->logo_path))
    <p class="text-uppercase" style="margin-top: 60px; margin-right: 0.5rem; float: right;"><strong>{{ company()->name }}</strong></p>
@else
    <img src="{{getCompanyImageAsBase64()}}" alt="logo" height="80" style="float: right;">
@endif
