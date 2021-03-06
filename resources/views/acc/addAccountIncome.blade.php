@extends('layouts.authorized')

@section('title', 'Add | Account Income')
@section('heading','Add An Income To An Account')

@section('content')
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Recently Added Accounts</b></div>
                <div class="panel-body">
                    <table class="table-striped responsive-utilities" data-toggle="table" data-show-refresh="false"
                           data-show-toggle="true" data-show-columns="true" data-search="true"
                           data-select-item-name="toolbar1" data-pagination="true" data-sort-name="name"
                           data-sort-order="desc" style="font-size: small">
                        <thead>
                        <tr>
                            {{--<th data-field="state" data-checkbox="true">Count</th>--}}
                            <th data-field="accountName" data-sortable="true">Account Name</th>
                            <th data-field="amount" data-sortable="true">Total Amount Received</th>
                            <th data-field="createdBy" data-sortable="true">Account Created By</th>
                            <th data-field="addIncome" data-sortable="true">Add Income</th>
                        </tr>
                        </thead>
                        @if(isset($records))
                            @foreach( $records as $rcd)
                                <tr>
                                    {{--<td data-field="state" data-checkbox="true"></td>--}}
                                    <td> @if(isset($rcd)) {{ $rcd->accountName }} @endif </td>
                                    <td> @if(isset($rcd)) {{"K ".number_format( $rcd->calculatedTotal->incomeAcquired , "2", ".", ",")}} @endif </td>
                                    <td> @if(isset($rcd)) {{ $rcd->user->firstName }}
                                        {{ $rcd->user->otherName }} {{ $rcd->user->lastName }}@endif </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('/addAccountIncome', ['id' => $rcd->id]) }}" class="btn btn-sm btn-link">
                                                <i class="fa fa-plus-circle fa-fw text-primary" style="font-size: medium"></i><span class="text-primary">Add Income</span></a>
                                        </div>
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