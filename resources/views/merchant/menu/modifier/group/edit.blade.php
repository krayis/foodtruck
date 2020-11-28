@extends('merchant.layouts.admin.layout')

@section('content')

    @include('merchant.menu._partials.navtabs')
    <form action="{{ route('merchant.menu.modifier.group.update', $group->id) }}" method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('merchant.menu.modifier.group.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button type="button" class="btn btn-grey" data-action="delete" data-target="delete-form">
                        Delete
                    </button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group--title  @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control" value="{{ $group->name }}" required>
                @error('name')
                <div class="help-block" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-sm-24 col-md-12">
                <h3>Modifiers</h3>
                @if(count($group->modifiers ))
                    <ul class="group-modifier-list" id="sortable">
                        @foreach($group->modifiers as $key => $modifier)
                            <li data-key="{{$key}}">
                                <div class="inner">
                                    <div class="reorder">
                                        <ion-icon name="reorder"></ion-icon>
                                    </div>
                                    <div>
                                        {{ $modifier->name }}
                                        <input type="hidden" name="modifiers[{{$key}}][id]" value="{{ $modifier->id }}">
                                        <input type="hidden" data-key="{{$key}}" name="modifiers[{{$key}}][sort_order]"
                                               value="{{ $modifier->sort_order }}">
                                    </div>

                                </div>

                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="leading">No modifiers added.</p>
                @endif

            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-sm-24 col-md-16">
                <h3>Rules</h3>
                <p class="leading">Set rules to control how customers select items in this modifier group</p>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="has_custom_range"
                                   value="1" {{ $group->has_custom_range === 1 ? 'checked' : '' }}/>
                            <strong>Require customer to select an modifier?</strong>
                        </label>
                    </div>
                    <div class="form-container"
                         style="display: {{ $group->has_custom_range === 1 ? 'block' : 'none' }}">
                        <p class="label-helper">What is the maximum number of items customers can select?</p>
                        <div class="form-inline">
                            <div class="form-group">
                                <select class="form-control" name="type">
                                    <option
                                        value="EXACT" {{ $group->type === 'EXACT' ? 'selected' : 'none' }}>
                                        Exactly
                                    </option>
                                    <option
                                        value="RANGE" {{ $group->type === 'RANGE' ? 'selected' : 'none' }}>A
                                        Range
                                    </option>
                                </select>
                                @error('type')
                                <div class="help-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="form-group--input-range form-group form-group--label-start"
                                 style="display: {{ $group->type === 'RANGE' ? 'inline-block' : 'none' }}">
                                <span>Between</span>
                            </div>
                            <div class="form-group--input-range form-group"
                                 style="display: {{ $group->type === 'RANGE' ? 'inline-block' : 'none' }}">
                                <input type="text" class="form-control" placeholder="-" name="min_permitted"
                                       value="{{ $group->min_permitted }}">
                            </div>
                            <div class="form-group--input-range form-group form-group--label-after"
                                 style="display: {{ $group->type === 'RANGE' ? 'inline-block' : 'none' }}">
                                <span>and</span>
                            </div>
                            <div class="form-group @error('max_permitted') has-error @enderror">
                                <input type="text" class="form-control" placeholder="-" name="max_permitted"
                                       value="{{ $group->max_permitted }}">
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
                     style="display: {{ $group->has_custom_range === 1 ? 'none' : 'block' }}">
                    <div class="checkbox checkbox-input-inline">
                        <label>
                            <input type="checkbox" name="has_max_permitted"
                                   value="1" {{ $group->has_max_permitted == 1 ? 'checked' : '' }}/>
                            <strong>
                                What's the maximum amount of modifiers a customer can select?
                            </strong>
                            <input type="text" name="max_permitted_per_option" class="form-control form-control--inline"
                                   value="{{ $group->has_custom_range === 1 ? '-' : $group->max_permitted_per_option }}"
                                   placeholder="-" {{ $group->has_custom_range === 1 ? 'disabled' : '' }}>
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
    <form action="{{ route('merchant.menu.modifier.group.destroy', $group->id) }}" style="display: none;" id="delete-form"
          method="POST">
        @csrf
        @method('DELETE')
    </form>

    <script>
        var max = {{ $group->max_permitted }};
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

        $('[name="type"]').on('change', function () {
            var $self = $(this);
            var value = $self.find(':selected').val();
            if (value === 'EXACT') {
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
                $('[name="max_permitted_per_option"]').prop('disabled', true);
            } else {
                $('[name="has_max_permitted"]').prop('checked', true);
                $self.closest('.form-group').find('.form-container').hide();
                $('#modifier-category-max-permitted').show();
                $('[name="max_permitted_per_option"]').prop('disabled', false).val(max);
            }

            $('[name="type"]').trigger('change');
        });

        var $list = $("#sortable");
        $list.sortable({
            handle: '.reorder',
            stop: function (evt, ui) {
                $list.find('> li').each(function (i, item) {
                    var $item = $(item);
                    $item.find('[name="modifiers[' + $item.data('key') + '][sort_order]"]').val(i);
                });

            }
        });
        $('[data-action="delete"]').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete?')) {
                $('#' + $(this).data('target')).submit();
            }
        });
    </script>`
@endsection
