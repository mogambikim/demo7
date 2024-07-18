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
                <span>{Lang::T('Select Router')}</span>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="color: black;">{Lang::T('Router Name')}</th>
                                <th style="color: black;">{Lang::T('Actions')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$routers item=router}
                            <tr>
                                <td>{$router.name}</td>
                                <td>
                                    <a href="{$_url}router_ip/view-ips?id={$router.id}" class="btn btn-info btn-sm">
                                        <i class="fa fa-eye"></i> {Lang::T('View IPs')}
                                    </a>
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
