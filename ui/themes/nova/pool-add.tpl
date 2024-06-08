{include file="sections/header.tpl"}

		<div class="row">
			<div class="col-sm-12 col-md-12">
				<div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>{Lang::T('Add Pool')}</span>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tutorialModal" style="margin-left: auto;">
                        {Lang::T('Need Help? Watch Guide Here')}
                    </button>
                </div>
						<div class="panel-body">

                <form class="form-horizontal" method="post" role="form" action="{$_url}pool/add-post" >
                    <div class="form-group">
							<label class="col-md-2 control-label">{Lang::T('Name Pool')}</label>
						<div class="col-md-6">
							<input type="text" class="form-control" id="name" name="name">
						</div>
                    </div>
                    <div class="form-group">
						<label class="col-md-2 control-label">{Lang::T('Range IP')}</label>
						<div class="col-md-6">
							<input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="ex: 192.168.88.2-192.168.88.254">
						</div>
                    </div>
                    <div class="form-group">
						<label class="col-md-2 control-label"><a href="{$_url}routers/add">{Lang::T('Routers')}</a></label>
						<div class="col-md-6">
							<select id="routers" name="routers" class="form-control select2">
                                {if $_c['radius_enable']}
                                    <option value="radius">Radius</option>
                                {/if}
                                {foreach $r as $rs}
									<option value="{$rs['name']}">{$rs['name']}</option>
                                {/foreach}
                            </select>
						</div>
                        {if $_c['radius_enable']}
                            <p class="help-block col-md-4">For Radius, you need to add <b>Pool Name</b> in Mikrotik manually</p>
                        {/if}
                    </div>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<button class="btn btn-primary" type="submit">{Lang::T('Save Changes')}</button>
							Or <a href="{$_url}pool/list">{Lang::T('Cancel')}</a>
						</div>
					</div>
                </form>

					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="tutorialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tutorialModalLabel">Tutorial Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/M91aZf1wrEw?si=f3cxhNtD6wDbMBwz" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


{include file="sections/footer.tpl"}
