@extends('truck.layouts.admin.layout')

@section('content')

    @include('truck.menu._partials.navtabs')
    <form action="{{ route('truck.menu.modifier.group.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('truck.menu.modifier.group.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group--title  @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control" value="{{ old('name') }}" placeholder="Name"
                       required>
                @error('name')
                <div class="help-block" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-sm-24 col-md-16">
                <h3>Rules</h3>
                <p class="leading">Set rules to control how customers select items in this modifier group</p>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="has_custom_range" value="1" {{ old('has_custom_range') !== null ? 'checked' : '' }}/>
                            <strong>Require customer to select an modifier?</strong>
                        </label>
                    </div>
                    <div class="form-container" style="display: {{ old('has_custom_range') ? 'block' : 'none' }}">
                        <p class="label-helper">What is the maximum number of items customers can select?</p>
                        <div class="form-inline">
                            <div class="form-group">
                                <select class="form-control" name="rule_condition">
                                    <option value="exact" {{ old('rule_condition') === 'exact' ? 'selected' : 'none' }}>Exactly</option>
                                    <option value="range" {{ old('rule_condition') === 'range' ? 'selected' : 'none' }}>A Range</option>
                                </select>
                                @error('rule_condition')
                                <div class="help-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="form-group--input-range form-group form-group--label-start"
                                 style="display: {{ old('rule_condition') === 'range' ? 'inline-block' : 'none' }}">
                                <span>Between</span>
                            </div>
                            <div class="form-group--input-range form-group"
                                 style="display: {{ old('rule_condition') === 'range' ? 'inline-block' : 'none' }}">
                                <input type="text" class="form-control" placeholder="-" name="min_permitted"
                                       value="{{ old('min_permitted', 1) }}">
                            </div>
                            <div class="form-group--input-range form-group form-group--label-after"
                                 style="display: {{ old('rule_condition') === 'range' ? 'inline-block' : 'none' }}">
                                <span>and</span>
                            </div>
                            <div class="form-group @error('max_permitted') has-error @enderror">
                                <input type="text" class="form-control" placeholder="-" name="max_permitted"
                                       value="{{ old('max_permitted', 1) }}">
                            </div>
                        </div>
                        <div class="@error('max_permitted') has-error @enderror">
                            @error('max_permitted')
                            <div class="help-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                    </div>


                </div>

                <div class="form-group" id="modifier-category-max-permitted"
                     style="display: {{ old('has_custom_range') === 1 ? 'none' : 'block' }}">
                    <div class="checkbox checkbox-input-inline">
                        <label>
                            <input type="checkbox" name="has_max_permitted"
                                   value="1" {{ old('has_custom_range') === null  ? 'checked' : '' }}/>
                            <strong>
                                What's the maximum amount of modifiers a customer can select?
                            </strong>
                            <input type="text" name="max_permitted_per_option" class="form-control form-control--inline"
                                   value="{{ old('has_custom_range')? '-' : old('max_permitted_per_option', 1) }}"
                                   placeholder="-" {{ old('has_custom_range') ? 'disabled' : '' }}>
                            @error('max_permitted_per_option')
                            <div class="help-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        var max = {{ old('max_permitted', 1) }};
        $('[name="has_max_permitted"]').on('change', function () {
            var $self = $(this);
            var $input = $self.parent().find('input[name="max_permitted_per_option"]');
            if ($self.is(':checked')) {
                $input.prop('disabled', false);
                $input.prop('required', true);
                $input.val(max);
                max = parseInt($input.val());
                $('[name="max_permitted"]').val(max);
                var min = max > 1 ? max - 1 : 1;
                $('[name="min_permitted"]').val(min);
            } else {
                $input.prop('disabled', true);
                $input.prop('required', false);
                $input.val('');
            }
        });

        $('[name="max_permitted"]').on('change', function () {
            var $self = $(this);
            max = parseInt($self.val());
            $('[name="max_permitted_per_option"]').val(max);
        });

        $('[name="max_permitted_per_option"]').on('keyup', function () {
            var $self = $(this);
            max = parseInt($self.val());
            $('[name="max_permitted"]').val(max);
            $('[name="min_permitted"]').val(1);
        });

        $('[name="rule_condition"]').on('change', function () {
            var $self = $(this);
            var value = $self.find(':selected').val();
            if (value === 'exact') {
                $self.closest('.form-container').find('.form-group--input-range').hide();
            } else {
                $self.closest('.form-container').find('.form-group--input-range').show();
            }
        });

        $('[name="has_custom_range"]').on('change', function () {
            var $self = $(this);
            if ($self.is(':checked')) {
                $self.closest('.form-group').find('.form-container').show();
                $('#modifier-category-max-permitted').hide();
                $('[name="has_max_permitted"]').prop('checked', false);
            } else {
                $('[name="has_max_permitted"]').prop('checked', true);
                $self.closest('.form-group').find('.form-container').hide();
                $('#modifier-category-max-permitted').show();
                $('[name="max_permitted_per_option"]').val(max);
            }

            $('[name="rule_condition"]').trigger('change');
        });
        $(document).ready(function() {
            $('[name="has_custom_range"]').trigger('change');
        });
    </script>
@endsection
