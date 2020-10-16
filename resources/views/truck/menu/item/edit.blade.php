@extends('truck.layouts.admin.layout')

@section('content')

    @include('truck.menu._partials.navtabs')

    <form id="item-form" action="{{ route('truck.menu.item.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="meta-header">
            <div class="meta-inner">
                <a href="{{ route('truck.menu.item.index') }}" class="back">
                    <ion-icon name="arrow-back"></ion-icon>
                </a>
                <div class="meta-buttons">
                    <button type="button" class="btn btn-grey" data-action="delete" data-target="delete-form">
                        Delete
                    </button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="form-group form-group--title @error('name') has-error @enderror">
                <input name="name" type="text" class="form-control" placeholder="Item name" value="{{ $item->name }}"
                       required/>
                @error('name')
                <div class="help-block" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
                @enderror
            </div>

        </div>


        <div class="row">
            <div class="col-md-16">
                <div class="form-group @error('category_id') has-error @enderror">
                    <label for="">Category</label>
                    <select name="category_id" class="form-control">
                        @if(count($categories) > 0)
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        @else
                            <option disabled="disabled">Please create a category first.</option>
                        @endif
                    </select>
                    @error('category_id')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('price') has-error @enderror">
                    <label for="">Price</label>
                    <input name="price" type="text" class="form-control" value="{{ $item->price }}" required>
                    @error('price')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('description') has-error @enderror">
                    <label for="">Description</label>
                    <textarea name="description" class="form-control">{{ $item->description }}</textarea>
                    @error('description')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="form-group @error('thumbnail') has-error @enderror">
                    <label for="">Thumbnail</label>
                    <input type="file" name="thumbnail">
                    @if (isset($item->thumbnail))
                        <br/>
                        <img src="{{ asset('storage/' . $item->thumbnail)  }}" class="img-thumbnail"/>
                    @endif
                    @error('thumbnail')
                    <div class="help-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-16">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="out_of_stock" value="1"> <strong>Item out of stock</strong>
                        </label>
                    </div>
                </div>
                <div class="form-group__options">
                    <div class="form-group">
                        <label class="radio-inline">
                            <input type="radio" name="sold_out_option" value="indefinitely"> Sold out indefinitely
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="sold_out_option" value="today"> Sold out today
                        </label>
                    </div>
                    <div class="form-group__leading form-group__leading--indefinitely">The Item will become unavailable until marked as back in stock.</div>
                    <div class="form-group__leading form-group__leading--today">Sold out until tomorrow</div>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-16">
                <h3>Customize Item</h3>
                <div class="form-group">
                    <button type="button" href="" class="btn btn-primary" data-toggle="modal"
                            data-target="#modifier-group-modal">Add Modifier Group
                    </button>
                    <button type="button" class="btn btn-default" data-action="clear-modifier-groups"]>Clear</button>
                </div>
                @if (count($item->modifierGroups))
                    <ul class="group-modifier-list" id="sortable">
                        @foreach($item->modifierGroups as $key => $categories)
                            <li data-key="{{$key}}">
                                <div class="inner">
                                    <div class="reorder">
                                        <ion-icon name="reorder"></ion-icon>
                                    </div>
                                    <div>{{ $categories->name }}</div>
                                    <input type="hidden" name="items[{{$key}}][id]" value="{{ $categories->id }}">
                                    <input type="hidden" data-key="{{$key}}" name="items[{{$key}}][sort_order]"
                                           value="{{ $item->sort_order }}">
                                    <div class="more">
                                        <div class="dropdown">
                                            <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                               aria-expanded="true">
                                                <ion-icon name="more"></ion-icon>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                <li>
                                                    <a href="{{ route('truck.menu.item.edit', $categories->id ) }}">
                                                        Remove
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('truck.menu.modifier.group.edit', $categories->id ) }}">
                                                        Edit Modifier Group
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="modal fade" id="modifier-group-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-lg modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Add Modifier Group</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th width="1%"></th>
                                <th>Modifier Group</th>
                                <th>Contains</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($modifierGroups as $group)
                                <tr>
                                    <td><input type="checkbox" name="modifier_groups[]" value="{{ $group->id }}" @if(in_array($group->id, $item->modifierGroups->pluck('id')->toArray())) checked @endif/></td>
                                    <td>{{ $group->name }}</td>
                                    <td>
                                        @if (count($group->modifiers)> 0)
                                            {{ implode(', ', $group->modifiers->pluck('name')->toArray()) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form action="{{ route('truck.menu.item.destroy',  $item->id) }}" id="form-delete"
          style="display: none;"
          method="POST">
        @csrf
        @method('DELETE')
    </form>

    <script>
        var form = $('#item-form');
        $('[data-action="delete"]').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete?')) {
                $('#' + $(this).data('target')).submit();
            }
        });
        $('[data-action="clear-modifier-groups"]').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to remove all groups?')) {
                $('[name="modifier_groups[]"]').prop('checked', false);
                form.submit();
            }
        });
        var $list = $("#sortable");
        $list.sortable({
            handle: '.reorder',
            stop: function (evt, ui) {
                $list.find('> li').each(function (i, item) {
                    var $item = $(item);
                    $item.find('[name="items[' + $item.data('key') + '][sort_order]"]').val(i);
                });

            }
        });

        $('[name="out_of_stock"]').on('change', function() {
            var $self = $(this);
            var $radio = $self.closest('.row').find('[name="sold_out_option"]');
            if ($self.is(':checked')) {
                $self.closest('.row').find('.form-group__options').show();
            } else {
                $self.closest('.row').find('.form-group__options').hide();
            }
            $('[name="sold_out_option"]:checked').trigger('change');
        });

        $('[name="sold_out_option"]').on('change', function() {
            var $self = $(this);
            $self.closest('.row').find('.form-group__leading').hide();
            console.log('.form-group__leading--' + $self.val());
            if ($self.is(':checked')) {
                $self.closest('.row').find('.form-group__leading--' + $self.val()).show();
            } else {
                $self.closest('.row').find('.form-group__leading').hide();
            }
        });

    </script>

@endsection
