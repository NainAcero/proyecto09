<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Models\Renta;
use DB;

class ProximasRentasController extends Component
{
    use WithPagination;
    public $totalVencidos, $totalProximas;
	public $pagination = 5;

    public function mount() {
        $this->totalVencidos = 0;
        $this->totalProximas = 0;
    }

    public function render() {
        $info = Renta::leftjoin('vehiculos as v','v.id','rentas.vehiculo_id')
            ->leftjoin('cliente_vehiculos as cv','cv.vehiculo_id', 'v.id')
            ->leftjoin('users as u','u.id','cv.user_id')
            ->where('rentas.vehiculo_id', '>', 0)
            ->where('rentas.status','ABIERTO')
            ->select('rentas.*','u.nombre as cliente','v.placa','v.modelo','v.marca','u.telefono',
                DB::RAW("'' as restantemeses "),
                DB::RAW("'' as restantedias "),
                DB::RAW("'' as restantehoras "),
                DB::RAW("'' as restanteyears "),
                DB::RAW("'' as dif "),
                DB::RAW("'' as estado "))
            ->orderBy('rentas.salida','asc')
            ->paginate($this->pagination);

        foreach ($info as $r) {
            $start  =  Carbon::parse($r->acceso);
            $end    =  Carbon::parse($r->salida);

            if(Carbon::now()->greaterThan($end)) { // Si la fecha de hoy es mayor a la fecha de salida
                $r->estado = "VENCIDO";
                $years =0;
                $months =0;
                $days =0;
                $hours =0;
            }
            else {
                $years = $start->diffInYears($end);
                $months = $start->diffInMonths($end);
                $days = $start->diffInDays($end);
                $hours = $start->diffInHours($end);
                $r->estado = ($days > 3 ? "ACTIVO" : "PRÓXIMO");
                $r->dif = Carbon::parse($r->salida)->diffForHumans();
            }

            $r->restantemeses = $months;
            $r->restantedias =  $days;
            $r->restantehoras =  $hours;
            $r->restanteyears =  $years;

            if($days < 1) $this->totalVencidos ++;
            if($days > 0 && $days <=3) $this->totalProximas ++;
        }

        return view('livewire.reportes.component-proximas-rentas', [
            'info' => $info
        ]);
    }

    public function checkOutTicketPension($id) {
        $renta = Renta::findOrFail($id);
        $renta->status ='CERRADO';
        $renta->save();

        $this->emit('msgok','El ticket de pensión se cerró correctamente');
    }

    protected $listeners = [
        'checkOutTicketPension'   => 'checkOutTicketPension'
    ];
}
