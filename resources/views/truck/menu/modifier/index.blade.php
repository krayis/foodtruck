@include('truck.layouts.admin.head')
@include('truck.layouts.admin.nav')
<div class="container">
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $message)
            @if(Session::has($message))
                <p class="alert alert-{{ $message }}">{{ Session::get($message) }}</p>
            @endif
        @endforeach
    </div>
</div>
@include('truck.menu._partials.navtabs')
<div class="container">
    <div class="page-header">
        <h1 class="inline-block">Modifiers</h1>
        <div class="page-action">
            <a class="btn btn-primary" href="{{ route('truck.menu.modifier.create') }}">Add Modifier</a>
        </div>
    </div>
    <div class="content">
        <table class="table table--modifier">
            <thead>
            <tr>
                <th width="1%"></th>
                <th>Modifier</th>
                <th>Modifier Category</th>
                <th>Min/Max</th>
                <th width="1%">Price</th>
                <th width="1%"></th>
            </tr>
            </thead>
            <tbody>

            @foreach($modifiers as $modifier)
                <tr>
                    <td width="1%" class="pd-0 {{ $modifier->active === 1 ? 'enabled' : 'disabled' }}">
                        <span class="table-icon table-icon-padding">
                            <ion-icon name="{{ $modifier->active === 1 ? 'ios-radio-button-on' : 'ios-pause' }}"></ion-icon>
                        </span>
                    </td>
                    <td>{{$modifier->name}}</td>
                    <td>{{$modifier->category->name}}</td>
                    <td class="white-space--nowrap">
                        @if($modifier->type === 1)
                            {{$modifier->min}}-{{$modifier->max}}
                        @else
                            Single Selection
                        @endif
                    </td>
                    <td align="center">${{$modifier->price}}</td>
                    <td align="right">
                        <form action="{{ route('truck.menu.modifier.destroy', $modifier->id) }}"
                              class="delete" style="display: none;" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>
                        <form action="{{ route('truck.menu.modifier.update',  $modifier->id) }}" class="toggle-state"
                              style="display: none;" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="active" value="{{ $modifier->active === 1 ? 0 : 1 }}"/>
                        </form>
                        <div class="dropdown">
                            <a href="#" data-toggle="dropdown" aria-haspopup="true"
                               aria-expanded="true">
                                <ion-icon name="ios-more" role="img"
                                          class="md hydrated"
                                          aria-label="ellipsis vertical outline"></ion-icon>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right"
                                aria-labelledby="dropdownMenu1">
                                <li><a href="{{route('truck.menu.modifier.edit', $modifier->id)}}">
                                        <ion-icon name="ios-create"></ion-icon>
                                        Edit</a></li>
                                <li><a href="#" class="js-table-toggle-state">
                                        <ion-icon
                                            name="{{ $modifier->active === 1 ? 'pause' : 'ios-radio-button-on' }}"></ion-icon> {{ $modifier->active === 1 ? 'Disable' : 'Enabled' }}
                                    </a></li>
                                <li><a href="#" class="js-table-delete">
                                        <ion-icon name="ios-trash"></ion-icon>
                                        Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $('.js-table-delete').on('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete?')) {
            $(this).closest('td').find('form.delete').submit();
        }
    });
    $('.js-table-toggle-state').on('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to disable?')) {
            $(this).closest('td').find('form.toggle-state').submit();
        }
    });
</script>
@include('truck.layouts.client.footer')
