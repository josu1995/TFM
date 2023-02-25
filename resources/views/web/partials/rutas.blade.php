<div class="table-responsive table-flow">
    <table class="table table-striped">
        <thead>
        <tr>
            <td><i class="icon-info"></i> Origen</td>
            <td><i class="icon-usuario"></i> Destino</td>
            <td><i class="icon-paquete"></i> Favorito</td>
        </tr>
        </thead>

        @foreach($puntosOrigen as $origen)

            @foreach($puntosDestino as $destino)

                <tr>
                    <td>
                        {{ $origen->nombre }}
                    </td>

                    <td>
                        {{ $destino->nombre }}
                    </td>

                    <td>
                        No
                    </td>
                </tr>

            @endforeach

        @endforeach

    </table>

</div>