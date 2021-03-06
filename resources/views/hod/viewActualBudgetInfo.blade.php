@extends('layouts.authorized')

@section('title', 'Department Actual | Budget ')
@section('heading','Final Actual Budget')

@section('content')
    <!-- /.row -->
    <div class="row" xmlns="http://www.w3.org/1999/html">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading ">
                    @if(isset($dpName)) <div class="text-primary">The Department Of {{ $dpName }} |
                        The total Actual Budget amount is {{"K ". number_format($totalBudget, "2", ".", ",") }}.00</div> @endif
                </div>
                <div class="panel-body">
                    <div class="btn-group">
                        <a href="{{ route('/departmentFinalActualBudget') }}" class="btn btn-md btn-link"><i class="fa fa-print fa-fw text-success">
                            </i><span class="text-success">Print Report</span></a>
                    </div>
                    <table class="table-striped responsive-utilities" data-toggle="table" data-show-refresh="false"
                           data-show-toggle="true" data-show-columns="true" data-search="true"
                           data-select-item-name="toolbar1" data-pagination="true" data-sort-name="name"
                           data-sort-order="desc" style="font-size: small">
                        <thead>
                        <tr>
                            <th data-field="departmentName" data-sortable="true">Strategic Directions</th>
                            <th data-field="projectCoordinator" data-sortable="true">Objective</th>
                            <th data-field="startDate" data-sortable="true">Activity Name</th>
                            <th data-field="endDate" data-sortable="true">Item Description</th>
                            <th data-field="quantity" data-sortable="true">Quantity</th>
                            <th data-field="pricePerUnit" data-sortable="true">Price Per Unit</th>
                            <th data-field="cost" data-sortable="true">Cost</th>
                            <th data-field="more" data-sortable="true">more</th>
                        </tr>
                        </thead>
                        @if(isset($records))
                            @foreach( $records as $rcd)

                                <tr>
                                    <td> @if(isset($rcd)) {{ $rcd->strategic_directions->strategy  }} @endif </td>
                                    <td> @if(isset($rcd)) {{ $rcd->objectives->objective  }} @endif </td>
                                    <td> @if(isset($rcd)) {{ $rcd->activityName }} @endif </td>
                                    <td> @if(isset($rcd)) {{ $rcd->estimate->itemDescription }} @endif </td>
                                    <td> @if(isset($rcd)) {{ $rcd->estimate->quantity }} @endif </td>
                                    <td> @if(isset($rcd)) {{"K ".number_format($rcd->estimate->pricePerUnit, "2", ".", ",") }} @endif </td>
                                    <td> @if(isset($rcd)) {{"K ".number_format($rcd->estimate->cost, "2", ".", ",") }}@endif </td>
                                    <td> @if(isset($rcd))
                                            <div class="btn-group">
                                                <a href="{{ route('/moreInfo', ['id' => $rcd->id]) }}"
                                                   class="btn btn-sm btn-link"><i class="fa fa-info-circle fa-fw text-success">
                                                    </i><span class="text-success">More info</span></a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
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
