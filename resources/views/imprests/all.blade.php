@extends('layouts.authorized')
@section('main_content')

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading"> Imprests</div>

                <div class="panel-body">

                    @if(Auth::user()->accessLevelId=='OT' or Auth::user()->accessLevelId=='HD')
                        <div class="form-group">
                            <a href="{{url('/imprests/new')}}" class="btn btn-link glyphicon-certificate">Create new</a>
                        </div>
                    @endif

                    <table class="table table-striped responsive-utilities" data-toggle="table" data-show-refresh="false"
                           data-show-toggle="true" data-show-columns="true" data-search="true"
                           data-select-item-name="toolbar1" data-pagination="true" data-sort-name="name"
                           data-sort-order="desc" style="font-size: small">

                        <thead>
                        <tr>
                            <!--<th data-field="state" data-checkbox="true">Count</th>-->
                            <th data-field="application stage" data-sortable="true">Budget</th>
                            <th data-field="name" data-sortable="true">Purpose</th>
                            <th data-field="id" data-sortable="true">Applicant</th>
                            <th data-field="position" data-sortable="true">Amount</th>
                            <th data-field="application stage" data-sortable="true">Authorised amount</th>
                            <th data-field="school" data-sortable="true">Authorisation</th>
                            <th data-field="renew by" data-sortable="true">Recommended by</th>
                            <th data-field="contract status" data-sortable="true">Status</th>
                            <th data-field="application stage" data-sortable="true">Created</th>
                            <th data-field="edit" data-sortable="true">Edit/Retire</th>
                        </tr>
                        </thead>

                        @foreach($imprests as $imprest)

                            <?php if ($imprest->authorisedByDean == 1) {
                                $auth = $imprest->dean->lastName." ".$imprest->dean->firstName." as the Dean";

                            } elseif($imprest->authorisedByHead == 1){
                                $auth = $imprest->head->lastName." ".$imprest->head->firstName." as the Head";
                            } else{
                                $auth = 'None';
                            }?>


                            <?php if ($imprest->bursarRecommendation == 1) {
                                $bursar = $imprest->bursar->lastName." ".$imprest->bursar->firstName;
                            } else {
                                $bursar = "Not recommended";
                            } ?>

                            <?php if ($imprest->isRetired == 1) {
                                $retired = "Retired";
                            } else {
                                $retired = "Not retired";
                            } ?>

                            <tr>

                                <!--<td data-field="state" data-checkbox="true">{$imprest->id}}</td>-->
                                <td>{{$imprest->budget->name}}</td>
                                <td>{{$imprest->item->description}}</td>
                                <td>{{$imprest->owner->firstName}} {{$imprest->owner->lastName}}</td>
                                <td>{{$imprest->amountRequested}}</td>
                                <td>{{$imprest->authorisedAmount}}</td>
                                <td @if($auth=='None') style="color: red" @elseif($auth=="The Head") style="color: orange" @else style="color:limegreen;"@endif>{{$auth}}</td>
                                <td @if($bursar=="Not recommended") style="color: red" @else style="color:limegreen" @endif >{{$bursar}}</td>


                                <td @if($retired=="Retired") style="color:red;"{{$retired}} @else style="color:limegreen;" @endif >{{$retired}}</td>
                                <td>{{\Carbon\Carbon::parse($imprest->created_at)->diffForHumans()}}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{url('/imprests/edit/'.$imprest->imprestId)}}"
                                           class="btn btn-sm btn-link glyphicon glyphicon-edit">Edit</a>
                                    </div>
                                    <div class="btn-group">
                                        @if($imprest->authorisedByDean==1 AND $imprest->isRetired==0)
                                        <a href="{{url('/imprests/retirement/form/'.$imprest->imprestId)}}"
                                           class="btn btn-sm btn-link glyphicon glyphicon-flag" >Retire</a>
                                            @endIf
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>


                    <!--/. script-->


                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <!-- /#page-wrapper -->

    <!-- Custom Table JavaScript -->

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
    </script>

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