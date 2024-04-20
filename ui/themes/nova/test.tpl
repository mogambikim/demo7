<div class="row">
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-money"></i> Last 5 Transactions</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>username</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $lastTransactions as $transaction}
                                <tr>
                                    <td>{$transaction.username}</td>
                                    <td>{$transaction.price}</td>
                                    <td>{$transaction.recharged_on}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-users"></i> Users by Service Type</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Service Type</th>
                                <th>Users</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $serviceTypes as $serviceType}
                                <tr>
                                    <td>{$serviceType.service_type}</td>
                                    <td>{$serviceType.count}</td>
                                    <td>{$serviceType.percentage}%</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-money"></i> Last 5 Transactions</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>username</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $lastTransactions as $transaction}
                                <tr>
                                    <td>{$transaction.username}</td>
                                    <td>{$transaction.price}</td>
                                    <td>{$transaction.recharged_on}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-users"></i> Users by Service Type</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Service Type</th>
                                <th>Users</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $serviceTypes as $serviceType}
                                <tr>
                                    <td>{$serviceType.service_type}</td>
                                    <td>{$serviceType.count}</td>
                                    '. $serviceType['percentage']. '%
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>