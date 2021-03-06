@extends('layouts.authorized')

@section('title', 'Strategic Directions| Add')
@section('heading','Strategic Directions | Activity based work plan and budget')

@section('content')
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
                <form class="form-horizontal" role="form" style="margin-top: 10px" method="POST"
                      action="{{ url('/addStrategicDirections') }}">
                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="form-group{{ $errors->has('academicYear') ? ' has-error' : '' }}">
                            <label for="academicYear" class="col-md-2">Academic Year</label>
                            <div class="col-md-10">
                                <input id="academicYear" placeholder="2016/2017" type="text" class="form-control" name="academicYear"
                                       value="{{ old('academicYear') }}">
                                @if ($errors->has('academicYear'))
                                    <span class="help-block"><strong>{{ $errors->first('academicYear') }}</strong></span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('strategy') ? ' has-error' : '' }}">
                            <label for="strategy" class="col-md-2">Strategy</label>
                            <div class="col-md-10">
                                <textarea id="strategy" rows="4" class="form-control" name="strategy"></textarea>
                                @if ($errors->has('strategy'))
                                    <span class="help-block"><strong>{{ $errors->first('strategy') }}</strong></span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-9 col-sm-9 col-md-11 col-lg-11">
                                <button type="submit" class="btn btn-sm btn-success pull-right">Add</button>
                            </div>
                            <div class="col-xs-3 col-sm-3 col-md-1 col-lg-1">
                                <button type="reset" class="btn btn-sm btn-danger pull-right">Cancel</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->
                </form>
                <!-- /.form -->
            </div>
            <!-- col-lg-md-sm-xs -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Strategic Directions For The School Of
                    <?php
                         use App\School;
                         use Illuminate\Support\Facades\Auth;

                         $school = School::where('id', Auth::user()->schools_id)->first();
                         echo $school->schoolName;
                    ?>
                    </b>
                </div>
                <div class="panel-body">
                    <table class="table-striped responsive-utilities" data-toggle="table" data-show-refresh="false"
                           data-show-toggle="true" data-show-columns="true" data-search="true"
                           data-select-item-name="toolbar1" data-pagination="true" data-sort-name="name"
                           data-sort-order="desc" style="font-size: small">
                        <thead>
                        <tr>
                            <th data-field="academicYear" data-sortable="true">Academic Year</th>
                            <th data-field="strategicDirectionNumber" data-sortable="true">Strategic Direction No</th>
                            <th data-field="strategicDirectionDescription" data-sortable="true">Strategic Direction Description</th>
                            <th data-field="deleteEdit" data-sortable="true"> Edit</th>
                        </tr>
                        </thead>
                        @foreach($str_dir as $rcd)
                            <tr>
                                <td> @if(isset($rcd)) {{ $rcd->academicYear }} @endif </td>
                                <td> @if(isset($rcd)) {{ $rcd->id }} @endif </td>
                                <td> @if(isset($rcd)) {{ $rcd->strategy }} @endif </td>
                                <td> @if(isset($rcd))
                                        <div class="btn-group">
                                            {{--<a class="btn btn-default btn-xs" href="{{ route('/dltStaff', ['id' => $rcd->id] ) }}"--}}
                                               {{--type="button" name="toggle" title="delete"><i class="glyphicon glyphicon glyphicon-trash"></i>--}}
                                            {{--</a>--}}

                                            <a href="{{ route('/editStaff', ['id' => $rcd->id]) }}" class="btn btn-md btn-link"><i class="fa fa-edit fa-fw"></i></a>
                                        </div>
                                     @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>

                    <script>
                        $(function () {
                            $('#hover, #striped, #condensed').click(function () {
                                var classes = 'table';

                                if ($('#hover').prop('checked')) {
                                    classes += ' table-hover';
                                }
                                if ($('#condensed').prop('checked')) {
                                    classes += ' table-condensed';
                                }
                                $('#table-style').bootstrapTable('destroy')
                                        .bootstrapTable({
                                            classes: classes,
                                            striped: $('#striped').prop('checked')
                                        });
                            });
                        });

                        function rowStyle(row, index) {
                            var classes = ['active', 'success', 'info', 'warning', 'danger'];

                            if (index % 2 === 0 && index / 2 < classes.length) {
                                return {
                                    classes: classes[index / 2]
                                };
                            }
                            return {};
                        }
                    </script> <!--/. script-->

                    <script>
                        function delete_user(user, man) {
                            var xhttp;
                            if (window.XMLHttpRequest) {
                                xhttp = new XMLHttpRequest();
                            } else {
                                // code for IE6, IE5
                                xhttp = new ActiveXObject("Microsoft.XMLHTTP");
                            }
                            if (confirm("Are you sure you want to delete " + user + "?")) {
                                xhttp.open("GET", "{{url('delete_user')}}/" + man, false);
                                xhttp.send();
                                alert(user + " has been deleted!");
                                location.reload();
                            }
                        }
                    </script>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@endsection

@section('scripts')
    @parent
    <!-- Custom Table JavaScript -->
    <script>
        !function ($) {
            $(document).on("click", "ul.nav li.parent > a > span.icon", function () {
                $(this).find('em:first').toggleClass("glyphicon-minus");
            });
            $(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
        }(window.jQuery);

        $(window).on('resize', function () {
            if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
        });
        $(window).on('resize', function () {
            if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
        })
    </script>
    <!-- /.Custom Table JavaScript -->
@endsection