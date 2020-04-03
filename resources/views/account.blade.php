@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Account Setup</div>

                <div class="card-body">
                    <button class="btn btn-primary" onclick="addCategory()">Add Category</button>
                    <p> </p>
                    <table class="table table-bordered">
                        <tbody id="categories">

                            @foreach(\App\Category::where('user', auth()->user()->id)->get() as $category)
                            <tr id="cat-{{ $category->id }}">
                                <td><h3 style="padding-top: 0.25em;">{{ $category->name }}</h3></td>
                                <td><button class="btn btn-danger" onclick="delCategory('{{ $category->id }}')" style="display: block;margin: auto;">X</button></td>
                            </tr>
                            @endforeach

                        </tbody>
                      </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

    var categories_json = {!! json_encode(\App\Category::where('user', auth()->user()->id)->get()); !!};
    var categories_array = [];
    categories_json.forEach(obj => {
        categories_array[obj['id']] = obj['name'];
    });

    categories_array[-1] = "Uncategorized";

    function addCategory() {
        Swal.fire({
            title: 'Name of new Category:',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'on'
            },
            showCancelButton: true,
            confirmButtonText: 'Add',
        }).then((result) => {
            if(result.value) {
                $.ajax({
                    url: "{{ route('add_category') }}",
                    type: 'POST',
                    data: {
                        name: result.value,
                        _token: '{{ csrf_token() }}'
                    },
                }).done(function (msg) {
                    if (msg['success']) {
                        var id = msg['id'];

                        var html = `
                            <tr id="cat-${id}">
                                <td><h3 style="padding-top: 0.25em;">${result.value}</h3></td>
                                <td><button class="btn btn-danger" onclick="delCategory('${id}')" style="display: block;margin: auto;">X</button></td>
                            </tr>
                        `;

                        $('#categories').append(html);

                        categories_array[id] = result.value;
                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: msg['msg'],
                            type: 'warning',
                            showCancelButton: false,
                        });
                    }
                });
            }
        });
    }

    async function delCategory(id) {

        var refined_categories = categories_array;

        delete refined_categories[id];

        const { value: new_category } = await Swal.fire({
            title: 'All expenses in this Category will be moved to:',
            input: 'select',
            inputOptions: refined_categories,
            inputPlaceholder: 'Select a Category',
            showCancelButton: true,
            inputValidator: (value) => {
                return new Promise((resolve) => {
                    resolve();
                });
            }
        });

        if (new_category) {
            $.ajax({
                url: "{{ route('del_category') }}",
                type: 'POST',
                data: {
                    id: id,
                    new_category: new_category,
                    _token: '{{ csrf_token() }}'
                },
            }).done(function (msg) {
                if (msg['success']) {
                    delete categories_array[id];
                    $('#cat-' + id).remove();
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: msg['msg'],
                        type: 'warning',
                        showCancelButton: false,
                    });
                }
            });
        }
    }
</script>
@endsection
