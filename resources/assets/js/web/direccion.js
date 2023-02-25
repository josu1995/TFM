var cp;
var autocomplete;
var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name'
};

function mapsCallback() {
    initAutocomplete();
}

function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('ciudad')),
        {types: ['(cities)'], componentRestrictions: {country: 'es'}});
    autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
    var place = autocomplete.getPlace();
    var  input = place.address_components[0].long_name+', '+place.address_components[3].long_name;
    $('#ciudad').val(input);
    $('#ciudad_oculto').val(input);
}
