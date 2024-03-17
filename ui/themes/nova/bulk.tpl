{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}settings/send-bulk">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">
                    <div class="btn-group pull-right">
                        <button class="btn btn-primary btn-xs" title="save" type="submit"><span
                                class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                    </div>
                    {Lang::T('Send bulk notification')}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        
                        
                        <div class="form-group">
								<label class="col-md-2 control-label">{$_L['Users']}</label>
								<div class="col-md-6">
									<label><input type="radio" id="all" name="type" value="all"> All users</label>
									<label><input type="radio" id="active" name="type" value="active"> Active Users</label>
								</div>
							</div>
                        
                        
                        
                        
                        	<div class="form-group">
								<label class="col-md-2 control-label">Message Type</label>
								<div class="col-md-6">
									<select id="message" name="message" class="form-control select2">
										<option value=''>Select message</option>
										<option value='downtime_alert'>Downtime Alert</option>
										<option value='discount_alert'>Discount alert</option>
										<option value='custom_message'>Custom Message</option>
									</select>
								</div>
							</div>
                </div>
               
                
              

            <div class="panel-body">
                <div class="form-group">
                    <button class="btn btn-success btn-block waves-effect waves-light"
                        type="submit">{$_L['Save']}</button>
                </div>
            </div>
        </div>
    </div>
</form>
{include file="sections/footer.tpl"}
