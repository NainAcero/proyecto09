<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\Renta;
use App\Models\Tarifa;
use App\Models\Cajon;

trait GenericTrait {

    public static function DameTotal($fromDate, $tarifaId, $toDate = '') {
        $fraccion = 0;
        $tarifa = Tarifa::where('id', $tarifaId)->first();
        $start  =  Carbon::parse($fromDate);
        $end    =  new \DateTime(Carbon::now());
        if(!$toDate == '')   $end = Carbon::parse($toDate);

        //dif en horas + dif en min y seg
        $tiempo= $start->diffInHours($end) . ':' . $start->diff($end)->format('%I:%S');

        $minutos = $start->diffInMinutes($end);
        $horasCompletas = $start->diffInHours($end);

        if($minutos <= 65){
            $fraccion = $tarifa->costo;
        } else {
            $m = ($minutos % 60);
            if ( in_array($m, range(0,5)) ) { // después de la 1ra hora, se dan 5 minutos de tolerancia al cliente
             //
            }
            else if ( in_array($m, range(6,30)) ){
                $fraccion = ($tarifa->costo / 2);   //después de la 1ra hora, del minuto 6 al 30 se cobra 50% de la tarifa ($6.50)
            }
            else if ( in_array($m, range(31,59)) ){
                $fraccion = $tarifa->costo;    //después de la 1ra hora, del minuto 31-60 se cobra tarifa completa ($13.00)
            }
        }
        //retornamos el total a cobrar
        $total = (($horasCompletas * $tarifa->costo) + $fraccion);
        return $total;
    }

    public function CalculaTiempo($fechaEntrada) {
        $start  =  Carbon::parse($fechaEntrada);
        $end    = new \DateTime(Carbon::now());
        $tiempo = $start->diffInHours($end) . ':' . $start->diff($end)->format('%I:%S');
        return $tiempo;
    }

    //método para salida de vehículos
    public function Salidas($barcode = '') {
        $nuevoTotal = 0;
        //validamos estatus del ticket
        $ticket = Renta::where('barcode', $barcode)->select('*')->first();
        if($ticket) {
            if($ticket->status == 'CERRADO') {
                $this->emit('msg-ops', 'El ticket ya tiene registrada la salida');
                return;
            }
        } else{
            $this->emit('msg-ops', 'El código de ticket no existe en sistema');
            return;
        }
        //obtenemos la tarifa
        $tarifa = Tarifa::where('id', $ticket->tarifa_id)->first();

        //obtenemos el tiempo
        $tiempo = $this->CalculaTiempo($ticket->acceso);

        //obtenemos el total
        $nuevoTotal =  $this->DameTotal($ticket->acceso, $ticket->tarifa_id);

        //guardamos la salida
        $ticket->salida = Carbon::now();
        $ticket->status = 'CERRADO';
        $ticket->total = $nuevoTotal;
        $ticket->hours = $tiempo;
        $ticket->save();

        //ponemos el cajón disponible
        $cajon = Cajon::where('id', $ticket->cajon_id)->first();
        $cajon->status = 'DISPONIBLE';
        $cajon->save();

        //feedback al user
        if($ticket){
            $this->emit('getout-ok', 'Salida Registrada Con Éxito');
            //$this->emit('print', $ticket->id);  //descomentar para imprimir
        } else {
            $this->emit('getout-error', 'No se pudo registrar sa salida :/');
        }
    }
}
