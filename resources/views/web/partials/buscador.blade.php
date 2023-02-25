@inject('precio', 'App\Services\CalcularPrecio')

<form method="get" action="{{ route('formulario_envio') }}" class="form-inline" >
    <div class="input-with-icon origen-input">
        <i class="fas fa-map-marker-alt"></i>
        <input name="localidad_entrega" class="ciudad span4 tr-arial" type="text"  placeholder="Ciudad de Origen">
    </div>
    <div class="input-with-icon destino-input">
        <i class="fas fa-map-marker-alt"></i>
        <input name="localidad_recogida" class="ciudad span4 tr-arial" type="text"  placeholder="Ciudad de Destino">
    </div>
    <div class="input-with-icon peso-input">
        <i class="fas fa-box-open"></i>
        <input name="peso" class="span2 tr-arial" type="text"  placeholder="Peso (kg)">
        <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
    </div>
    <button type="submit" class="btn btn-primary precioButton">
        <p class="precioPortadaLabel">Envía tu <br/> paquete desde</p>
        <span class="precioSpan">{{ $precio->getDefault() }}€</span>
        <i class="fas fa-sync-alt fa-spin precioSpinner"></i>
    </button>
</form>

@push('javascripts-footer')
    <script type="text/javascript" async defer>

        // Automplete de localidades para buscador
        $(function() {
            var localidades = {!! $localidades->map(function($value, $key){ return $value->nombre;}) !!}

            // Para que acepte caracteres acentuados
            var accentMap = {
              "á": "a",
              "é": "e",
              "í": "í",
              "ó": "o",
              "ú": "u"
            };

            var normalize = function( term ) {
              var ret = "";
              for ( var i = 0; i < term.length; i++ ) {
                ret += accentMap[ term.charAt(i) ] || term.charAt(i);
              }
              return ret;
            };


            $(".ciudad").autocomplete({
                source: function( request, response ) {
                  var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
                  response( $.grep(localidades, function( value ) {
                    value = value.label || value.value || value;
                    return matcher.test( value ) || matcher.test( normalize( value ) );
                  }) );
                }
            });

            $('.peso-input > input').on('input', function () {
                var peso = $(this).val();
                if (!isNaN(peso.substr(peso.length - 1))) {
                    //preparamos spinner
                    mostrarSpinner();
                    //realizamos peticion
                    setTimeout(function() {
                        if (peso === $('.peso-input > input').val()) {
                            if(peso.replace(',', '.') <= 20) {
                                if(peso.replace(',', '.') != '') {
                                    $.get("{{ env('APP_URL') }}/api/tstore/v1/precio/peso/" + peso, function (data) {
                                        if (peso === $('.peso-input > input').val()) {
                                            $('.peso-input input').removeClass('has-error').css('outline', 'inherit').attr('placeholder', 'Peso (kg)');
                                            $('.peso-input .form-control-feedback').hide();
                                            if (peso !== '' && peso.replace(',', '.') > 0) {
                                                $('.precioPortadaLabel').html('Envía tu <br/> paquete por', cambiarTextoPrecio(data));
                                            } else {
                                                $('.precioPortadaLabel').html('Envía tu <br/> paquete desde', cambiarTextoPrecio(data));
                                            }
                                        }
                                    });
                                } else {
                                    $('.precioPortadaLabel').html('Envía tu <br/> paquete desde', $('.precioSpan').text(3 + '€', mostrarPrecio()));
                                }
                            } else {
                                $('.peso-input input').addClass('has-error').val('').css('outline', 'none').attr('placeholder', 'Peso máximo 20kg');
                                $('.peso-input .form-control-feedback').show();
                                $('.precioPortadaLabel').html('Envía tu <br/> paquete desde', $('.precioSpan').text(3 + '€', mostrarPrecio()));
                            }
                        }
                    }, 750);
                }
            });

            function mostrarSpinner() {
                $('.precioButton').css({"text-align": "center"});
                $('.precioPortadaLabel').hide();
                $('.precioSpan').attr('style', 'display: none !important');
                $('.precioSpinner').attr('style', 'display:inherit');
            }

            function cambiarTextoPrecio(data) {
                $('.precioSpan').text(data.precio + '€', mostrarPrecio());
            }

            function mostrarPrecio() {
                //ocultamos spinner y mostramos el precio
                console.log("Mostramos el precio");
                $('.precioButton').css({"text-align": "right"});
                $('.precioSpinner').attr('style', 'display:none');
                $('.precioSpan').attr('style', 'display:inherit');
                $('.precioPortadaLabel').show();
            }

        });
    </script>
@endpush