{include file="sections/header.tpl"}

		<div class="row">
			<div class="col-sm-12 col-md-12">
				<div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>{Lang::T('Add Service Plan')}</span>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tutorialModal" style="margin-left: auto;">
                        {Lang::T('Need Help? Watch Guide Here')}
                    </button>
                </div>
						<div class="panel-body">
                        <form class="form-horizontal" method="post" role="form" action="{$_url}services/balance-add-post" >
                            <div class="form-group">
                                <label class="col-md-2 control-label">{Lang::T('Status')}</label>
                                <div class="col-md-10">
                                    <label class="radio-inline warning">
                                        <input type="radio" checked name="enabled" value="1"> Enable
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="enabled" value="0"> Disable
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
																<label class="col-md-2 control-label">{Lang::T('Client Can Purchase')}</label>
																<div class="col-md-10">
																		<label class="radio-inline warning">
																				<input type="radio" checked name="allow_purchase" value="yes"> Yes
																		</label>
																		<label class="radio-inline">
																				<input type="radio" name="allow_purchase" value="no"> No
																		</label>
																</div>
														</div>
                            <div class="form-group">
                                 <label class="col-md-2 control-label">{Lang::T('Plan Name')}</label>
                                <div class="col-md-6">
                                    <input type="text" required class="form-control" id="name" name="name" maxlength="40" placeholder="Topup 100">
                                </div>
                            </div>
                            <div class="form-group">
                               <label class="col-md-2 control-label">{Lang::T('Plan Price')}</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">{$_c['currency_code']}</span>
                                        <input type="number" class="form-control" name="price" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button class="btn btn-success" type="submit">{Lang::T('Save Changes')}</button>
                                    Or <a href="{$_url}services/balance">{Lang::T('Cancel')}</a>
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
