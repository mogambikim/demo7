{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}customers/add-post">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>{Lang::T('Add New Contact')}</span>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tutorialModal" style="margin-left: auto;">
                        {Lang::T('Need Help? Watch Guide Here')}
                    </button>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">{Lang::T('Username')}</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                {if $_c['country_code_phone']!= ''}
                                <span class="input-group-addon" id="basic-addon1">+</span>
                                {else}
                                <span class="input-group-addon" id="basic-addon1"><i
                                        class="glyphicon glyphicon-phone-alt"></i></span>
                                {/if}
                                <input type="text" class="form-control" name="username" required
                                    placeholder="{if $_c['country_code_phone']!= ''}{$_c['country_code_phone']}{/if} {Lang::T('Enter Username')}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">{Lang::T('Full Name')}</label>
                        <div class="col-md-9">
                            <input type="text" required class="form-control" id="fullname" name="fullname">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">{Lang::T('Email')}</label>
                        <div class="col-md-9">
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">{Lang::T('Phone Number')}</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                {if $_c['country_code_phone']!= ''}
                                <span class="input-group-addon" id="basic-addon1">+</span>
                                {else}
                                <span class="input-group-addon" id="basic-addon1"><i
                                        class="glyphicon glyphicon-phone-alt"></i></span>
                                {/if}
                                <input type="text" class="form-control" name="phonenumber"
                                    placeholder="{if $_c['country_code_phone']!= ''}{$_c['country_code_phone']}{/if} {Lang::T('Phone Number')}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                             <label class="col-md-3 control-label">{Lang::T('Password')}</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" autocomplete="off" required id="password"
                                value="{rand(000000,999999)}" name="password" onmouseleave="this.type = 'password'"
                                onmouseenter="this.type = 'text'">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">{Lang::T('PPPOE Password')}</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" id="pppoe_password" name="pppoe_password"
                                value="{$d['pppoe_password']}" onmouseleave="this.type = 'password'"
                                onmouseenter="this.type = 'text'">
                            <span class="help-block">
                                {Lang::T('User Cannot change this, only admin. if it Empty it will use user password')}
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">{Lang::T('Address')}</label>
                        <div class="col-md-9">
                            <textarea name="address" id="address" class="form-control"></textarea>
                        </div>
                    </div>

                    <!-- New IP Address Field -->
<div class="form-group">
    <label class="col-md-3 control-label">IP Address</label>
    <div class="col-md-9">
        <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="Enter IP Address">
    </div>
</div>
					<div class="form-group">
                        <label class="col-md-3 control-label">{Lang::T('Service Type')}</label>
                        <div class="col-md-9">
						<select class="form-control" id="service_type" name="service_type">
						<option value="Hotspot" {if $d['service_type'] eq 'Hotspot'}selected{/if}>Hotspot</option>
						<option value="PPPoE" {if $d['service_type'] eq 'PPPoE'}selected{/if}>PPPoE</option>
                        <option value="Static" {if $d['service_type'] eq 'Static'}selected{/if}>Static</option>
		
                        <option value="Others" {if $d['service_type'] eq 'Others'}selected{/if}>Others</option>
						</select>
						</div>
                    </div>

                    <div class="form-group">
    <label class="col-md-3 control-label">Router</label>
    <div class="col-md-9">
  <select class="form-control" id="router_id" name="router_id">
    <option value="">Select Router (optional)</option>
    {foreach from=$routers item=router}
        <option value="{$router.id}">{$router.name}</option>
    {/foreach}
</select>

    </div>
                    </div>
                </div>
<div class="form-group">
    <label class="col-md-3 control-label">{Lang::T('Search Location')}</label>
    <div class="col-md-9">
        <input type="text" class="form-control" id="search-location" placeholder="Enter location">
        <div id="location-suggestions"></div>
    </div>
</div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">{Lang::T('Coordinates')}</label>
                        <div class="col-md-9">
                            <input name="coordinates" id="coordinates" class="form-control" value=""
                                placeholder="6.465422, 3.406448">
                                <div id="map" style="width: '100%'; height: 200px; min-height: 150px;"></div>
                        </div>
                    </div>
                </div> 
            </div>
        <div class="col-md-6">
<div class="panel panel-primary panel-hovered panel-stacked mb30">
    <div class="panel-heading">{Lang::T('Attributes')}</div>
    <div class="panel-body">
        <div id="custom-fields-container">
            <!-- Customers Attributes will be dynamically added here -->
        </div>
    </div>
    <div class="panel-footer">
        <button class="btn btn-success btn-block" type="button" id="add-custom-field">{Lang::T('Add')}</button>
    </div>
</div>
        </div>
    </div>

    <center>
        <button class="btn btn-primary" type="submit">
            {Lang::T('Save Changes')}
        </button>
        <br><a href="{$_url}customers/list" class="btn btn-link">{Lang::T('Cancel')}</a>
    </center>
</form>
            </div>
        </div>
    </div>
</div>
</div>

{literal}
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    var customFieldsContainer = document.getElementById('custom-fields-container');
    var addCustomFieldButton = document.getElementById('add-custom-field');

    addCustomFieldButton.addEventListener('click', function() {
        var newField = document.createElement('div');
        newField.className = 'form-group';
        newField.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="custom_field_name[]" placeholder="Name">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="custom_field_value[]" placeholder="Value">
                </div>
                <div class="col-md-2">
                    <button type="button" class="remove-custom-field btn btn-danger btn-sm">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
        `;
        customFieldsContainer.appendChild(newField);
    });

    customFieldsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-custom-field')) {
            var fieldContainer = event.target.closest('.form-group');
            fieldContainer.remove();
        }
    });
});
</script>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    var map;
    var marker;

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showDefaultPosition);
        } else {
            showDefaultPosition();
        }
    }

    function showPosition(position) {
        setupMap(position.coords.latitude, position.coords.longitude);
    }

    function showDefaultPosition() {
        setupMap(-1.2921, 36.8219); // Default coordinates for Nairobi, Kenya
    }

    function setupMap(lat, lon) {
        if (!map) {
            map = L.map('map').setView([lat, lon], 13);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/light_all/{z}/{x}/{y}.png', {
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);
        }
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lon]).addTo(map);
        map.setView([lat, lon], 13);
        $('#coordinates').val(lat + ',' + lon);

        // Add the click event listener to update the marker position
        map.on('click', function(e) {
            var coord = e.latlng;
            var lat = coord.lat;
            var lng = coord.lng;
            marker.setLatLng([lat, lng]);
            $('#coordinates').val(lat + ',' + lng);
        });
    }

    window.onload = function() {
        getLocation();
    }

    var searchInput = document.getElementById('search-location');
    var suggestionContainer = document.getElementById('location-suggestions');

    searchInput.addEventListener('input', function() {
        var location = this.value;
        if (location.length >= 3) {
            var url = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(location);
            fetch(url)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    suggestionContainer.innerHTML = '';
                    data.forEach(function(item) {
                        var suggestion = document.createElement('div');
                        suggestion.classList.add('location-suggestion');
                        suggestion.textContent = item.display_name;
                        suggestion.addEventListener('click', function() {
                            searchInput.value = item.display_name;
                            suggestionContainer.innerHTML = '';
                            var lat = parseFloat(item.lat);
                            var lon = parseFloat(item.lon);
                            setupMap(lat, lon);
                        });
                        suggestionContainer.appendChild(suggestion);
                    });
                })
                .catch(function(error) {
                    console.log('Error:', error);
                });
        } else {
            suggestionContainer.innerHTML = '';
        }
    });
</script>
{/literal}


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