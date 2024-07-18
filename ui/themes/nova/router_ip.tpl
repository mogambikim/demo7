{include file="sections/header.tpl"}

<style>
/* Include your styling here */
.table th, .table td {
    vertical-align: middle !important;
}
.btn-sm {
    padding: .25rem .5rem;
    font-size: .875rem;
    line-height: 1.5;
    border-radius: .2rem;
}
.thead-dark th {
    background-color: #343a40;
    color: #000;
}
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.075);
}
.accordion .card {
    margin-bottom: 1rem;
}
.accordion .card-header {
    cursor: pointer;
    background-color: #007bff;
    color: white;
}
.accordion .card-header h5 {
    margin-bottom: 0;
}
.accordion .card-header .btn {
    width: 100%;
    text-align: left;
    color: white;
}
.accordion .card-header .btn:hover {
    text-decoration: none;
}
.card-body {
    background-color: #f8f9fa;
}
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                <span>{Lang::T('IP Addresses')}</span>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="color: black;">{Lang::T('Address')}</th>
                                <th style="color: black;">{Lang::T('Network')}</th>
                                <th style="color: black;">{Lang::T('Interface')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$ipAddresses item=ip}
                            <tr>
                                <td>{$ip.address}</td>
                                <td>{$ip.network}</td>
                                <td>{$ip.interface}</td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                <form method="post" action="{$_url}router_ip/add-ip">
                    <input type="hidden" name="router_id
