<!-- field_type_name -->
@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
{{--<input--}}
{{--    type="textarea"--}}
{{--    name="{{ $field['name'] }}"--}}
{{--    value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"--}}
{{--    @include('crud::fields.inc.attributes')--}}
{{--    id="ckeditor1"--}}
{{-->--}}
<textarea class="ckeditor1"
    name="{{ $field['name'] }}"

        @include('crud::fields.inc.attributes', ['default_class' => 'form-control'])
    	>{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}</textarea>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD EXTRA CSS  --}}
    {{-- push things in the after_styles section --}}
    @push('crud_fields_styles')
        <!-- no styles -->
    @endpush

    {{-- FIELD EXTRA JS --}}
    {{-- push things in the after_scripts section --}}
    @push('crud_fields_scripts')
        <!-- no scripts -->
        <script src="{{asset('ckeditor/ckeditor.js')}}">
        </script>
        <script src="{{asset('ckeditor/adapters/jquery.js')}}">
        </script>
        <script>

                CKEDITOR.replaceClass = 'ckeditor1';
        </script>
    @endpush
@endif
