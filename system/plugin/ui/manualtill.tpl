{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}plugin/manualtillsave" >
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">M-Pesa</div>
                <div class="panel-body">
                   
                   
                  
				
				<div class="form-group">
    <label class="col-md-2 control-label">Display text</label>
    <div class="col-md-6">
        <textarea class="form-control" name="manualtext" rows="4" cols="50">{$_c['tillmanualtext']}</textarea>
        <small class="form-text text-muted"><font color="red"><b></b></font>This is the manual payment instructions text shown at the bottom of the payment section <font color="green"><b></b></font> </small>
    </div>
</div>


                    <div class="form-group">
                        	<div class="form-group">
                        <label class="col-md-2 control-label">Show/Hide</label>
                        <div class="col-md-6">
                          <select class="form-control" name="show" id="bankstk">
                            <option value="Show"  {if $_c['tillmanualshow'] == 'Show'}selected{/if}>Show</option>
                            <option value="Hide" {if $_c['tillmanualshow'] == 'Hide'}selected{/if}>Hide</option>
                            
                            
                            
                            
                            
                            
                            
                            
                          </select>

                        </div>
                          
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">{$_L['Save']}</button>
                        </div>
                    </div>
                        <pre>/ip hotspot walled-garden
                   add dst-host=safaricom.co.ke
                   add dst-host=*.safaricom.co.ke</pre>
                </div>
            </div>

        </div>
    </div>
</form>
{include file="sections/footer.tpl"}
