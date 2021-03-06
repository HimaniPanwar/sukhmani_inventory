@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="{{asset('css/style.css')}}">
<script type="text/javascript">
    var count = 1;
    function addfield() {
        $("#table").append('\
                        <tr count='+count+'>\
                            <td>\
                                <select class="form-control category" name="categories[]" required="true">\
                                    <option >Select Category</option>\
                                    @foreach($cats as $cat)\
                                        <option value="{{$cat->id}}">{{$cat->category}}</option>\
                                    @endforeach\
                                </select>\
                            </td>\
                            <td>\
                                <select class="form-control subcategory" name="subcategories[]" required="true">\
                                    <option disabled="true">Select Category</option>\
                                </select>\
                            </td>\
                            <td>\
                                <select class="form-control" name="site[]" required="true">\
                                    @foreach($sites as $site)\
                                        <option value="{{$site->id}}">{{$site->site_name}}</option>\
                                    @endforeach\
                                </select>\
                            </td>\
                            <td>\
                                <select class="form-control costing " name="costing[]" required="true">\
                                    <option disabled="true">Select Sub Category</option>\
                                </select>\
                            </td>\
                            <td>\
                                <input type="number" class="form-control quantity " name="quantity[]" step="0"  required="true">\
                            </td>\
                            <td>\
                                <input type="number" class="form-control amount" name="amount[]" step="0.01" required="true">\
                            </td>\
                            <td>\
                                <input type="text" class="form-control comment" name="comment[]">\
                            </td>\
                            <td>\
                                <input type="date" class="form-control" name="date[]" required="true">\
                            </td>\
                            <td>\
                                <img src="{{ asset('/close.png') }}" width="30px;" class="deleteRow">\
                            </td>\
                        </tr>\
            ')
        count++;
        return;   
    }    
</script>

<div class="container">
  <div class="jumbotron">
    <div class="row">
        @if(session('errors'))
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="alert alert-danger">
                      <strong>Danger!</strong> The following items could not be moved to Site.<br>
                        @foreach(session('errors') as $error)
                            {{$error}}<br>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        @if(session('success'))
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="alert alert-success">
                      <strong>Success!</strong> Items moved to Site.
                    </div>
                </div>
            </div>
        @endif
        <?php session()->forget('errors');session()->forget('success'); ?>
        <h2 id="site_head" style="margin: auto;">To Site</h2>
        <form method="POST" action="{{ route('siteInventory') }}">
            {{ csrf_field() }}
            <div class="col-md-12" style="overflow-x:auto;">
                <table class="table table-hover table-striped table-condensed">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Category</th>
                            <th style="width: 19%;">Sub Category</th>
                            <th style="width: 20%;">To Site</th>
                            <th style="width: 18%;">Rate/Unit(Rs)</th>
                            <th style="width: 18%;">Quantity</th>
                            <th style="width: 20% !important;">Total Cost(Rs)</th>
                            <th style="width: 12%;">Comment</th>
                            <th style="width: 15%;">Date</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="table">
                        <tr count="0">
                            <td>
                                <select class="form-control category" name="categories[]" required="true">
                                    <option disabled="" selected="">Select Category</option>
                                    @foreach($cats as $cat)
                                        <option value="{{$cat->id}}">{{$cat->category}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control subcategory" id="site_subcat" name="subcategories[]" required="true">
                                    <option disabled="true">Select Category</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="site[]" required="true">
                                    @foreach($sites as $site)
                                        <option value="{{$site->id}}">{{$site->site_name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control costing " name="costing[]" required="true">
                                    <option disabled="true">Select Sub Category</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control quantity " name="quantity[]" step="0" required="true">
                            </td>
                            <td>
                                <input type="number" class="form-control amount" name="amount[]" step="0.01" required="true">
                            </td>
                            <td>
                                <input type="text" class="form-control comment" name="comment[]">
                            </td>
                            <td>
                                <input type="date" class="form-control" name="date[]" required="true">
                            </td>
                            <td>
                                <img src="{{ asset('/close.png') }}" width="30px;" class="deleteRow">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <span style="float: right;"><input type="button" class="btn btn-sm btn-primary" onclick="addfield()" value="Add Row"></span>
                <br><br>
                <center><input type="submit" class="form-control btn btn-primary" style="width: 150px; margin-bottom: 40px;" name="submit"></center>
            </div>        
        </form>
    </div>
  </div>
</div>
@endsection
@section('script')
<script type="text/javascript">

        $(document).on("change", ".subcategory", function(){
            subcategory = $(this).val();
            c = $(this).closest('tr').attr('count');
            $.ajax({
                type: 'GET',
                url: 'getsubcategoryrates',
                data: {
                    'subcategory' : subcategory,
                    'c' : c
                },
                success: function(data){
                    console.log(data);
                    if(data[0].length === 0){
                        alert("No Items Present in Warehouse.");
                        return;
                    }
                    $("tr[count='"+data[1]+"']").find(".costing").html('');
                    $("tr[count='"+data[1]+"']").find(".quantity").attr('max',data[0][0].qty);
                    $("tr[count='"+data[1]+"']").find(".quantity").attr('placeholder','max: '+data[0][0].qty);
                    data[0].forEach(function(d){
                        $("tr[count='"+data[1]+"']").find(".costing").append('<option value='+d.rate+'>'+d.rate +'</option>');
                        console.log(d);
                    })
                }
            });
        });

        $(document).on("change", ".costing", function(){
            costing = $(this).val();
            c = $(this).closest('tr').attr('count');
            subcategory = $("tr[count='"+c+"']").find(".subcategory").val();
            $.ajax({
                type: 'GET',
                url: 'getmaxquantity',
                data: {
                    'subcategory' : subcategory,
                    'costing' : costing,
                    'c' : c
                },
                success: function(data){
                    console.log(data);
                    $("tr[count='"+data[1]+"']").find(".quantity").attr('max',data[0]);
                    $("tr[count='"+data[1]+"']").find(".quantity").attr('placeholder','max:'+data[0]);
                }
            });
        });

    
</script>
@endsection