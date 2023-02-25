<div class="horario-punto">
    <table class="table table-condensed table-hover">
        <tbody>
            @foreach($horarios as $dia => $franjas)
                <tr>
                    <td>
                        @if($dia == 1)
                            <strong>Lunes</strong>
                        @elseif($dia == 2)
                            <strong>Martes</strong>
                        @elseif($dia == 3)
                            <strong>Miércoles</strong>
                        @elseif($dia == 4)
                            <strong>Jueves</strong>
                        @elseif($dia == 5)
                            <strong>Viernes</strong>
                        @elseif($dia == 6)
                            <strong>Sábado</strong>
                        @elseif($dia == 7)
                            <strong>Domingo</strong>
                        @else
                            <strong>Todos</strong>
                        @endif
                    </td>
                    @if(count($franjas) >= 2)
                        <td class="mañana">
                            @if($franjas[0]->cerrado == 1)
                                <small class="texto-recogida">Cerrado</small>
                            @else
                                <small>{{ date('H:i', strtotime($franjas[0]->inicio)) }} - {{ date('H:i', strtotime($franjas[0]->fin)) }}</small>
                            @endif
                        </td>
                        <td class="tarde">
                            @if($franjas[1]->cerrado == 1)
                                <small class="texto-recogida">Cerrado</small>
                            @else
                                <small>{{ date('H:i', strtotime($franjas[1]->inicio)) }} - {{ date('H:i', strtotime($franjas[1]->fin)) }}</small>
                            @endif
                        </td>
                    @elseif(count($franjas) == 1)
                        <td colspan="2" class="text-center">
                            @if($franjas[0]->cerrado == 1)
                                <small class="texto-recogida">Cerrado</small>
                            @else
                                <small>{{ date('H:i', strtotime($franjas[0]->inicio)) }} - {{ date('H:i', strtotime($franjas[0]->fin)) }}</small>
                            @endif
                        </td>
                    @else(!count($franjas))
                        <td colspan="2" class="text-center">
                            <small class="texto-recogida">Cerrado</small>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
